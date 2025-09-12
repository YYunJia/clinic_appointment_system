<?php
require_once '../controller/AuthenticationController.php';

session_start();

// Redirect non-logged-in users
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/login.html');
    exit;
}

// Redirect non-admins
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../view/homepage.html?error=access_denied');
    exit;
}

$authController = new AuthenticationController();
$authController->registerDoctor();
?>
