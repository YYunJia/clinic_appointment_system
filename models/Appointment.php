<?php
require_once __DIR__ . '/../database.php';

class Appointment {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Save a new appointment
    public function createAppointment($patient_id, $doctor_id, $service_id, $appointment_datetime, $book_type, $status = 'scheduled') {
        $appointmentID = 'APT' . uniqid();
        $stmt = $this->conn->prepare("
            INSERT INTO appointments
            (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status, book_type)
            VALUES
            (:appointment_id, :patient_id, :doctor_id, :service_id, :appointment_datetime, :status, :book_type)
        ");
        $stmt->execute([
            ':appointment_id' => $appointmentID,
            ':patient_id'    => $patient_id,
            ':doctor_id'     => $doctor_id,
            ':service_id'    => $service_id,
            ':appointment_datetime' => $appointment_datetime,
            ':status'        => $status,
            ':book_type'        => $book_type,
        ]);
        return $this->getById($appointmentID);
    }
    
    public function getAppointment(){
        $stmt = $this->conn->query("SELECT * FROM appointments");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get appointments for a patient
    public function getAptByPatient($patient_id) {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE patient_id = :patient_id");
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAptByDentist($doctor_id) {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE doctor_id = :doctor_id");
        $stmt->execute([':doctor_id' => $doctor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single appointment by ID
    public function getAptById($appointment_id) {
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
        $stmt->execute([':appointment_id' => $appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function updateAptStatus($appointment_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE appointments
            SET status = :status
            WHERE appointment_id = :appointment_id
        ");
        $stmt->execute([
            ':status' => $status,
            ':appointment_id' => $appointment_id
        ]);
        return $this->getAptById($appointment_id);
    }
    
    public function rescheduleApt($appointment_id, $new_datetime){
        $stmt = $this->conn->prepare("
            UPDATE appointments
            SET appointment_datetime = :new_datetime
            WHERE appointment_id = :appointment_id
        ");
        $stmt->execute([
            ':appointment_datetime' => $new_datetime,
            ':appointment_id' => $appointment_id
        ]);
        return $this->getAptById($appointment_id);
    }

    // Cancel an appointment
    public function cancelAptByClinic($appointment_id) {
        return $this->updateAptStatus($appointment_id, 'cancelled_by_clinic');
    }
    
    public function cancelAptByPatient($appointment_id) {
        return $this->updateAptStatus($appointment_id, 'cancelled_by_patient');
    }
}