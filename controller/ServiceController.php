<?php

require_once __DIR__ . '/../models/Service.php';

class ServiceController {

    private $service;

    public function __construct() {
        $this->service = new Service;
    }

    public function getServices() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $data = $this->service->getServices();

            header('Content-Type:application/json');
            if ($data === false) {
                echo json_encode(['error' =>'No service found']);
            } else{
                echo json_encode($data);
            }
        }
    }
    
    public function getServicesByid() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $data = $this->service->getServices();

            header('Content-Type:application/json');
            if ($data === false) {
                echo json_encode(['error' =>'No service found']);
            } else{
                echo json_encode($data);
            }
        }
    }
    
    
}
