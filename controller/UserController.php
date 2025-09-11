<?php
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $authService;
    private $userModel;

    public function __construct() {
        $this->authService = new AuthService();
        $this->userModel = new User();
    }

    // Show user profile
    public function showProfile() {
        $this->authService->requireAuth();
        
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            $this->setFlashMessage('error', 'User not found');
            header('Location: login.php');
            exit;
        }

        $this->renderView('profile', ['user' => $user->toArray()]);
    }

    // Update user profile
    public function updateProfile() {
        $this->authService->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showProfile();
            return;
        }

        try {
            $user = $this->authService->getCurrentUser();
            if (!$user) {
                throw new Exception('User not found');
            }

            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'address' => trim($_POST['address'] ?? '')
            ];

            // Validate required fields
            if (empty($data['first_name']) || empty($data['last_name'])) {
                throw new Exception('First name and last name are required');
            }

            $result = $this->authService->updateProfile($user->getId(), $data);

            if ($result['success']) {
                $this->setFlashMessage('success', $result['message']);
            } else {
                $this->setFlashMessage('error', $result['message']);
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Profile update failed: ' . $e->getMessage());
        }

        $this->showProfile();
    }

    // Change password
    public function changePassword() {
        $this->authService->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showProfile();
            return;
        }

        try {
            $user = $this->authService->getCurrentUser();
            if (!$user) {
                throw new Exception('User not found');
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('All password fields are required');
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception('New password and confirmation do not match');
            }

            $result = $this->authService->changePassword($user->getId(), $currentPassword, $newPassword);

            if ($result['success']) {
                $this->setFlashMessage('success', $result['message']);
            } else {
                $this->setFlashMessage('error', $result['message']);
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Password change failed: ' . $e->getMessage());
        }

        $this->showProfile();
    }

    // List all users (admin only)
    public function listUsers() {
        $this->authService->requireRole('admin');
        
        try {
            $users = $this->userModel->getAllByRole('patient');
            $doctors = $this->userModel->getAllByRole('doctor');
            
            $this->renderView('user_list', [
                'users' => $users,
                'doctors' => $doctors
            ]);
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Failed to load users: ' . $e->getMessage());
            $this->renderView('user_list', ['users' => [], 'doctors' => []]);
        }
    }

    // Deactivate user (admin only)
    public function deactivateUser() {
        $this->authService->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: user_list.php');
            exit;
        }

        try {
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                throw new Exception('User ID is required');
            }

            $user = $this->userModel->findById($userId);
            if (!$user) {
                throw new Exception('User not found');
            }

            // Prevent admin from deactivating themselves
            $currentUser = $this->authService->getCurrentUser();
            if ($user->getId() == $currentUser->getId()) {
                throw new Exception('Cannot deactivate your own account');
            }

            $result = $user->delete(); // Soft delete

            if ($result) {
                $this->setFlashMessage('success', 'User deactivated successfully');
            } else {
                $this->setFlashMessage('error', 'Failed to deactivate user');
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Deactivation failed: ' . $e->getMessage());
        }

        header('Location: user_list.php');
        exit;
    }

    // Activate user (admin only)
    public function activateUser() {
        $this->authService->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: user_list.php');
            exit;
        }

        try {
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                throw new Exception('User ID is required');
            }

            $user = $this->userModel->findById($userId);
            if (!$user) {
                throw new Exception('User not found');
            }

            $result = $user->update(['is_active' => 1]);

            if ($result) {
                $this->setFlashMessage('success', 'User activated successfully');
            } else {
                $this->setFlashMessage('error', 'Failed to activate user');
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Activation failed: ' . $e->getMessage());
        }

        header('Location: user_list.php');
        exit;
    }

    // Show user details (admin only)
    public function showUserDetails() {
        $this->authService->requireRole('admin');
        
        $userId = $_GET['id'] ?? '';
        
        if (empty($userId)) {
            $this->setFlashMessage('error', 'User ID is required');
            header('Location: user_list.php');
            exit;
        }

        try {
            $user = $this->userModel->findById($userId);
            
            if (!$user) {
                $this->setFlashMessage('error', 'User not found');
                header('Location: user_list.php');
                exit;
            }

            $this->renderView('user_details', ['user' => $user->toArray()]);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Failed to load user details: ' . $e->getMessage());
            header('Location: user_list.php');
            exit;
        }
    }

    // Render view with data
    private function renderView($viewName, $data = []) {
        $flashMessage = $this->getFlashMessage();
        $data['flash_message'] = $flashMessage;
        
        $viewFile = __DIR__ . '/../view/' . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            extract($data);
            include $viewFile;
        } else {
            // Fallback to HTML files
            $htmlFile = __DIR__ . '/../view/' . $viewName . '.html';
            if (file_exists($htmlFile)) {
                include $htmlFile;
            } else {
                echo "View not found: $viewName";
            }
        }
    }

    // Set flash message
    private function setFlashMessage($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    // Get and clear flash message
    private function getFlashMessage() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        
        return null;
    }
}
?>
