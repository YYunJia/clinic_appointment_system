<?php
require_once __DIR__ . '/controller/AppointmentController.php';

$controller = new AppointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create appointment (book/consult)
    $data = json_decode(file_get_contents("php://input"), true);
    $controller->makeAppointment($data);
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View appointment(s)
    if (isset($_GET['appointment_id'])) {
        $controller->viewAppointment($_GET['appointment_id']);
    } elseif (isset($_GET['patient_id'])) {
        $controller->getAppointmentsByPatient($_GET['patient_id']);
    } elseif (isset($_GET['doctor_id'])) {
        $controller->getAppointmentsByDoctor($_GET['doctor_id']);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Handle update status, reschedule, cancel
    parse_str(file_get_contents("php://input"), $data);
    if (isset($_GET['action']) && $_GET['action'] === 'status') {
        $controller->updateStatus($data['appointment_id'], $data['status']);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'reschedule') {
        $controller->reschedule($data['appointment_id'], $data['new_datetime']);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'cancel') {
        $controller->cancelAppointment($data['appointment_id'], $data['by']);
    }
}