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

    // Handle user login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../view/login.html');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            $user = $result['user'];
            header('Location: ../view/homepage.html?login=success'
                    . '&username=' . urlencode($user['username'])
                    . '&role=' . urlencode($user['user_type'])
            );
            exit;
        } else {
            header('Location: ../view/login.html?error=' . urlencode($result['message']));
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
