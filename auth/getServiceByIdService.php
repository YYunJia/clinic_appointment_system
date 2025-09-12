<?php
require_once __DIR__ . '/../controller/ServiceController.php';

$controller = new ServiceController();

// GET /service?category=xxx
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
   if (isset($_GET['service_id'])) {
        $controller->getServiceDetail($_GET['id']);
    }
}

