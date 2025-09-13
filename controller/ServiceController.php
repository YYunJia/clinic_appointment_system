<?php

require_once __DIR__ . '/../models/Service.php';

class ServiceController {

    public function getAllServices() {
        $serviceModel = new Service();
        $services = $serviceModel->getServices();
        header('Content-Type: application/json');
        if ($services === false) {
            echo json_encode(['error' => 'No service list found']);
        } else {
            echo json_encode($services);
        }
    }
    
    public function getDetailByCategory($serviceCategory)
    {
        $serviceModel = new Service();
        $services = $serviceModel->getServicesByCategory($serviceCategory);
        header('Content-Type: application/json');
        if ($services === false) {
            echo json_encode(['error' => 'No service detail found for the category']);
        } else {
            echo json_encode($services);
        }
    }

    public function getServiceDetail($serviceId)
    {
        $serviceModel = new Service();
        $services = $serviceModel->getServicesById($serviceId);
        header('Content-Type: application/json');
        if ($services === false) {
            echo json_encode(['error' => 'No service detail found']);
        } else {
            echo json_encode($services);
        }
    }
}

