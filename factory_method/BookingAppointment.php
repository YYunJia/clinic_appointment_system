<?php

require_once __DIR__ . '/IAppointmentType.php';

class BookingAppointment implements IAppointmentType {
    public function prepare(array $data): array {
        $data['status'] = 'scheduled';
        $data['appointment_id'] = uniqid('APT');
        $data['book_type'] = 'booking';
        return $data;
    }
}
