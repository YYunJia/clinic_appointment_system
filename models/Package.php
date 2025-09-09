<?php
require_once __DIR__ . '/../database.php';

class Package {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Get remaining sessions for a patient and a specific package
    public function getRemainingSessions($patient_id, $package_id) {
        $stmt = $this->conn->prepare("
            SELECT sessions_remaining 
            FROM patient_packages
            WHERE patient_id = :patient_id AND package_id = :package_id
        ");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['sessions_remaining'] : 0;
    }

    // Deduct 1 session
    public function deductSession($patient_id, $package_id) {
        // Check current sessions
        $remaining = $this->getRemainingSessions($patient_id, $package_id);
        if ($remaining <= 0) {
            return false; // Cannot deduct
        }

        $stmt = $this->conn->prepare("
            UPDATE patient_packages
            SET sessions_remaining = sessions_remaining - 1
            WHERE patient_id = :patient_id AND package_id = :package_id
        ");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':package_id', $package_id);
        return $stmt->execute();
    }
}
