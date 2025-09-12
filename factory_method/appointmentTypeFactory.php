<?php
require_once __DIR__ . '/IAppointmentType.php';
require_once __DIR__ . '/ConsultationAppointment.php';
require_once __DIR__ . '/BookingAppointment.php';

class appointmentTypeFactory {
    public static function create(string $type): AppointmentTypeI {
        switch(strtolower($type)) {
            case 'consultation':
                return new ConsultationAppointment();
            default:
                return new BookingAppointment();
        }
    }
}
