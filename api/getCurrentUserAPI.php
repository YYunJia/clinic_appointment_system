<?php
/**
 * Get Current User API
 * Reusable API that can be called by any controller
 * Used by: Dashboard Controller, Appointments Controller
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'getCurrentUserAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $currentUser = null;
    
    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'doctor') {
            $stmt = $conn->prepare("
                SELECT d.doctor_id, u.name, d.specialization
                FROM doctors d 
                JOIN users u ON d.user_id = u.user_id 
                WHERE d.user_id = :user_id
            ");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $doctorInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doctorInfo) {
                $currentUser = [
                    'user_id' => $_SESSION['user_id'],
                    'user_type' => $_SESSION['user_type'],
                    'doctor_id' => $doctorInfo['doctor_id'],
                    'name' => $doctorInfo['name'],
                    'specialization' => $doctorInfo['specialization']
                ];
            }
        }
    } catch (Exception $e) {
        // If session handling fails, just return null
        error_log('Session error: ' . $e->getMessage());
    }
    
    $response = [
        'success' => true,
        'data' => $currentUser
    ];
    
    if ($isDirectCall) {
        echo json_encode($response);
    } else {
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error getting current user: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(500);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>