<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    require_once __DIR__ . '/../database.php';
    
    $appointment_id = $_GET['appointment_id'] ?? '';
    
    if (empty($appointment_id)) {
        throw new Exception('Appointment ID is required');
    }
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $conn->prepare("
        SELECT a.*, 
               p.name as patient_name, pu.email as patient_email, pu.phone_number as patient_phone,
               d.name as doctor_name, doc.specialization, du.email as doctor_email,
               s.service_name, s.base_price, s.description as service_description
        FROM appointments a
        LEFT JOIN patients pt ON a.patient_id = pt.patient_id
        LEFT JOIN users p ON pt.user_id = p.user_id
        LEFT JOIN users pu ON pt.user_id = pu.user_id
        LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
        LEFT JOIN users d ON doc.user_id = d.user_id
        LEFT JOIN users du ON doc.user_id = du.user_id
        LEFT JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_id = :appointment_id
    ");
    $stmt->execute([':appointment_id' => $appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        throw new Exception('Appointment not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $appointment
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error getting appointment details: ' . $e->getMessage()
    ]);
}
?>