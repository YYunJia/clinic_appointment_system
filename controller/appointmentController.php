<?php
/**
 * Appointments Controller
 * Handles requests for appointments viewing page
 * Routes to appropriate API endpoints
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 0);

class AppointmentsController {
    
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'getFormData':
                $this->getFormData();
                break;
                
            case 'getAllAppointments':
                $this->getAllAppointments();
                break;
                
            case 'getAppointmentsByDoctor':
                $this->getAppointmentsByDoctor();
                break;
                
            case 'getAppointmentsByPatient':
                $this->getAppointmentsByPatient();
                break;
                
            case 'getAppointmentDetails':
                $this->getAppointmentDetails();
                break;
                
            case 'updateStatus':
                $this->updateAppointmentStatus();
                break;
                
            default:
                $this->sendError('Invalid action for appointments', 400);
        }
    }
    
    /**
     * Get form data for appointments page filters
     * Calls APIs to get patients, doctors, current user for filter dropdowns
     */
    private function getFormData() {
        try {
            // Call individual APIs - reusing the same APIs as dashboard
            $patients = $this->callAPI('getAllPatientsAPI');
            $doctors = $this->callAPI('getAllDoctorsAPI');
            $currentUser = $this->callAPI('getCurrentUserAPI');
            
            $this->sendSuccess([
                'patients' => $patients,
                'doctors' => $doctors,
                'currentUser' => $currentUser
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Error getting form data: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all appointments for viewing
     * Calls getAllAppointmentsAPI
     */
    private function getAllAppointments() {
        try {
            $result = $this->callAPI('getAllAppointmentsAPI');
            echo json_encode($result);
            
        } catch (Exception $e) {
            $this->sendError('Error getting appointments: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get appointments by doctor
     * Calls getAppointmentsByDoctorAPI
     */
    private function getAppointmentsByDoctor() {
        try {
            $doctor_id = $_GET['doctor_id'] ?? '';
            
            if (empty($doctor_id)) {
                $this->sendError('Doctor ID is required', 400);
            }
            
            $result = $this->callAPI('getAppointmentsByDoctorAPI', [
                'doctor_id' => $doctor_id
            ]);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            $this->sendError('Error getting doctor appointments: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get appointments by patient
     * Calls getAppointmentsByPatientAPI
     */
    private function getAppointmentsByPatient() {
        try {
            $patient_id = $_GET['patient_id'] ?? '';
            
            if (empty($patient_id)) {
                $this->sendError('Patient ID is required', 400);
            }
            
            $result = $this->callAPI('getAppointmentsByPatientAPI', [
                'patient_id' => $patient_id
            ]);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            $this->sendError('Error getting patient appointments: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get detailed appointment information
     * Calls getAppointmentDetailsAPI
     */
    private function getAppointmentDetails() {
        try {
            $appointment_id = $_GET['appointment_id'] ?? '';
            
            if (empty($appointment_id)) {
                $this->sendError('Appointment ID is required', 400);
            }
            
            $result = $this->callAPI('getAppointmentDetailsAPI', [
                'appointment_id' => $appointment_id
            ]);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            $this->sendError('Error getting appointment details: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update appointment status
     * Calls updateAppointmentStatusAPI
     */
    private function updateAppointmentStatus() {
        try {
            $result = $this->callAPI('updateAppointmentStatusAPI');
            echo json_encode($result);
            
        } catch (Exception $e) {
            $this->sendError('Error updating appointment status: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Call individual API files
     */
    private function callAPI($apiName, $params = []) {
        $apiPath = __DIR__ . "/../api/{$apiName}.php";
        
        if (!file_exists($apiPath)) {
            throw new Exception("API file not found: {$apiName}");
        }
        
        // Preserve original GET parameters
        $originalGet = $_GET;
        
        // Set parameters for the API
        foreach ($params as $key => $value) {
            $_GET[$key] = $value;
        }
        
        // For POST/PUT requests, preserve the request method and body
        $originalMethod = $_SERVER['REQUEST_METHOD'];
        $originalInput = null;
        
        if (in_array($originalMethod, ['POST', 'PUT', 'DELETE'])) {
            $originalInput = file_get_contents('php://input');
        }
        
        // Capture the API output
        ob_start();
        include $apiPath;
        $output = ob_get_clean();
        
        // Restore original GET parameters
        $_GET = $originalGet;
        
        $result = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from {$apiName}");
        }
        
        if (isset($result['success']) && !$result['success']) {
            throw new Exception($result['error'] ?? "API error from {$apiName}");
        }
        
        return $result;
    }
    
    private function sendSuccess($data, $httpCode = 200) {
        http_response_code($httpCode);
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }
    
    private function sendError($message, $httpCode = 400) {
        http_response_code($httpCode);
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}

// Handle requests
try {
    $controller = new AppointmentsController();
    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>