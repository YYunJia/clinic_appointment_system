<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/serviceTypeFactory.php';
require_once __DIR__ . '/../models/appointmentTypeFactory.php';
require_once __DIR__ . '/../models/Service.php'; // Assuming you have a Service model to get service by ID

class appointmentController {

 
    public function getAppointmentByPatient($patient_id) {
        $apptModel = new Appointment();
        $appointment = $apptModel->getAptByPatient($patient_id);
        header('Content-Type: application/json');
        if ($appointment === false) {
            echo json_encode(['error' => 'No service list found']);
        } else {
            echo json_encode($appointment);
        }
    }
    
    public function getAllAppointments() {
        $apptModel = new Service();
        $appointment = $apptModel->getServices();
        header('Content-Type: application/json');
        if ($services === false) {
            echo json_encode(['error' => 'No service list found']);
        } else {
            echo json_encode($services);
        }
    }

    // GET /api/appointments/doctor/{doctor_id}
    public function getByDoctor($doctor_id) {
        header('Content-Type: application/json');
        $data = $this->appointment->getAptByDentist($doctor_id);
        echo json_encode($data);
    }

    // GET /api/appointments/{appointment_id}
    public function getById($appointment_id) {
        header('Content-Type: application/json');
        $data = $this->appointment->getAptById($appointment_id);
        echo json_encode($data);
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

        // Get service info from DB by service_id
        require_once __DIR__ . '/../models/Service.php';
        $serviceModel = new Service();
        $service = $serviceModel->getById($input['service_id']);
        if (!$service) {
            http_response_code(404);
            echo json_encode(['error' => 'Service not found']);
            return;
        }

        // ServiceType Factory
        try {
            $serviceTypeObj = serviceTypeFactory::create($service['service_name']);
            $serviceInfo = $serviceTypeObj->getServiceInfo();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        // AppointmentType Factory
        $aptTypeObj = appointmentTypeFactory::create($input['book_type']);
        $aptData = $aptTypeObj->prepare([
            'patient_id' => $input['patient_id'],
            'doctor_id' => $input['doctor_id'],
            'service_id' => $input['service_id'],
            'apt_datetime' => $input['appointment_datetime'],
            'type' => $input['book_type']
        ]);

        // Create the appointment
        $result = $this->appointment->createAppointment(
            $aptData['patient_id'],
            $aptData['doctor_id'],
            $aptData['service_id'],
            $aptData['apt_datetime'],
            $aptData['book_type'],
            $aptData['status']
        );

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Appointment created', 'data' => $result, 'service_info' => $serviceInfo]);
    }

    // PATCH /api/appointments/{appointment_id}/status
    public function updateStatus($appointment_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing status']);
            return;
        }
        $result = $this->appointment->updateAptStatus($appointment_id, $input['status']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Status updated', 'data' => $result]);
    }

    // PATCH /api/appointments/{appointment_id}/reschedule
    public function reschedule($appointment_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['appointment_datetime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing appointment_datetime']);
            return;
        }
        $result = $this->appointment->rescheduleApt($appointment_id, $input['appointment_datetime']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Appointment rescheduled', 'data' => $result]);
    }

    // PATCH /api/appointments/{appointment_id}/cancel/{who}
    public function cancel($appointment_id, $who) {
        if ($who === 'clinic') {
            $result = $this->appointment->cancelAptByClinic($appointment_id);
        } else {
            $result = $this->appointment->cancelAptByPatient($appointment_id);
        }
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Appointment cancelled', 'data' => $result]);
    }
}