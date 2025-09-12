<?php
/**
 * Get Time Slots API
 * Reusable API that can be called by any controller
 * Used by: Dashboard Controller (for appointment booking)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Only output JSON if called directly
$isDirectCall = basename($_SERVER['PHP_SELF']) === 'getTimeSlotsAPI.php';

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $doctor_id = $_GET['doctor_id'] ?? '';
    $date = $_GET['date'] ?? '';
    
    if (empty($doctor_id) || empty($date)) {
        throw new Exception('Missing doctor_id or date parameter');
    }
    
    $dayOfWeek = date('w', strtotime($date));
    
    // Get doctor's schedule
    $scheduleStmt = $conn->prepare("
        SELECT start_time, end_time
        FROM clinic_schedules 
        WHERE doctor_id = :doctor_id 
        AND day_of_week = :day_of_week 
        AND is_available = 1
        ORDER BY start_time ASC
    ");
    $scheduleStmt->execute([
        ':doctor_id' => $doctor_id,
        ':day_of_week' => $dayOfWeek
    ]);
    $schedules = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get booked appointments
    $bookedStmt = $conn->prepare("
        SELECT DATE_FORMAT(appointment_datetime, '%H:%i') as booked_time
        FROM appointments 
        WHERE doctor_id = :doctor_id 
        AND DATE(appointment_datetime) = :date
        AND status NOT IN ('cancelled_by_patient', 'cancelled_by_clinic')
    ");
    $bookedStmt->execute([
        ':doctor_id' => $doctor_id,
        ':date' => $date
    ]);
    $bookedSlots = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Build available slots
    $availableSlots = [];
    foreach ($schedules as $schedule) {
        $timeSlot = substr($schedule['start_time'], 0, 5);
        if (!in_array($timeSlot, $bookedSlots)) {
            $availableSlots[] = [
                'time' => $timeSlot,
                'display' => date('g:i A', strtotime($schedule['start_time']))
            ];
        }
    }
    
    $response = [
        'success' => true,
        'data' => $availableSlots,
        'doctor_id' => $doctor_id,
        'date' => $date,
        'count' => count($availableSlots)
    ];
    
    if ($isDirectCall) {
        echo json_encode($response);
    } else {
        return $response;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'error' => 'Error getting time slots: ' . $e->getMessage()
    ];
    
    if ($isDirectCall) {
        http_response_code(400);
        echo json_encode($errorResponse);
    } else {
        throw new Exception($errorResponse['error']);
    }
}
?>