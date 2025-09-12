<?php
/**
 * Create Appointment Service
 * JSON API for creating appointments
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

class createAptService {
    private $conn;
    
    public function __construct() {
        require_once __DIR__ . '/../database.php';
        $this->conn = getDBConnection();
        
        if (!$this->conn) {
            $this->sendError('Database connection failed', 500);
        }
    }
    
    /**
     * Create a new appointment
     */
    public function createAppointment($data = null) {
        try {
            // If no data provided, get from POST/JSON input
            if (!$data) {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $this->sendError('Only POST method allowed', 405);
                }
                
                // Try to get JSON input first, then fall back to POST data
                $jsonInput = json_decode(file_get_contents('php://input'), true);
                if ($jsonInput) {
                    $data = $jsonInput;
                } else {
                    $data = $_POST;
                }
            }
            
            // Validate required fields
            $required = ['patient_id', 'doctor_id', 'service_id', 'appointment_datetime'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $this->sendError("Missing required field: $field", 400);
                }
            }
            
            // Validate service exists
            $service = $this->validateService($data['service_id']);
            if (!$service) {
                $this->sendError('Service not found', 404);
            }
            
            // Validate patient exists
            if (!$this->validatePatient($data['patient_id'])) {
                $this->sendError('Patient not found', 404);
            }
            
            // Validate doctor exists
            if (!$this->validateDoctor($data['doctor_id'])) {
                $this->sendError('Doctor not found', 404);
            }
            
            // Check for time conflicts
            if ($this->checkTimeConflict($data['doctor_id'], $data['appointment_datetime'])) {
                $this->sendError('Time slot is already booked', 409);
            }
            
            // Determine appointment type and status
            $bookType = strtolower($data['book_type'] ?? 'appointment');
            $status = ($bookType === 'consultation') ? 'scheduled' : 'pending_payment';
            $requirePayment = ($bookType !== 'consultation');
            
            // Generate appointment ID
            $appointmentId = 'APP' . date('YmdHis') . rand(1000, 9999);
            
            // Insert appointment
            $stmt = $this->conn->prepare("
                INSERT INTO appointments 
                (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status, reason, created_at)
                VALUES 
                (:appointment_id, :patient_id, :doctor_id, :service_id, :appointment_datetime, :status, :reason, NOW())
            ");
            
            $result = $stmt->execute([
                ':appointment_id' => $appointmentId,
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':service_id' => $data['service_id'],
                ':appointment_datetime' => $data['appointment_datetime'],
                ':status' => $status,
                ':reason' => $data['reason'] ?? ''
            ]);
            
            if (!$result) {
                $this->sendError('Failed to create appointment', 500);
            }
            
            // Get the created appointment with details
            $appointment = $this->getAppointmentDetails($appointmentId);
            
            $this->sendSuccess([
                'message' => 'Appointment created successfully',
                'appointment' => $appointment,
                'require_payment' => $requirePayment
            ], 201);
            
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Validate service exists
     */
    private function validateService($serviceId) {
        $stmt = $this->conn->prepare("SELECT * FROM services WHERE service_id = :service_id");
        $stmt->execute([':service_id' => $serviceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validate patient exists
     */
    private function validatePatient($patientId) {
        $stmt = $this->conn->prepare("SELECT patient_id FROM patients WHERE patient_id = :patient_id");
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validate doctor exists
     */
    private function validateDoctor($doctorId) {
        $stmt = $this->conn->prepare("SELECT doctor_id FROM doctors WHERE doctor_id = :doctor_id");
        $stmt->execute([':doctor_id' => $doctorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check for time conflicts
     */
    private function checkTimeConflict($doctorId, $datetime) {
        $stmt = $this->conn->prepare("
            SELECT appointment_id 
            FROM appointments 
            WHERE doctor_id = :doctor_id 
            AND appointment_datetime = :datetime
            AND status NOT IN ('cancelled_by_patient', 'cancelled_by_clinic')
        ");
        $stmt->execute([
            ':doctor_id' => $doctorId,
            ':datetime' => $datetime
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get appointment details with related information
     */
    private function getAppointmentDetails($appointmentId) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   p.name as patient_name, pu.email as patient_email,
                   d.name as doctor_name, d.specialization,
                   s.service_name, s.base_price
            FROM appointments a
            LEFT JOIN patients pt ON a.patient_id = pt.patient_id
            LEFT JOIN users p ON pt.user_id = p.user_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
            LEFT JOIN users d ON doc.user_id = d.user_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN users pu ON pt.user_id = pu.user_id
            WHERE a.appointment_id = :appointment_id
        ");
        $stmt->execute([':appointment_id' => $appointmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Send success response
     */
    private function sendSuccess($data, $httpCode = 200) {
        http_response_code($httpCode);
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }
    
    /**
     * Send error response
     */
    private function sendError($message, $httpCode = 400) {
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
}

// Handle direct API calls
if (basename($_SERVER['PHP_SELF']) === 'createAptService.php') {
    $service = new createAptService();
    $service->createAppointment();
}
?>