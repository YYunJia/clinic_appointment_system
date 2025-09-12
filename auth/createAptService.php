<?php

require_once __DIR__ . '/controller/appointmentController.php';

$controller = new appointmentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create appointment (book/consult)
    $data = json_decode(file_get_contents("php://input"), true);
    $controller->create();
}

