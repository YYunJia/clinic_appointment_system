<?php
// Add this temporarily for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getFormData':
            getFormData();
            break;
            
        case 'getTimeSlots':
            getTimeSlots();
            break;
            
        case 'create':
            createAppointment();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function getFormData() {
    // Call each API and get their data
    $patients = callAPI('getAllPatientsAPI');
    $doctors = callAPI('getAllDoctorsAPI');
    $services = callAPI('getAllServicesAPI');
    $currentUser = callAPI('getCurrentUserAPI');
    
    echo json_encode([
        'success' => true,
        'patients' => $patients,
        'doctors' => $doctors,
        'services' => $services,
        'currentUser' => $currentUser
    ]);
}

function getTimeSlots() {
    $doctor_id = $_GET['doctor_id'] ?? '';
    $date = $_GET['date'] ?? '';
    
    if (empty($doctor_id) || empty($date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        return;
    }
    
    // Set parameters for the API
    $_GET['doctor_id'] = $doctor_id;
    $_GET['date'] = $date;
    
    $result = callAPI('getTimeSlotsAPI');
    
    echo json_encode([
        'success' => true,
        'slots' => $result,
        'doctor_id' => $doctor_id,
        'date' => $date
    ]);
}

function createAppointment() {
    $result = callAPI('createAppointmentAPI');
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