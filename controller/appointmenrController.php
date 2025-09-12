<?php
// Factory Method Design Pattern for Appointment Processing

// Appointment interface
interface Appointment {
    public function process();
    public function confirm();
}

// Concrete Appointment classes
class DentureAppointment implements Appointment {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function process() {
        // Validate data
        if (!$this->validate()) {
            return false;
        }
        
        // Save to database
        if ($this->save()) {
            $this->sendConfirmationEmail();
            return true;
        }
        
        return false;
    }
    
    public function confirm() {
        return "Your denture appointment has been confirmed for " . $this->data['appointmentDate'] . " at " . $this->data['appointmentTime'];
    }
    
    private function validate() {
        // Basic validation
        if (empty($this->data['fullName']) || empty($this->data['email']) || empty($this->data['phone'])) {
            return false;
        }
        
        if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return true;
    }
    
    private function save() {
        // In a real application, this would save to a database
        // For demo purposes, we'll simulate a successful save
        return true;
    }
    
    private function sendConfirmationEmail() {
        // Email sending logic would go here
        // For demo purposes, we'll just log it
        error_log("Confirmation email sent to: " . $this->data['email']);
    }
}

// Appointment Factory
class AppointmentFactory {
    public static function createAppointment($type, $data) {
        switch ($type) {
            case 'denture':
                return new DentureAppointment($data);
            // Additional appointment types can be added here
            default:
                throw new Exception("Unknown appointment type: $type");
        }
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect form data
        $appointmentData = [
            'fullName' => $_POST['fullName'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'service' => $_POST['service'],
            'doctor' => $_POST['doctor'],
            'appointmentDate' => $_POST['appointmentDate'],
            'appointmentTime' => $_POST['appointmentTime'],
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Create appointment using factory
        $appointment = AppointmentFactory::createAppointment('denture', $appointmentData);
        
        // Process the appointment
        if ($appointment->process()) {
            // Redirect to payment page with success message
            header('Location: payment.php?status=success&message=' . urlencode($appointment->confirm()));
            exit();
        } else {
            // Redirect back with error message
            header('Location: appointment.php?status=error&message=Failed to process appointment');
            exit();
        }
    } catch (Exception $e) {
        // Log error and redirect back
        error_log("Appointment processing error: " . $e->getMessage());
        header('Location: appointment.php?status=error&message=An error occurred');
        exit();
    }
} else {
    // If someone tries to access this page directly, redirect them
    header('Location: appointment.php');
    exit();
}
?>

