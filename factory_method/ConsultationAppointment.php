<?php

require_once __DIR__ . '/IAppointmentType.php';

class ConsultationAppointment implements IAppointmentType {
    public function prepare(array $data): array {
        $data['status'] = 'scheduled';
        $data['appointment_id'] = uniqid('CONS');
        $data['book_type'] = 'consultation';
        return $data;
    }
}