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
    
    public function getAllAvailableSlot() {
        $pdo = getDBConnection();
        $dentistModel = new Dentist($pdo);
        $slot = $dentistModel->getAvailableSlots();
        header('Content-Type: application/json');
        if ($slot === false) {
            echo json_encode(['error' => 'No available slot found']);
        } else {
            echo json_encode($slot);
        }
    }
}
