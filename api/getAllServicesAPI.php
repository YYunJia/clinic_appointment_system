<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    require_once __DIR__ . '/../database.php';
    
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $conn->prepare("SELECT * FROM services ORDER BY service_category, service_name");
    $stmt->execute();
    $servicesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group services by category
    $services = [];
    foreach ($servicesData as $service) {
        $category = $service['service_category'] ?? 'General';
        if (!isset($services[$category])) {
            $services[$category] = [];
        }
        $services[$category][] = $service;
    }
    
    $response = [
        'success' => true,
        'data' => $services,
        'count' => count($servicesData)
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error getting services: ' . $e->getMessage()
    ]);
}
?>