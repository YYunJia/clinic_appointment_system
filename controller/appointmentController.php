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

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Basic validation
        if (!isset($data['patient_id'], $data['doctor_id'], $data['service_id'], $data['appointment_datetime'], $data['book_type'])) {
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $appointmentModel = new Appointment();
        $serviceModel = new Service();
        $service = $serviceModel->getServicesById($data['service_id']);
        if ($service===false) {
            echo json_encode(['error' => 'Service not found']);
            return;
        }

        $bookType = strtolower($data['book_type']);
        if ($bookType == 'consultation') {
            // Consultation: No payment, status is scheduled
            $result = $appointmentModel->createAppointment(
                $data['patient_id'],
                $data['doctor_id'],
                $data['service_id'],
                $data['appointment_datetime'],
                'consultation',
                'scheduled'
            );
            header('Content-Type: application/json');
            echo json_encode([
                'appointment' => $result,
                'require_payment' => false
            ]);
        } elseif ($bookType == 'appointment') {
    
            $result = $appointmentModel->createAppointment(
                $data['patient_id'],
                $data['doctor_id'],
                $data['service_id'],
                $data['appointment_datetime'],
                'appointment',
                'pending_payment'
            );
            header('Content-Type: application/json');
            echo json_encode([
                
                'appointment' => $result,
                'require_payment' => true
            ]);
        } else {
            
            echo json_encode(['error' => 'Invalid book_type']);
        }
    }

    public function updateStatus($appointmentId)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['status'])) {
            
            echo json_encode(['error' => 'Missing status']);
            return;
        }
        $appointmentModel = new Appointment();
        $result = $appointmentModel->updateAptStatus($appointmentId, $data['status']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Status updated', 'appointment' => $result]);
    }

    public function reschedule($appointmentId)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['appointment_datetime'])) {
            echo json_encode(['error' => 'Missing appointment_datetime']);
            return;
        }
        $appointmentModel = new Appointment();
        $result = $appointmentModel->rescheduleApt($appointmentId, $data['appointment_datetime']);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Rescheduled', 'appointment' => $result]);
    }

    public function cancelByPatient($appointment_id)
    {
        $appointmentModel = new Appointment();
        $result = $appointmentModel->cancelAptByPatient($appointment_id);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cancelled by patient', 'appointment' => $result]);
    }

    public function cancelByClinic($appointment_id)
    {
        $appointmentModel = new Appointment();
        $result = $appointmentModel->cancelAptByClinic($appointment_id);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cancelled by clinic', 'appointment' => $result]);
    }
}
