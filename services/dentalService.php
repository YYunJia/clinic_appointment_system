<?php
require_once __DIR__ . '/controller/ServiceController.php';

$controller = new ServiceController();

// GET /service?category=xxx
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['service_category'])) {
        $controller->getDetailByCategory($_GET['category']);
    } elseif (isset($_GET['service_id'])) {
        $controller->getServiceDetail($_GET['id']);
    } else {
        $controller->getAllServices();
    }
}

