<?php

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/serviceTypeFactory.php';
require_once __DIR__ . '/../models/appointmentTypeFactory.php';
require_once __DIR__ . '/../models/Service.php'; // Assuming you have a Service model to get service by ID

class appointmentController {

    public function getAppointmentByPatient($patientId) {
        $apptModel = new Appointment();
        $appointment = $apptModel->getAptByPatient($patientId);
        header('Content-Type: application/json');
        if ($appointment === false) {
            echo json_encode(['error' => 'No appointment found for this patient']);
        } else {
            echo json_encode($appointment);
        }
    }

    public function getAllAppointments() {
        $apptModel = new Appointment();
        $appointment = $apptModel->getAppointment();
        header('Content-Type: application/json');
        if ($appointment === false) {
            echo json_encode(['error' => 'No appointment list found']);
        } else {
            echo json_encode($appointment);
        }
    }

    // GET /api/appointments/doctor/{doctor_id}
    public function getAppointmentByDoctor($doctorId) {
        $apptModel = new Appointment();
        $appointment = $apptModel->getAptByDentist($doctorId);
        header('Content-Type: application/json');
        if ($appointment === false) {
            echo json_encode(['error' => 'No appointment list found for dentist']);
        } else {
            echo json_encode($appointment);
        }
    }

    // GET /api/appointments/{appointment_id}
    public function getAppointmentById($appointment_id) {
        $apptModel = new Appointment();
        $appointment = $apptModel->getAptById($appointment_id);
        header('Content-Type: application/json');
        if ($appointment === false) {
            echo json_encode(['error' => 'No appointment found']);
        } else {
            echo json_encode($appointment);
        }
    }

    // POST /api/appointments
    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);

        // Basic validation
        if (!isset($input['patient_id'], $input['doctor_id'], $input['service_id'], $input['appointment_datetime'], $input['book_type'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
    }
    
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Basic validation
        if (!isset($data['patient_id'], $data['doctor_id'], $data['service_id'], $data['appointment_datetime'], $data['book_type'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        // Get service record for name (for factory)
        $serviceModel = new Service();
        $service = $serviceModel->getServiceById($data['service_id']);
        if (!$service) {
            http_response_code(404);
            echo json_encode(['error' => 'Service not found']);
            exit;
        }

        // Use serviceTypeFactory for mapping (optional business logic)
        try {
            $serviceTypeObj = serviceTypeFactory::create($service['service_name']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        // Determine booking type and status
        $bookType = strtolower($data['book_type']);
        if ($bookType === 'consultation') {
            $status = 'scheduled'; // No payment needed
        } elseif ($bookType === 'appointment' || $bookType === 'service') {
            $status = 'pending_payment'; // Payment must be made before confirming
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid book_type']);
            exit;
        }

        // Use appointmentTypeFactory for appointment type logic
        $appointmentTypeObj = appointmentTypeFactory::create($bookType);
        $aptPrepData = $appointmentTypeObj->prepare([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'service_id' => $data['service_id'],
            'appointment_datetime' => $data['appointment_datetime'],
            'status' => $status,
            'book_type' => $data['book_type'],
        ]);

        // Create appointment
        $result = $this->appointmentModel->createAppointment(
            $aptPrepData['patient_id'],
            $aptPrepData['doctor_id'],
            $aptPrepData['service_id'],
            $aptPrepData['appointment_datetime'],
            $aptPrepData['book_type'],
            $aptPrepData['status']
        );
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Appointment created',
            'appointment' => $result,
            // You may want to return an indicator for frontend to trigger payment if needed
            'payment_required' => ($status === 'pending_payment')
        ]);
    }

    // PATCH /api/appointments/{id}/status
    public function updateStatus($appointment_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing status']);
            exit;
        }
        $result = $this->appointmentModel->updateAptStatus($appointment_id, $data['status']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Status updated', 'appointment' => $result]);
    }

    // PATCH /api/appointments/{id}/reschedule
    public function reschedule($appointment_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['appointment_datetime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing appointment_datetime']);
            exit;
        }
        $result = $this->appointmentModel->rescheduleApt($appointment_id, $data['appointment_datetime']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Rescheduled', 'appointment' => $result]);
    }

    // PATCH /api/appointments/{id}/cancel/patient
    public function cancelByPatient($appointment_id) {
        $result = $this->appointmentModel->cancelAptByPatient($appointment_id);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cancelled by patient', 'appointment' => $result]);
    }

    // PATCH /api/appointments/{id}/cancel/clinic
    public function cancelByClinic($appointment_id) {
        $result = $this->appointmentModel->cancelAptByClinic($appointment_id);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cancelled by clinic', 'appointment' => $result]);
    }

       
}
