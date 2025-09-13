<?php
require_once __DIR__ . '/../database.php';

class Doctor {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Get all doctors with user details
    public function getAllDoctors() {
        $stmt = $this->conn->query("
            SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
                   u.name, u.email, u.phone_number
            FROM doctors d 
            JOIN users u ON d.user_id = u.user_id 
            ORDER BY u.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get doctor by ID
    public function findById($doctor_id) {
        $stmt = $this->conn->prepare("
            SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
                   u.name, u.email, u.phone_number
            FROM doctors d 
            JOIN users u ON d.user_id = u.user_id 
            WHERE d.doctor_id = :doctor_id
        ");
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get doctor by user ID (for session detection)
    public function findByUserId($user_id) {
        $stmt = $this->conn->prepare("
            SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
                   u.name, u.email, u.phone_number
            FROM doctors d 
            JOIN users u ON d.user_id = u.user_id 
            WHERE d.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>