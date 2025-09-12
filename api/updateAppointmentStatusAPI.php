<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get data from JSON input or POST
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $appointment_id = $data['appointment_id'] ?? $_GET['appointment_id'] ?? '';
    $status = $data['status'] ?? $_GET['status'] ?? '';
    
    if (empty($appointment_id) || empty($status)) {
        throw new Exception('Appointment ID and status are required');
    }
    
    // Validate status
    $validStatuses = ['scheduled', 'pending_payment', 'completed', 'cancelled_by_patient', 'cancelled_by_clinic'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status');
    }
    
    $stmt = $conn->prepare("
        UPDATE appointments 
        SET status = :status, updated_at = NOW() 
        WHERE appointment_id = :appointment_id
    ");
    
    $result = $stmt->execute([
        ':appointment_id' => $appointment_id,
        ':status' => $status
    ]);
    
    if (!$result) {
        throw new Exception('Failed to update appointment status');
    }
    
    // Get updated appointment details
    $stmt = $conn->prepare("
        SELECT a.*, 
               p.name as patient_name,
               d.name as doctor_name, doc.specialization,
               s.service_name, s.base_price
        FROM appointments a
        LEFT JOIN patients pt ON a.patient_id = pt.patient_id
        LEFT JOIN users p ON pt.user_id = p.user_id
        LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
        LEFT JOIN users d ON doc.user_id = d.user_id
        LEFT JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_id = :appointment_id
    ");
    $stmt->execute([':appointment_id' => $appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Appointment status updated successfully',
        'data' => $appointment
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error updating appointment status: ' . $e->getMessage()
    ]);
}
?>