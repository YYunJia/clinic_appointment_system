<?php
require_once __DIR__ . '/../database.php';

class Patient {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Get all patients with user details
    public function getAllPatients() {
        $stmt = $this->conn->query("
            SELECT p.patient_id, p.user_id, p.emergency_contact_name, p.emergency_contact_phone,
                   u.name, u.email, u.phone_number
            FROM patients p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY u.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get patient by ID
    public function findById($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT p.patient_id, p.user_id, p.emergency_contact_name, p.emergency_contact_phone,
                   u.name, u.email, u.phone_number
            FROM patients p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE p.patient_id = :patient_id
        ");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>