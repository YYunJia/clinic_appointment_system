<?php
/**
 * Appointment API
 * Main API endpoint that uses the JSON service classes
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Include database connection
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get action parameter
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getFormData':
            getFormData($conn);
            break;
            
        case 'getTimeSlots':
            getTimeSlots($conn);
            break;
            
        case 'create':
            createAppointment();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Get form data for the dashboard
 */
function getFormData($conn) {
    try {
        // Get patients
        $patientsStmt = $conn->prepare("
            SELECT p.patient_id, p.user_id, p.emergency_contact_name, p.emergency_contact_phone,
                   u.name, u.email, u.phone_number
            FROM patients p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY u.name ASC
        ");
        $patientsStmt->execute();
        $patients = $patientsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get doctors
        $doctorsStmt = $conn->prepare("
            SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
                   u.name, u.email, u.phone_number
            FROM doctors d 
            JOIN users u ON d.user_id = u.user_id 
            ORDER BY u.name ASC
        ");
        $doctorsStmt->execute();
        $doctors = $doctorsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get services using the new service API
        $servicesGrouped = [];
        
        $serviceApiPath = __DIR__ . '/../auth/getServiceByCategoryService.php';
        if (file_exists($serviceApiPath)) {
            try {
                require_once $serviceApiPath;
                $serviceApi = new getServiceByCategoryService();
                
                // Capture output from the service
                ob_start();
                $serviceApi->getAllServicesGroupedByCategory();
                $output = ob_get_clean();
                
                // Decode the JSON response
                $response = json_decode($output, true);
                if ($response && isset($response['success']) && $response['success'] && isset($response['data'])) {
                    $servicesGrouped = $response['data'];
                }
                
            } catch (Exception $e) {
                // Fallback to direct query
                $servicesGrouped = getServicesDirectly($conn);
            }
        } else {
            // Direct query fallback
            $servicesGrouped = getServicesDirectly($conn);
        }
        
        // If services are still empty, try direct query
        if (empty($servicesGrouped)) {
            $servicesGrouped = getServicesDirectly($conn);
        }
        
        // Get current user info
        $currentUser = getCurrentUser($conn);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'patients' => $patients,
            'doctors' => $doctors,
            'services' => $servicesGrouped,
            'currentUser' => $currentUser
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error getting form data: ' . $e->getMessage());
    }
}

/**
 * Get services directly from database (fallback)
 */
function getServicesDirectly($conn) {
    $stmt = $conn->prepare("SELECT * FROM services ORDER BY service_category, service_name");
    $stmt->execute();
    $servicesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $services = [];
    foreach ($servicesData as $service) {
        $category = $service['service_category'] ?? 'General';
        if (!isset($services[$category])) {
            $services[$category] = [];
        }
        $services[$category][] = $service;
    }
    
    return $services;
}

/**
 * Get current user info
 */
function getCurrentUser($conn) {
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
                return [
                    'user_id' => $_SESSION['user_id'],
                    'user_type' => $_SESSION['user_type'],
                    'doctor_id' => $doctorInfo['doctor_id'],
                    'name' => $doctorInfo['name'],
                    'specialization' => $doctorInfo['specialization']
                ];
            }
        }
    } catch (Exception $e) {
        // Return null if session fails
        error_log('Session error: ' . $e->getMessage());
    }
    
    return null;
}

/**
 * Get available time slots
 */
function getTimeSlots($conn) {
    try {
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
        
        echo json_encode([
            'success' => true,
            'slots' => $availableSlots
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error getting time slots: ' . $e->getMessage());
    }
}

/**
 * Create new appointment using the service API
 */
function createAppointment() {
    try {
        $createAptServicePath = __DIR__ . '/../auth/createAptService.php';
        if (file_exists($createAptServicePath)) {
            require_once $createAptServicePath;
            
            // Get POST data
            $patient_id = $_POST['patient_id'] ?? '';
            $doctor_id = $_POST['doctor_id'] ?? '';
            $service_id = $_POST['service_id'] ?? '';
            $appointment_date = $_POST['appointment_date'] ?? '';
            $appointment_time = $_POST['appointment_time'] ?? '';
            $reason = $_POST['reason'] ?? '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($service_id) || 
                empty($appointment_date) || empty($appointment_time)) {
                throw new Exception('All required fields must be filled');
            }
            
            $appointment_datetime = $appointment_date . ' ' . $appointment_time . ':00';
            
            // Prepare data for the service
            $appointmentData = [
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
                'service_id' => $service_id,
                'appointment_datetime' => $appointment_datetime,
                'book_type' => 'appointment',
                'reason' => $reason
            ];
            
            // Use the service API
            $serviceApi = new createAptService();
            
            // Capture output from the service
            ob_start();
            $serviceApi->createAppointment($appointmentData);
            $output = ob_get_clean();
            
            // Return the service response
            echo $output;
            
        } else {
            throw new Exception('Appointment creation service not available');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error creating appointment: ' . $e->getMessage()
        ]);
    }
}
?>