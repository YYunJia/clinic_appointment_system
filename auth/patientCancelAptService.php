<?php

require_once __DIR__ . '/../controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get put data
    parse_str(file_get_contents("php://input"), $data);

    if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($data['appointment_id']) && isset($data['by']) && $data['by'] === 'patient') {
        $controller->cancelByPatient($data['appointment_id']);
    }
}

