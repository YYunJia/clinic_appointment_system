<?php
interface ServiceType {
    public function createAppointment($patient_id, $doctor_id, $datetime, $book_type);
}
