<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../observers/UserEventObserver.php';

class AuthService {

    private $userModel;
    private $eventManager;
    private $sessionTimeout = 3600; // 1 hour

    public function __construct() {
        $this->userModel = new User();
        $this->eventManager = new UserEventManager();

        // Attach observers
        $this->eventManager->attach(new EmailNotificationObserver());
        $this->eventManager->attach(new AuditLogObserver());
        $this->eventManager->attach(new SecurityAlertObserver());
    }

    /** Generate unique user ID */
    public function generateUserId($role) {
        $prefix = ($role === 'patient') ? 'U' : (($role === 'doctor') ? 'D' : 'X');
        $lastUser = $this->userModel->getLastUserByType($role);

        if ($lastUser && preg_match('/[UD](\d+)/', $lastUser['user_id'], $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /** Hash password */
    private function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    /** Verify password */
    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /** Start session */
    private function startSession(User $user) {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->getUserId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['user_type'] = $user->getUserType();
        $_SESSION['last_activity'] = time();
    }

    /** Register patient */
    public function registerPatient($data) {
        try {
            // Required fields
            $required = [
                'username', 'email', 'password', 'name', 'phone_number',
                'emergency_contact_name', 'emergency_contact_phone'
            ];
            foreach ($required as $field) {
                if (empty($data[$field]))
                    throw new Exception("Field '$field' is required");
            }

            // Validation
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email format");
            if (strlen($data['password']) < 8)
                throw new Exception("Password must be at least 8 characters");
            if (!preg_match('/[A-Z]/', $data['password']) ||
                    !preg_match('/[a-z]/', $data['password']) ||
                    !preg_match('/[0-9]/', $data['password']))
                throw new Exception("Password must include uppercase, lowercase, and number");
            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username']))
                throw new Exception("Invalid username");
            if ($this->userModel->emailExists($data['email']))
                throw new Exception("Email already exists");
            if ($this->userModel->usernameExists($data['username']))
                throw new Exception("Username already exists");

            // Hash password
            $data['password_hash'] = $this->hashPassword($data['password']);
            unset($data['password']);
            $data['user_type'] = 'patient';

            // **Generate user_id BEFORE creating**
            $data['user_id'] = $this->generateUserId('patient');

            // Create user
            $user = new User($data);
            $user->create(); // now user_id is set, no SQL error
            // Insert into patients table
            $conn = getDBConnection();
            $stmt = $conn->prepare("
            INSERT INTO patients (patient_id, user_id, emergency_contact_name, emergency_contact_phone)
            VALUES (:patient_id, :user_id, :emergency_contact_name, :emergency_contact_phone)
        ");
            $stmt->bindParam(':patient_id', $data['user_id']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':emergency_contact_name', $data['emergency_contact_name']);
            $stmt->bindParam(':emergency_contact_phone', $data['emergency_contact_phone']);
            $stmt->execute();

            // Trigger event
            $this->eventManager->triggerEvent('patient_registered', $user->toArray());

            return ['success' => true, 'user_id' => $data['user_id'], 'message' => 'Patient registered successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /** Register doctor (admin only) */
    public function registerDoctor($data, $adminId) {
        try {
            // Check admin access
            $admin = $this->userModel->findById($adminId);
            if (!$admin || $admin->getUserType() !== 'admin')
                throw new Exception("Unauthorized: Admin access required");

            // Required fields
            $required = ['username', 'email', 'password', 'name', 'phone_number', 'specialization', 'license_number'];
            foreach ($required as $field) {
                if (empty($data[$field]))
                    throw new Exception("Field '$field' is required");
            }

            // Validation
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email format");
            if (strlen($data['password']) < 8)
                throw new Exception("Password too short");
            if (!preg_match('/[A-Z]/', $data['password']) ||
                    !preg_match('/[a-z]/', $data['password']) ||
                    !preg_match('/[0-9]/', $data['password']))
                throw new Exception("Password must include uppercase, lowercase, and number");
            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username']))
                throw new Exception("Invalid username");
            if ($this->userModel->emailExists($data['email']))
                throw new Exception("Email exists");
            if ($this->userModel->usernameExists($data['username']))
                throw new Exception("Username exists");

            // Hash password
            $data['password_hash'] = $this->hashPassword($data['password']);
            unset($data['password']);
            $data['user_type'] = 'doctor';

            // **Generate user_id BEFORE creating**
            $data['user_id'] = $this->generateUserId('doctor');

            // Create user
            $user = new User($data);
            $user->create();

            // Insert into doctors table
            $conn = getDBConnection();
            $stmt = $conn->prepare("
            INSERT INTO doctors (doctor_id, user_id, specialization, license_number)
            VALUES (:doctor_id, :user_id, :specialization, :license_number)
        ");
            $stmt->bindParam(':doctor_id', $data['user_id']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':specialization', $data['specialization']);
            $stmt->bindParam(':license_number', $data['license_number']);
            $stmt->execute();

            // Trigger event
            $this->eventManager->triggerEvent('doctor_registered', $user->toArray());

            return ['success' => true, 'user_id' => $data['user_id'], 'message' => 'Doctor registered successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /** Login */
    public function login($email, $password) {
        try {
            $user = $this->userModel->findByEmail($email);
            if (!$user || !$this->verifyPassword($password, $user->getPasswordHash()))
                throw new Exception("Invalid email or password");

            $this->startSession($user);

            $this->eventManager->triggerEvent('user_login', $user->toArray());
            return ['success' => true, 'user' => $user->toArray(), 'message' => 'Login successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /** Logout */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /** Check login */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        return isset($_SESSION['user_id']) &&
                isset($_SESSION['last_activity']) &&
                (time() - $_SESSION['last_activity']) < $this->sessionTimeout;
    }

    /** Current user */
    public function getCurrentUser() {
        if (!$this->isLoggedIn())
            return null;
        return $this->userModel->findById($_SESSION['user_id']);
    }

    /** Check user type */
    public function hasUserType($type) {
        $user = $this->getCurrentUser();
        return $user && $user->getUserType() === $type;
    }

    public function requireUserType($type) {
        if (!$this->hasUserType($type))
            throw new Exception("Access denied: $type required");
    }

    /** Update profile */
    public function updateProfile($userId, $data) {
        try {
            $user = $this->userModel->findById($userId);
            if (!$user)
                throw new Exception("User not found");

            if (isset($data['password'])) {
                $data['password_hash'] = $this->hashPassword($data['password']);
                unset($data['password']);
            }
            unset($data['user_id']); // prevent changing user_id
            $result = $user->update($data);
            if ($result)
                return ['success' => true, 'message' => 'Profile updated successfully'];
            throw new Exception("Failed to update profile");
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /** Change password */
    public function changePassword($userId, $current, $new) {
        try {
            $user = $this->userModel->findById($userId);
            if (!$user)
                throw new Exception("User not found");
            if (!$this->verifyPassword($current, $user->getPasswordHash()))
                throw new Exception("Current password incorrect");

            $user->update(['password_hash' => $this->hashPassword($new)]);
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>

