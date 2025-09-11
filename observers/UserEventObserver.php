<?php

// Observer interface
interface Observer {
    public function update($event, $data);
}

// Subject interface
interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify($event, $data);
}

// User Event Manager (Subject)
class UserEventManager implements Subject {
    private $observers = [];
    private $event;

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer) {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify($event, $data) {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }

    public function triggerEvent($event, $data) {
        $this->event = $event;
        $this->notify($event, $data);
    }
}

// Email Notification Observer
class EmailNotificationObserver implements Observer {
    public function update($event, $data) {
        switch ($event) {
            case 'user_registered':
                $this->sendWelcomeEmail($data);
                break;
            case 'doctor_registered':
                $this->sendDoctorWelcomeEmail($data);
                break;
            case 'user_login':
                $this->logLoginActivity($data);
                break;
        }
    }

    private function sendWelcomeEmail($userData) {
        // In a real application, you would send an actual email
        error_log("Welcome email sent to: " . $userData['email']);
        error_log("Welcome message: Welcome to Dental Clinic, " . $userData['first_name'] . "!");
    }

    private function sendDoctorWelcomeEmail($userData) {
        // In a real application, you would send an actual email
        error_log("Doctor welcome email sent to: " . $userData['email']);
        error_log("Welcome message: Welcome to Dental Clinic as a Doctor, Dr. " . $userData['last_name'] . "!");
    }

    private function logLoginActivity($userData) {
        error_log("User login: " . $userData['username'] . " (" . $userData['role'] . ") logged in at " . date('Y-m-d H:i:s'));
    }
}

// Audit Log Observer
class AuditLogObserver implements Observer {
    public function update($event, $data) {
        $this->logEvent($event, $data);
    }

    private function logEvent($event, $data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'user_id' => $data['id'] ?? null,
            'username' => $data['username'] ?? null,
            'role' => $data['role'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];

        // In a real application, you would save this to a database
        error_log("AUDIT LOG: " . json_encode($logEntry));
    }
}

// Security Alert Observer
class SecurityAlertObserver implements Observer {
    public function update($event, $data) {
        switch ($event) {
            case 'user_registered':
                $this->checkSuspiciousRegistration($data);
                break;
            case 'user_login':
                $this->checkSuspiciousLogin($data);
                break;
        }
    }

    private function checkSuspiciousRegistration($data) {
        // Check for suspicious patterns
        if (strpos($data['email'], 'temp') !== false || strpos($data['email'], 'test') !== false) {
            error_log("SECURITY ALERT: Suspicious registration detected for email: " . $data['email']);
        }
    }

    private function checkSuspiciousLogin($data) {
        // Check for multiple failed login attempts, unusual IP, etc.
        // This is a simplified example
        if ($data['role'] === 'admin') {
            error_log("SECURITY ALERT: Admin login detected for user: " . $data['username']);
        }
    }
}
?>
