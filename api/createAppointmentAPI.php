<?php
/**
 * Create Appointment API
 * Reusable API that can be called by any controller
 * Used by: Dashboard Controller (and potentially others)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'createAppointmentAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    // Get POST data
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id = $_POST['doctor_id'] ?? '';
    $service_id = $_POST['service_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    // Also try JSON input for API calls
    if (empty($patient_id)) {
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        if ($jsonInput) {
            $patient_id = $jsonInput['patient_id'] ?? '';
            $doctor_id = $jsonInput['doctor_id'] ?? '';
            $service_id = $jsonInput['service_id'] ?? '';
            $appointment_datetime = $jsonInput['appointment_datetime'] ?? '';
            $reason = $jsonInput['reason'] ?? '';
            
            // If we have datetime directly, use it; otherwise combine date and time
            if (empty($appointment_datetime)) {
                $appointment_date = $jsonInput['appointment_date'] ?? '';
                $appointment_time = $jsonInput['appointment_time'] ?? '';
                $appointment_datetime = $appointment_date . ' ' . $appointment_time . ':00';
            }
        }
    } else {
        $appointment_datetime = $appointment_date . ' ' . $appointment_time . ':00';
    }
    
    // Validate required fields
    if (empty($patient_id) || empty($doctor_id) || empty($service_id) || empty($appointment_datetime)) {
        throw new Exception('All required fields must be filled');
    }
    
    // Validate service exists
    $serviceStmt = $conn->prepare("SELECT * FROM services WHERE service_id = :service_id");
    $serviceStmt->execute([':service_id' => $service_id]);
    $service = $serviceStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        throw new Exception('Selected service not found');
    }
    
    // Validate patient exists
    $patientStmt = $conn->prepare("SELECT patient_id FROM patients WHERE patient_id = :patient_id");
    $patientStmt->execute([':patient_id' => $patient_id]);
    if (!$patientStmt->fetch()) {
        throw new Exception('Patient not found');
    }
    
    // Validate doctor exists
    $doctorStmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE doctor_id = :doctor_id");
    $doctorStmt->execute([':doctor_id' => $doctor_id]);
    if (!$doctorStmt->fetch()) {
        throw new Exception('Doctor not found');
    }
    
    // Check for time conflicts
    $conflictStmt = $conn->prepare("
        SELECT appointment_id 
        FROM appointments 
        WHERE doctor_id = :doctor_id 
        AND appointment_datetime = :datetime
        AND status NOT IN ('cancelled_by_patient', 'cancelled_by_clinic')
    ");
    $conflictStmt->execute([
        ':doctor_id' => $doctor_id,
        ':datetime' => $appointment_datetime
    ]);
    
    if ($conflictStmt->fetch()) {
        throw new Exception('Time slot is already booked');
    }
    
    // Generate appointment ID
    $appointment_id = 'APP' . date('YmdHis') . rand(1000, 9999);
    
    // Insert appointment
    $stmt = $conn->prepare("
        INSERT INTO appointments 
        (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status, reason, created_at)
        VALUES 
        (:appointment_id, :patient_id, :doctor_id, :service_id, :appointment_datetime, 'scheduled', :reason, NOW())
    ");
    
    $result = $stmt->execute([
        ':appointment_id' => $appointment_id,
        ':patient_id' => $patient_id,
        ':doctor_id' => $doctor_id,
        ':service_id' => $service_id,
        ':appointment_datetime' => $appointment_datetime,
        ':reason' => $reason
    ]);
    
    if (!$result) {
        throw new Exception('Failed to create appointment');
    }
    
    // Get the created appointment with details
    $detailsStmt = $conn->prepare("
        SELECT a.*, 
               p.name as patient_name, pu.email as patient_email,
               d.name as doctor_name, doc.specialization,
               s.service_name, s.base_price
        FROM appointments a
        LEFT JOIN patients pt ON a.patient_id = pt.patient_id
        LEFT JOIN users p ON pt.user_id = p.user_id
        LEFT JOIN users pu ON pt.user_id = pu.user_id
        LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
        LEFT JOIN users d ON doc.user_id = d.user_id
        LEFT JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_id = :appointment_id
    ");
    $detailsStmt->execute([':appointment_id' => $appointment_id]);
    $appointment = $detailsStmt->fetch(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'message' => 'Appointment created successfully',
        'data' => $appointment
    ];
    
    if ($isDirectCall) {
        http_response_code(201);
        echo json_encode($response);
    } else {
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error creating appointment: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(400);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>