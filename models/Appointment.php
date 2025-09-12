<?php

require_once __DIR__ . '/../database.php';

class Appointment {
    private $conn;
    
    // Model properties
    public $appointment_id;
    public $patient_id;
    public $doctor_id;
    public $service_id;
    public $appointment_datetime;
    public $status;
    public $reason;
    public $cancellation_reason;
    public $created_at;
    public $updated_at;

    // Valid status values
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED_BY_PATIENT = 'cancelled_by_patient';
    const STATUS_CANCELLED_BY_CLINIC = 'cancelled_by_clinic';
    const STATUS_NO_SHOW = 'no_show';

    public function __construct() {
        $this->conn = getDBConnection();
        $this->status = self::STATUS_SCHEDULED;
    }

    /**
     * Find all appointments with detailed information
     * @return array
     */
    public function findAll() {
        $stmt = $this->conn->query("
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone, p_user.email as patient_email,
                   d_user.name as doctor_name, doc.specialization as doctor_specialization,
                   s.service_name, s.duration, s.base_price, s.service_category
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            ORDER BY a.appointment_datetime DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find appointment by ID with detailed information
     * @param string $appointment_id
     * @return array|null
     */
    public function findById($appointment_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone, p_user.email as patient_email,
                   d_user.name as doctor_name, doc.specialization as doctor_specialization,
                   s.service_name, s.duration, s.base_price, s.service_category
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            WHERE a.appointment_id = :appointment_id
        ");
        $stmt->execute([':appointment_id' => $appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find appointments by patient ID
     * @param string $patient_id
     * @return array
     */
    public function findByPatientId($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   d_user.name as doctor_name, doc.specialization as doctor_specialization,
                   s.service_name, s.duration, s.base_price, s.service_category
            FROM appointments a
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            WHERE a.patient_id = :patient_id
            ORDER BY a.appointment_datetime DESC
        ");
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find appointments by doctor ID
     * @param string $doctor_id
     * @param string $date Optional - filter by specific date
     * @return array
     */
    public function findByDoctorId($doctor_id, $date = null) {
        $sql = "
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone, p_user.email as patient_email,
                   s.service_name, s.duration, s.base_price, s.service_category
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            WHERE a.doctor_id = :doctor_id
        ";

        $params = [':doctor_id' => $doctor_id];

        if ($date) {
            $sql .= " AND DATE(a.appointment_datetime) = :date";
            $params[':date'] = $date;
        }

        $sql .= " ORDER BY a.appointment_datetime ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find appointments by service ID
     * @param string $service_id
     * @return array
     */
    public function findByServiceId($service_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone,
                   d_user.name as doctor_name, doc.specialization as doctor_specialization
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            WHERE a.service_id = :service_id
            ORDER BY a.appointment_datetime DESC
        ");
        $stmt->execute([':service_id' => $service_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if there's a conflicting appointment
     * @param string $doctor_id
     * @param string $appointment_datetime
     * @param string $exclude_appointment_id Optional - exclude this appointment from check
     * @return bool
     */
    public function hasConflict($doctor_id, $appointment_datetime, $exclude_appointment_id = null) {
        $sql = "
            SELECT COUNT(*) 
            FROM appointments 
            WHERE doctor_id = :doctor_id 
            AND appointment_datetime = :appointment_datetime 
            AND status NOT IN (:cancelled_patient, :cancelled_clinic)
        ";

        $params = [
            ':doctor_id' => $doctor_id,
            ':appointment_datetime' => $appointment_datetime,
            ':cancelled_patient' => self::STATUS_CANCELLED_BY_PATIENT,
            ':cancelled_clinic' => self::STATUS_CANCELLED_BY_CLINIC
        ];

        if ($exclude_appointment_id) {
            $sql .= " AND appointment_id != :exclude_id";
            $params[':exclude_id'] = $exclude_appointment_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Create new appointment
     * @param string $patient_id
     * @param string $doctor_id
     * @param string $service_id
     * @param string $appointment_datetime
     * @param string $book_type
     * @param string $status
     * @return array|false
     */
    public function create($patient_id, $doctor_id, $service_id, $appointment_datetime, $book_type = 'online', $status = null) {
        try {
            // Generate unique appointment ID
            $appointment_id = 'APP' . date('YmdHis') . mt_rand(100, 999);
            
            // Use provided status or default
            $status = $status ?: self::STATUS_SCHEDULED;

            $stmt = $this->conn->prepare("
                INSERT INTO appointments
                (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status, reason)
                VALUES
                (:appointment_id, :patient_id, :doctor_id, :service_id, :appointment_datetime, :status, :reason)
            ");
            
            $result = $stmt->execute([
                ':appointment_id' => $appointment_id,
                ':patient_id' => $patient_id,
                ':doctor_id' => $doctor_id,
                ':service_id' => $service_id,
                ':appointment_datetime' => $appointment_datetime,
                ':status' => $status,
                ':reason' => $book_type
            ]);
            
            if ($result) {
                return $this->findById($appointment_id);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error creating appointment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update appointment status
     * @param string $appointment_id
     * @param string $status
     * @param string $reason Optional - cancellation reason
     * @return array|false
     */
    public function updateStatus($appointment_id, $status, $reason = null) {
        try {
            $sql = "UPDATE appointments SET status = :status";
            $params = [
                ':status' => $status,
                ':appointment_id' => $appointment_id
            ];

            if ($reason && in_array($status, [self::STATUS_CANCELLED_BY_PATIENT, self::STATUS_CANCELLED_BY_CLINIC])) {
                $sql .= ", cancellation_reason = :reason";
                $params[':reason'] = $reason;
            }

            $sql .= " WHERE appointment_id = :appointment_id";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return $this->findById($appointment_id);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error updating appointment status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reschedule appointment
     * @param string $appointment_id
     * @param string $new_datetime
     * @return array|false
     */
    public function reschedule($appointment_id, $new_datetime) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE appointments
                SET appointment_datetime = :new_datetime
                WHERE appointment_id = :appointment_id
            ");
            
            $result = $stmt->execute([
                ':new_datetime' => $new_datetime,
                ':appointment_id' => $appointment_id
            ]);
            
            if ($result) {
                return $this->findById($appointment_id);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error rescheduling appointment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel appointment by clinic
     * @param string $appointment_id
     * @param string $reason
     * @return array|false
     */
    public function cancelByClinic($appointment_id, $reason = null) {
        return $this->updateStatus($appointment_id, self::STATUS_CANCELLED_BY_CLINIC, $reason);
    }

    /**
     * Cancel appointment by patient
     * @param string $appointment_id
     * @param string $reason
     * @return array|false
     */
    public function cancelByPatient($appointment_id, $reason = null) {
        return $this->updateStatus($appointment_id, self::STATUS_CANCELLED_BY_PATIENT, $reason);
    }

    /**
     * Mark appointment as completed
     * @param string $appointment_id
     * @return array|false
     */
    public function complete($appointment_id) {
        return $this->updateStatus($appointment_id, self::STATUS_COMPLETED);
    }

    /**
     * Mark appointment as no-show
     * @param string $appointment_id
     * @return array|false
     */
    public function markNoShow($appointment_id) {
        return $this->updateStatus($appointment_id, self::STATUS_NO_SHOW);
    }

    /**
     * Get appointments by status
     * @param string $status
     * @return array
     */
    public function findByStatus($status) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone,
                   d_user.name as doctor_name, doc.specialization as doctor_specialization,
                   s.service_name, s.duration, s.base_price
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            WHERE a.status = :status
            ORDER BY a.appointment_datetime ASC
        ");
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get appointments by date range
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function findByDateRange($start_date, $end_date) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   p_user.name as patient_name, p_user.phone_number as patient_phone,
                   d_user.name as doctor_name, doc.specialization as doctor_specialization,
                   s.service_name, s.duration, s.base_price
            FROM appointments a
            LEFT JOIN patients pat ON a.patient_id = pat.patient_id
            LEFT JOIN users p_user ON pat.user_id = p_user.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id  
            LEFT JOIN users d_user ON doc.user_id = d_user.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            WHERE DATE(a.appointment_datetime) BETWEEN :start_date AND :end_date
            ORDER BY a.appointment_datetime ASC
        ");
        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get valid status options
     * @return array
     */
    public static function getValidStatuses() {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_CONFIRMED,
            self::STATUS_CHECKED_IN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED_BY_PATIENT,
            self::STATUS_CANCELLED_BY_CLINIC,
            self::STATUS_NO_SHOW
        ];
    }

    /**
     * Check if status is valid
     * @param string $status
     * @return bool
     */
    public static function isValidStatus($status) {
        return in_array($status, self::getValidStatuses());
    }

    /**
     * Hydrate model with data from database
     * @param array $data
     */
    public function hydrate($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray() {
        return [
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'service_id' => $this->service_id,
            'appointment_datetime' => $this->appointment_datetime,
            'status' => $this->status,
            'reason' => $this->reason,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

?>