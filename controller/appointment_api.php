<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../database.php';

try {
    $conn = getDBConnection();
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getFormData':
            // Get patients
            $patientsStmt = $conn->query("
                SELECT p.patient_id, p.user_id, p.emergency_contact_name, p.emergency_contact_phone,
                       u.name, u.email, u.phone_number
                FROM patients p 
                JOIN users u ON p.user_id = u.user_id 
                ORDER BY u.name ASC
            ");
            $patients = $patientsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get doctors
            $doctorsStmt = $conn->query("
                SELECT d.doctor_id, d.user_id, d.specialization, d.license_number,
                       u.name, u.email, u.phone_number
                FROM doctors d 
                JOIN users u ON d.user_id = u.user_id 
                ORDER BY u.name ASC
            ");
            $doctors = $doctorsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get services grouped by category
            $servicesStmt = $conn->query("SELECT * FROM services ORDER BY service_category, service_name");
            $servicesData = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $services = [];
            foreach ($servicesData as $service) {
                $category = $service['service_category'];
                if (!isset($services[$category])) {
                    $services[$category] = [];
                }
                $services[$category][] = $service;
            }
            
            // Try to get current user info from session (safely - won't break if it fails)
            $currentUser = null;
            try {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'doctor') {
                    $currentUserStmt = $conn->prepare("
                        SELECT d.doctor_id, u.name, d.specialization
                        FROM doctors d 
                        JOIN users u ON d.user_id = u.user_id 
                        WHERE d.user_id = :user_id
                    ");
                    $currentUserStmt->execute([':user_id' => $_SESSION['user_id']]);
                    $doctorInfo = $currentUserStmt->fetch(PDO::FETCH_ASSOC);
                    
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
                // If session stuff fails, just continue without it
            }
            
            // Always return the basic data, currentUser is optional
            echo json_encode([
                'patients' => $patients,
                'doctors' => $doctors,
                'services' => $services,
                'currentUser' => $currentUser
            ]);
            break;
            
        case 'getTimeSlots':
            $doctor_id = $_GET['doctor_id'] ?? '';
            $date = $_GET['date'] ?? '';
            
            if (empty($doctor_id) || empty($date)) {
                echo json_encode(['error' => 'Missing parameters']);
                exit;
            }
            
            $dayOfWeek = date('w', strtotime($date));
            
            // Get schedule
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
            
            // Get booked times
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
            
            echo json_encode(['slots' => $availableSlots]);
            break;
            
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Method not allowed']);
                exit;
            }
            
            $patient_id = $_POST['patient_id'] ?? '';
            $doctor_id = $_POST['doctor_id'] ?? '';
            $service_id = $_POST['service_id'] ?? '';
            $appointment_date = $_POST['appointment_date'] ?? '';
            $appointment_time = $_POST['appointment_time'] ?? '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($service_id) || empty($appointment_date) || empty($appointment_time)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }
            
            $appointment_datetime = $appointment_date . ' ' . $appointment_time . ':00';
            $appointment_id = 'APP' . date('YmdHis');
            
            $insertStmt = $conn->prepare("
                INSERT INTO appointments
                (appointment_id, patient_id, doctor_id, service_id, appointment_datetime, status)
                VALUES
                (:appointment_id, :patient_id, :doctor_id, :service_id, :appointment_datetime, 'scheduled')
            ");
            
            $result = $insertStmt->execute([
                ':appointment_id' => $appointment_id,
                ':patient_id' => $patient_id,
                ':doctor_id' => $doctor_id,
                ':service_id' => $service_id,
                ':appointment_datetime' => $appointment_datetime
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Appointment created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create appointment']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

?>