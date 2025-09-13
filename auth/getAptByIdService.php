<?php

require_once __DIR__ . '/../controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View appointment(s)
    if (isset($_GET['appointment_id'])) {
        $controller->getAppointmentById($_GET['appointment_id']);
    }
}
    
