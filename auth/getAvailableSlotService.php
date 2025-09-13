<?php
require_once __DIR__ . '/../controller/dentistsController.php';

$controller = new dentistsController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['doctor_id']) && isset($_GET['date'])) {
        // Convert date (Y-m-d) to day of week (0=Sunday, 6=Saturday)
        $day_of_week = date('w', strtotime($_GET['date']));
        $controller->getAvailableSlots($_GET['doctor_id'], $day_of_week);
    } else {
        echo json_encode(['error' => 'Missing parameters']);
    }
}
?>


