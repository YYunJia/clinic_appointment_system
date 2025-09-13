<?php
require_once __DIR__ . '/../database.php';

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
    
    public function getDentists() {
        $stmt = $this->pdo->query("
            SELECT d.doctor_id, u.name AS full_name, d.specialization
            FROM doctors d
            JOIN users u ON d.user_id = u.user_id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableSlots($doctor_id) {
        $stmt = $this->pdo->prepare("
            SELECT schedule_id, day_of_week, start_time, end_time
            FROM clinic_schedules
            WHERE doctor_id = :doctor_id AND is_available = 1
        ");
        $stmt->execute([':doctor_id' => $doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

