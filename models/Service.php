<?php

require_once __DIR__ . '/../database.php';

class Service {

    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    public function getServices() {

        $stmt = $this->conn->query("SELECT * FROM services");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesById($serviceId) {
        $stmt = $this->conn->prepare("SELECT * FROM services WHERE service_id = :serviceId");
        $stmt->execute([':service_id' => $serviceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getServicesByCategory($serviceCategory) {
        $stmt = $this->conn->prepare("SELECT * FROM services WHERE service_category = :serviceCategory");
        $stmt->execute([':service_id' => $serviceCategory]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
