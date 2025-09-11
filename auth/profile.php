<?php
require_once '../controller/UserController.php';

$userController = new UserController();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'])) {
        // Update profile
        $userController->updateProfile();
    } elseif (isset($_POST['current_password'])) {
        // Change password
        $userController->changePassword();
    }
} else {
    // Show profile
    $userController->showProfile();
}
?>
