<?php

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Service.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create appointment (book/consult)
    $data = json_decode(file_get_contents("php://input"), true);
    $controller->create();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View appointment(s)
    if (isset($_GET['appointment_id'])) {
        $controller->getAppointmentById($_GET['appointment_id']);
    } elseif (isset($_GET['patient_id'])) {
        $controller->getAppointmentsByPatient($_GET['patient_id']);
    } elseif (isset($_GET['doctor_id'])) {
        $controller->getAppointmentsByDoctor($_GET['doctor_id']);
    } else {
        $controller->getAllAppointments();
    }
}
class AppointmentService {

    private $appointmentModel;
    private $patientModel;
    private $doctorModel;
    private $serviceModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->serviceModel = new Service();
    }

    /**
     * Create a new appointment
     */
    public function createAppointment($data) {
        try {
            // Validate required fields
            $required = ['patient_id', 'doctor_id', 'service_id', 'appointment_date', 'appointment_time'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            // Validate patient exists
            $patient = $this->patientModel->findById($data['patient_id']);
            if (!$patient) {
                throw new Exception("Patient not found");
            }

            // Validate doctor exists
            $doctor = $this->doctorModel->findById($data['doctor_id']);
            if (!$doctor) {
                throw new Exception("Doctor not found");
            }

            // Validate service exists
            $service = $this->serviceModel->getById($data['service_id']);
            if (!$service) {
                throw new Exception("Service not found");
            }

            // Combine date and time
            $appointment_datetime = $data['appointment_date'] . ' ' . $data['appointment_time'] . ':00';

            // Validate appointment is in the future
            if (strtotime($appointment_datetime) <= time()) {
                throw new Exception("Appointment must be scheduled for a future date and time");
            }

            // Check if doctor is available at this time
            $dayOfWeek = date('w', strtotime($data['appointment_date']));
            if (!$this->isDoctorAvailable($data['doctor_id'], $dayOfWeek, $data['appointment_time'])) {
                throw new Exception("Doctor is not available at the selected time");
            }

            // Check for conflicting appointments
            if ($this->hasConflictingAppointment($data['doctor_id'], $appointment_datetime)) {
                throw new Exception("This time slot is already booked");
            }

            // Create the appointment
            $result = $this->appointmentModel->createAppointment(
                $data['patient_id'],
                $data['doctor_id'],
                $data['service_id'],
                $appointment_datetime,
                'online', // book_type
                'scheduled'
            );

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Appointment created successfully',
                    'appointment' => $result
                ];
            } else {
                throw new Exception("Failed to create appointment");
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all data needed for appointment form
     */
    public function getAppointmentFormData() {
        return [
            'patients' => $this->patientModel->getAllPatients(),
            'doctors' => $this->doctorModel->getAllDoctors(),
            'services' => $this->serviceModel->getServicesByCategory()
        ];
    }

    /**
     * Get appointments for a doctor
     */
    public function getDoctorAppointments($doctor_id, $date = null) {
        if ($date) {
            return $this->appointmentModel->getAptByDoctorAndDate($doctor_id, $date);
        } else {
            return $this->appointmentModel->getAptByDentist($doctor_id);
        }
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    public function getAvailableTimeSlots($doctor_id, $date) {
        return $this->doctorModel->getAvailableSlots($doctor_id, $date);
    }

    /**
     * Check if doctor is available at specific day and time
     */
    private function isDoctorAvailable($doctor_id, $dayOfWeek, $time) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM clinic_schedules 
            WHERE doctor_id = :doctor_id 
            AND day_of_week = :day_of_week 
            AND start_time <= :time 
            AND end_time > :time 
            AND is_available = 1
        ");
        
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':day_of_week', $dayOfWeek);
        $stmt->bindParam(':time', $time . ':00');
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check for conflicting appointments
     */
    private function hasConflictingAppointment($doctor_id, $appointment_datetime) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM appointments 
            WHERE doctor_id = :doctor_id 
            AND appointment_datetime = :appointment_datetime 
            AND status NOT IN ('cancelled_by_patient', 'cancelled_by_clinic')
        ");
        
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':appointment_datetime', $appointment_datetime);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus($appointment_id, $status) {
        try {
            $validStatuses = ['scheduled', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled_by_patient', 'cancelled_by_clinic', 'no_show'];
            
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status");
            }

            $result = $this->appointmentModel->updateAptStatus($appointment_id, $status);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Appointment status updated successfully',
                    'appointment' => $result
                ];
            } else {
                throw new Exception("Failed to update appointment status");
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    }
