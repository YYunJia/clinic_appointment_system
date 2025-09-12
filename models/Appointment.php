<?php
require_once __DIR__ . '/../database.php';

class Appointment {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Save a new appointment
    public function createAppointment($patient_id, $doctor_id, $service_id, $apt_datetime, $type, $status = 'scheduled') {
        $appointmentID = 'APT' . uniqid();
        $stmt = $this->pdo->prepare("
            INSERT INTO appointments
            (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status, book_type)
            VALUES
            (:appointment_id, :patient_id, :doctor_id, :service_id, :apt_datetime, :status, :type)
        ");
        $stmt->execute([
            ':appointment_id' => $appointmentID,
            ':patient_id'    => $patient_id,
            ':doctor_id'     => $doctor_id,
            ':service_id'    => $service_id,
            ':appointment_datetime' => $apt_datetime,
            ':status'        => $status,
            ':book_type'        => $type,
        ]);
        return $this->getById($appointmentID);
    }

    // Get appointments for a patient
    public function getAptByPatient($patient_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE patient_id = :patient_id");
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single appointment by ID
    public function getAptById($appointment_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
        $stmt->execute([':appointment_id' => $appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update an appointment (e.g., reschedule)
    public function updateAptStatus($appointment_id,$doctor_id, $service_id, $apt_datetime, $type, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE appointments
            SET doctor_id = :doctor_id, service_id = :service_id, appointment_datetime = :apt_datetime, status = :status
            WHERE appointment_id = :appointment_id
        ");
        $stmt->execute([
            ':doctor_id'      => $doctor_id,
            ':service_id'     => $service_id,
            ':appointment_datetime'       => $apt_datetime,
            ':status'         => $status,
            ':appointment_id' => $appointment_id
        ]);
        return $this->getById($appointment_id);
    }

    // Cancel an appointment
    public function cancelAppointment($appointment_id) {
        $stmt = $this->pdo->prepare("
            UPDATE appointments
            SET status = 'cancelled'
            WHERE appointment_id = :appointment_id
        ");
        $stmt->execute([':appointment_id' => $appointment_id]);
        return $this->getById($appointment_id);
    }
}
