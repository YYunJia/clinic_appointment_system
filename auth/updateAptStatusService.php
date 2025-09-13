<?php

require_once __DIR__ . '/../controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get put data
    parse_str(file_get_contents("php://input"), $data);

    // Status update
    if (isset($_GET['action']) && $_GET['action'] === 'status' && isset($data['appointment_id']) && isset($data['status'])) {
        $controller->updateStatus($data['appointment_id']);
    }
}

