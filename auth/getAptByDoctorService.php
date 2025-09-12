<?php

require_once __DIR__ . '/controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['doctor_id'])) {
        $controller->getAppointmentsByDoctor($_GET['doctor_id']);
    }
}

