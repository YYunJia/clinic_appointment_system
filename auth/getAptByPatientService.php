<?php

require_once __DIR__ . '/controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View appointment(s)
    if (isset($_GET['patient_id'])) {
        $controller->getAppointmentsByPatient($_GET['patient_id']);
    }
}
