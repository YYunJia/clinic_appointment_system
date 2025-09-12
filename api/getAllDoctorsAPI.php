<?php
/**
 * Get All Doctors API
 * Reusable API that can be called by any controller
 * Used by: Dashboard Controller, Appointments Controller
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'getAllDoctorsAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $conn->prepare("
        SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
               u.name, u.email, u.phone_number
        FROM doctors d 
        JOIN users u ON d.user_id = u.user_id 
        ORDER BY u.name ASC
    ");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'data' => $doctors,
        'count' => count($doctors)
    ];
    
    if ($isDirectCall) {
        echo json_encode($response);
    } else {
        // Return data for controller use
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error getting doctors: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(500);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>