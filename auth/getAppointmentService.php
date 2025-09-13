<?php

require_once __DIR__ . '/../controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
        $controller->getAllAppointments();

}

