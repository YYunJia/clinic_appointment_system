<?php
/**
 * Get All Patients API
 * Reusable API that can be called by any controller
 * Used by: Dashboard Controller, Appointments Controller
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'getAllPatientsAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $conn->prepare("
        SELECT p.patient_id, p.user_id, p.emergency_contact_name, p.emergency_contact_phone,
               u.name, u.email, u.phone_number
        FROM patients p 
        JOIN users u ON p.user_id = u.user_id 
        ORDER BY u.name ASC
    ");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'data' => $patients,
        'count' => count($patients)
    ];
    
    if ($isDirectCall) {
        echo json_encode($response);
    } else {
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error getting patients: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(500);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>