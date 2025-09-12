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
                'phone_number' => trim($_POST['phone_number'] ?? ''), // <-- fixed
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../view/login.html');
        exit;
    }

    try {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            $userType = $result['user']['user_type'];

            // Redirect based on user_type
            switch ($userType) {
                case 'patient':
                    header('Location: ../view/homepage_patient.html');
                    break;
                case 'doctor':
                    header('Location: ../view/doctor_dashboard.html');
                    break;
                case 'admin':
                    header('Location: ../view/admin_dashboard.html');
                    break;
                default:
                    header('Location: ../view/homepage.html'); // fallback
                    break;
            }
            exit;

        } else {
            header('Location: ../view/login.html?error=' . urlencode($result['message']));
            exit;
        }

    } catch (Exception $e) {
        header('Location: ../view/login.html?error=' . urlencode($e->getMessage()));
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
