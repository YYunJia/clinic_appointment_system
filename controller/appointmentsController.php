<?php
// Add this temporarily for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getFormData':
            getFormData();
            break;
            
        case 'getAllAppointments':
            getAllAppointments();
            break;
            
        case 'getAppointmentsByDoctor':
            getAppointmentsByDoctor();
            break;
            
        case 'getAppointmentsByPatient':
            getAppointmentsByPatient();
            break;
            
        case 'getAppointmentDetails':
            getAppointmentDetails();
            break;
            
        case 'updateStatus':
            updateAppointmentStatus();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action for appointments']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function getFormData() {
    // Call each API and get their data
    $patients = callAPI('getAllPatientsAPI');
    $doctors = callAPI('getAllDoctorsAPI');
    $currentUser = callAPI('getCurrentUserAPI');
    
    echo json_encode([
        'success' => true,
        'patients' => $patients,
        'doctors' => $doctors,
        'currentUser' => $currentUser
    ]);
}

function getAllAppointments() {
    $result = callAPI('getAllAppointmentsAPI');
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'appointments' => $result
    ]);
}

function getAppointmentsByDoctor() {
    $doctor_id = $_GET['doctor_id'] ?? '';
    
    if (empty($doctor_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Doctor ID is required']);
        return;
    }
    
    // Set parameters for the API
    $_GET['doctor_id'] = $doctor_id;
    
    $result = callAPI('getAppointmentsByDoctorAPI');
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'appointments' => $result
    ]);
}

function getAppointmentsByPatient() {
    $patient_id = $_GET['patient_id'] ?? '';
    
    if (empty($patient_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Patient ID is required']);
        return;
    }
    
    // Set parameters for the API
    $_GET['patient_id'] = $patient_id;
    
    $result = callAPI('getAppointmentsByPatientAPI');
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'appointments' => $result
    ]);
}

function getAppointmentDetails() {
    $appointment_id = $_GET['appointment_id'] ?? '';
    
    if (empty($appointment_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
        return;
    }
    
    // Set parameters for the API
    $_GET['appointment_id'] = $appointment_id;
    
    $result = callAPI('getAppointmentDetailsAPI');
    
    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
}

function updateAppointmentStatus() {
    $result = callAPI('updateAppointmentStatusAPI');
    echo json_encode($result);
}

function callAPI($apiName) {
    $apiPath = __DIR__ . "/../api/{$apiName}.php";
    
    if (!file_exists($apiPath)) {
        throw new Exception("API file not found: {$apiName}");
    }
    
    // Capture any output and get the return value
    ob_start();
    $result = include $apiPath;
    $output = ob_get_clean();
    
    // If the API returned data directly, use that
    if (is_array($result) && isset($result['success'])) {
        return $result['data'] ?? $result;
    }
    
    // If the API echoed JSON, decode it
    if (!empty($output)) {
        $decoded = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['success'])) {
            if (!$decoded['success']) {
                throw new Exception($decoded['error'] ?? 'API error');
            }
            return $decoded['data'] ?? $decoded;
        }
    }
    
    // Fallback: try to return the raw result
    return $result;
}
?>
