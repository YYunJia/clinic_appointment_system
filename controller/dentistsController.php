<?php

require_once __DIR__ . '/../models/Dentist.php';
require_once __DIR__ . '/../database.php';

class dentistsController {

    public function getAllDentist() {
        $pdo = getDBConnection();
        $dentistModel = new Dentist($pdo);
        $dentist = $dentistModel->getDentists();
        header('Content-Type: application/json');
        if ($dentist === false) {
            echo json_encode(['error' => 'No dentist found']);
        } else {
            echo json_encode($dentist);
        }
    }

    public function getAvailableSlots($doctor_id, $day_of_week) {
        $pdo = getDBConnection();
        $dentistModel = new Dentist($pdo);
        $slots = $dentistModel->getAvailableSlots($doctor_id, $day_of_week);
        header('Content-Type: application/json');
        echo json_encode($slots ?: []);
    }
}
