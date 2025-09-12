<?php
// Debug version - place this in /controller/ folder temporarily

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Test basic PHP execution
echo json_encode(['debug' => 'PHP is working', 'action' => $_GET['action'] ?? 'none']);

// Uncomment lines below one by one to test each component:








// Test 4: Get form data
try {
    require_once __DIR__ . '/../services/AppointmentService.php';
    $appointmentService = new AppointmentService();
    $data = $appointmentService->getAppointmentFormData();
    echo json_encode(['debug' => 'Form data loaded', 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Form data loading failed: ' . $e->getMessage()]);
}


?>