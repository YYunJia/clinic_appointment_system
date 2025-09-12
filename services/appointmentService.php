<?php

require_once __DIR__ . '/controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create appointment (book/consult)
    $data = json_decode(file_get_contents("php://input"), true);
    $controller->create();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View appointment(s)
    if (isset($_GET['appointment_id'])) {
        $controller->getAppointmentById($_GET['appointment_id']);
    } elseif (isset($_GET['patient_id'])) {
        $controller->getAppointmentsByPatient($_GET['patient_id']);
    } elseif (isset($_GET['doctor_id'])) {
        $controller->getAppointmentsByDoctor($_GET['doctor_id']);
    } else {
        $controller->getAllAppointments();
    }
} elseif ($method === 'PUT') {
    // Get put data
    parse_str(file_get_contents("php://input"), $data);

    // Status update
    if (isset($_GET['action']) && $_GET['action'] === 'status' && isset($data['appointment_id']) && isset($data['status'])) {
        $controller->updateStatus($data['appointment_id']);
    }
    // Reschedule
    elseif (isset($_GET['action']) && $_GET['action'] === 'reschedule' && isset($data['appointment_id']) && isset($data['appointment_datetime'])) {
        $controller->reschedule($data['appointment_id']);
    }
    // Cancel by patient
    elseif (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($data['appointment_id']) && isset($data['by']) && $data['by'] === 'patient') {
        $controller->cancelByPatient($data['appointment_id']);
    }
    // Cancel by clinic
    elseif (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($data['appointment_id']) && isset($data['by']) && $data['by'] === 'clinic') {
        $controller->cancelByClinic($data['appointment_id']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid PUT request or missing parameters']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid HTTP method']);
}