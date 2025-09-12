<?php
require_once __DIR__ . '/../config/database.php';

class Dentist {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // List available doctors with their schedule
    public function getAvailableDentist() {
        $stmt = $this->pdo->query("
            SELECT d.doctor_id, d.first_name, d.last_name, d.specialization, s.day_of_week, s.start_time, s.end_time
            FROM doctor d
            JOIN clinic_schedule s ON d.doctor_id = s.doctor_id
            WHERE s.is_available = 1
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

