<?php
/**
 * Get All Appointments API
 * Reusable API that can be called by any controller
 * Used by: Appointments Controller (and potentially others)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'getAllAppointmentsAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $conn->prepare("
        SELECT a.*, 
               p.name as patient_name, pu.email as patient_email, pu.phone_number as patient_phone,
               d.name as doctor_name, doc.specialization,
               s.service_name, s.base_price, s.description as service_description
        FROM appointments a
        LEFT JOIN patients pt ON a.patient_id = pt.patient_id
        LEFT JOIN users p ON pt.user_id = p.user_id
        LEFT JOIN users pu ON pt.user_id = pu.user_id
        LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
        LEFT JOIN users d ON doc.user_id = d.user_id
        LEFT JOIN services s ON a.service_id = s.service_id
        ORDER BY a.appointment_datetime DESC
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'data' => $appointments,
        'count' => count($appointments)
    ];
    
    if ($isDirectCall) {
        echo json_encode($response);
    } else {
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error getting appointments: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(500);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>