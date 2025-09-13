<?php

require_once __DIR__ . '/../controller/dentistsController.php';

$controller = new dentistsController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['doctor_id'])) {
        $controller->getAvailableSlots($_GET['doctor_id']);
    }
}


