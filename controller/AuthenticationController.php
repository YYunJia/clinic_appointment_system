<?php

require_once __DIR__ . '/../services/AuthService.php';

class AuthenticationController {

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    // Handle patient registration
    public function registerPatient() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../view/register_patient.html');
            exit;
        }

        try {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'phone_number' => trim($_POST['phone_number'] ?? ''),
                'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
                'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
            ];

            $result = $this->authService->registerPatient($data);

            if ($result['success']) {
                header('Location: ../view/login.html?success=1');
                exit;
            } else {
                header('Location: ../view/register_patient.html?error=' . urlencode($result['message']));
                exit;
            }
        } catch (Exception $e) {
            header('Location: ../view/register_patient.html?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function registerDoctor() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../view/register_doctor.html');
            exit;
        }

        try {
            // Collect form data including specialization and license_number
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'phone_number' => trim($_POST['phone_number'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'license_number' => trim($_POST['license_number'] ?? '')
            ];

            // Get admin ID from session (only admin can register doctor)
            session_start();
            $adminId = $_SESSION['user_id'] ?? null;
            if (!$adminId) {
                throw new Exception("Admin login required to register doctor");
            }

            $result = $this->authService->registerDoctor($data, $adminId);

            if ($result['success']) {
                header('Location: ../view/login.html?success=1');
                exit;
            } else {
                header('Location: ../view/register_doctor.html?error=' . urlencode($result['message']));
                exit;
            }
        } catch (Exception $e) {
            header('Location: ../view/register_doctor.html?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // Handle user login
    public function login() {
        session_start(); // ✅ Needed

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../view/login.html');
            exit;
        }

        try {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $result = $this->authService->login($email, $password);

            if ($result['success']) {
                $user = $result['user'];
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];

                switch ($user['user_type']) {
                    case 'patient':
                        header('Location: ../view/HomePage_patient.html?login=success&username='
                                . urlencode($user['username']) . '&role=patient');
                        break;
                    case 'doctor':
                        header('Location: ../view/doctor_dashboard.html?login=success&username='
                                . urlencode($user['username']) . '&role=doctor');
                        break;
                    case 'admin':
                        header('Location: ../view/admin_dashboard.html?login=success&username='
                                . urlencode($user['username']) . '&role=admin');
                        break;
                    default:
                        header('Location: ../view/homepage.html');
                }
                exit;
            } else {
                header('Location: ../view/login.html?error=' . urlencode($result['message']));
                exit;
            }
        } catch (Exception $e) {
            error_log("Login failed: " . $e->getMessage());
            header('Location: ../view/login.html?error=server_error');
            exit;
        }
    }

    // Handle user logout
    public function logout() {
        $this->authService->logout();
        header('Location: ../view/HomePage_patient.html?logout=1');
        exit;
    }
}

?>
