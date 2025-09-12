<?php
require_once('database.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/appointment_api.php', '', $path);
$path_segments = explode('/', trim($path, '/'));

// Main routing logic
try {
    switch ($method) {
        case 'GET':
            if (isset($path_segments[0])) {
                switch ($path_segments[0]) {
                    case 'services':
                        getServices();
                        break;
                    case 'available-slots':
                        getAvailableSlots();
                        break;
                    case 'appointments':
                        if (isset($path_segments[1])) {
                            getAppointment($path_segments[1]);
                        } else {
                            getAppointments();
                        }
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(["error" => "Endpoint not found"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
            
        case 'POST':
            if (isset($path_segments[0])) {
                switch ($path_segments[0]) {
                    case 'appointments':
                        createAppointment();
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(["error" => "Endpoint not found"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
            
        case 'PUT':
            if (isset($path_segments[0]) && $path_segments[0] == 'appointments' && isset($path_segments[1])) {
                updateAppointment($path_segments[1]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
            
        case 'DELETE':
            if (isset($path_segments[0]) && $path_segments[0] == 'appointments' && isset($path_segments[1])) {
                cancelAppointment($path_segments[1]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

// Function to get all services
function getServices() {
    $conn = getDBConnection();
    $stmt = $conn->query("SELECT * FROM services WHERE is_active = 1");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($services);
}

// Function to get available time slots
function getAvailableSlots() {
    $doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    $service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;
    
    $conn = getDBConnection();
    
    // Get doctor's schedule
    $day_of_week = strtolower(date('l', strtotime($date)));
    $query = "SELECT * FROM clinic_schedule WHERE doctor_id = :doctor_id AND day_of_week = :day AND is_available = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':day', $day_of_week);
    $stmt->execute();
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$schedule) {
        echo json_encode([]);
        return;
    }
    
    // Get service duration
    $service_duration = 30; // default 30 minutes
    if ($service_id) {
        $stmt = $conn->prepare("SELECT duration FROM services WHERE service_id = :service_id");
        $stmt->bindParam(':service_id', $service_id);
        $stmt->execute();
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($service) {
            $service_duration = $service['duration'];
        }
    }
    
    // Get existing appointments for that day
    $stmt = $conn->prepare("SELECT datetime FROM appointments WHERE doctor_id = :doctor_id AND DATE(datetime) = :date AND status != 'cancelled'");
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Generate available time slots
    $start_time = strtotime($date . ' ' . $schedule['start_time']);
    $end_time = strtotime($date . ' ' . $schedule['end_time']);
    $current_time = $start_time;
    $slots = [];
    
    while ($current_time + ($service_duration * 60) <= $end_time) {
        $slot_time = date('Y-m-d H:i:s', $current_time);
        $slot_end = date('Y-m-d H:i:s', $current_time + ($service_duration * 60));
        
        // Check if this slot is already booked
        $is_available = true;
        foreach ($appointments as $appt_time) {
            $appt_start = strtotime($appt_time);
            $appt_end = strtotime($appt_time) + ($service_duration * 60);
            
            if (($current_time >= $appt_start && $current_time < $appt_end) || 
                ($current_time + ($service_duration * 60) > $appt_start && 
                 $current_time + ($service_duration * 60) <= $appt_end)) {
                $is_available = false;
                break;
            }
        }
        
        if ($is_available) {
            $slots[] = [
                'start_time' => $slot_time,
                'end_time' => $slot_end,
                'doctor_id' => $doctor_id
            ];
        }
        
        $current_time += 1800; // 30-minute intervals
    }
    
    echo json_encode($slots);
}

// Function to get all appointments (with optional filtering)
function getAppointments() {
    $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
    $doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    
    $conn = getDBConnection();
    $query = "SELECT a.*, s.service_name, s.duration, u.first_name, u.last_name 
              FROM appointments a 
              JOIN services s ON a.service_id = s.service_id 
              JOIN users u ON a.patient_id = u.user_id 
              WHERE 1=1";
    
    $params = [];
    
    if ($patient_id) {
        $query .= " AND a.patient_id = :patient_id";
        $params[':patient_id'] = $patient_id;
    }
    
    if ($doctor_id) {
        $query .= " AND a.doctor_id = :doctor_id";
        $params[':doctor_id'] = $doctor_id;
    }
    
    if ($status) {
        $query .= " AND a.status = :status";
        $params[':status'] = $status;
    }
    
    if ($date) {
        $query .= " AND DATE(a.datetime) = :date";
        $params[':date'] = $date;
    }
    
    $query .= " ORDER BY a.datetime DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($appointments);
}

// Function to get a specific appointment
function getAppointment($appointment_id) {
    $conn = getDBConnection();
    $query = "SELECT a.*, s.service_name, s.duration, s.base_price, 
                     u.first_name, u.last_name, u.email, u.phone_number,
                     d.first_name as doctor_first_name, d.last_name as doctor_last_name
              FROM appointments a 
              JOIN services s ON a.service_id = s.service_id 
              JOIN users u ON a.patient_id = u.user_id 
              JOIN users d ON a.doctor_id = d.user_id 
              WHERE a.appointment_id = :appointment_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($appointment) {
        echo json_encode($appointment);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Appointment not found"]);
    }
}

// Function to create a new appointment
function createAppointment() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['patient_id', 'service_id', 'doctor_id', 'datetime'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: $field"]);
            return;
        }
    }
    
    $conn = getDBConnection();
    
    // Check if the slot is still available
    $datetime = $data['datetime'];
    $doctor_id = $data['doctor_id'];
    
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE doctor_id = :doctor_id AND datetime = :datetime AND status != 'cancelled'");
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':datetime', $datetime);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["error" => "The selected time slot is no longer available"]);
        return;
    }
    
    // Generate appointment ID
    $appointment_id = 'APT' . uniqid();
    
    // Create appointment
    $query = "INSERT INTO appointments (appointment_id, patient_id, service_id, doctor_id, datetime, status, created_at) 
              VALUES (:appointment_id, :patient_id, :service_id, :doctor_id, :datetime, 'confirmed', NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->bindParam(':patient_id', $data['patient_id']);
    $stmt->bindParam(':service_id', $data['service_id']);
    $stmt->bindParam(':doctor_id', $data['doctor_id']);
    $stmt->bindParam(':datetime', $data['datetime']);
    
    if ($stmt->execute()) {
        // Get the created appointment
        $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        http_response_code(201);
        echo json_encode([
            "message" => "Appointment created successfully",
            "appointment" => $appointment
        ]);
        
        // In a real application, you would send notifications here
        // sendNotifications($appointment);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to create appointment"]);
    }
}

// Function to update an appointment
function updateAppointment($appointment_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $conn = getDBConnection();
    
    // Check if appointment exists
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(["error" => "Appointment not found"]);
        return;
    }
    
    // Build update query based on provided fields
    $allowed_fields = ['datetime', 'doctor_id', 'status', 'notes'];
    $update_fields = [];
    $params = [':appointment_id' => $appointment_id];
    
    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $update_fields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode(["error" => "No valid fields to update"]);
        return;
    }
    
    $query = "UPDATE appointments SET " . implode(', ', $update_fields) . " WHERE appointment_id = :appointment_id";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute($params)) {
        echo json_encode(["message" => "Appointment updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update appointment"]);
    }
}

// Function to cancel an appointment
function cancelAppointment($appointment_id) {
    $conn = getDBConnection();
    
    // Check if appointment exists
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(["error" => "Appointment not found"]);
        return;
    }
    
    // Check if appointment can be cancelled (at least one day before)
    $stmt = $conn->prepare("SELECT datetime FROM appointments WHERE appointment_id = :appointment_id");
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $appointment_time = strtotime($appointment['datetime']);
    $current_time = time();
    $time_diff = $appointment_time - $current_time;
    
    if ($time_diff < 24 * 60 * 60) {
        http_response_code(400);
        echo json_encode(["error" => "Appointment can only be cancelled at least 24 hours in advance"]);
        return;
    }
    
    // Cancel the appointment
    $query = "UPDATE appointments SET status = 'cancelled' WHERE appointment_id = :appointment_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':appointment_id', $appointment_id);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Appointment cancelled successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to cancel appointment"]);
    }
}
?>