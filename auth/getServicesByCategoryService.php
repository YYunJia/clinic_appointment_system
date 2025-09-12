<?php
/**
 * Get Services by Category Service
 * JSON API for retrieving services by category
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

class getServiceByCategoryService {
    private $conn;
    
    public function __construct() {
        require_once __DIR__ . '/../database.php';
        $this->conn = getDBConnection();
        
        if (!$this->conn) {
            $this->sendError('Database connection failed', 500);
        }
    }
    
    /**
     * Get services by specific category
     */
    public function getServicesByCategory($category = null) {
        try {
            // If no category provided, get from GET parameter
            if (!$category) {
                $category = $_GET['service_category'] ?? $_GET['category'] ?? null;
            }
            
            if (empty($category)) {
                $this->sendError('Category parameter is required', 400);
            }
            
            $stmt = $this->conn->prepare("
                SELECT service_id, service_name, service_category, base_price, description, duration_minutes
                FROM services 
                WHERE service_category = :category
                ORDER BY service_name ASC
            ");
            $stmt->execute([':category' => $category]);
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->sendSuccess([
                'message' => 'Services retrieved successfully',
                'category' => $category,
                'data' => $services,
                'count' => count($services)
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all services grouped by category
     */
    public function getAllServicesGroupedByCategory() {
        try {
            $stmt = $this->conn->prepare("
                SELECT service_id, service_name, service_category, base_price, description, duration_minutes
                FROM services 
                ORDER BY service_category ASC, service_name ASC
            ");
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group by category
            $grouped = [];
            foreach ($services as $service) {
                $category = $service['service_category'] ?? 'General';
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][] = $service;
            }
            
            $this->sendSuccess([
                'message' => 'All services retrieved successfully',
                'data' => $grouped,
                'categories' => array_keys($grouped),
                'total_services' => count($services)
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get all available categories
     */
    public function getCategories() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT service_category as category, COUNT(*) as service_count
                FROM services 
                GROUP BY service_category
                ORDER BY service_category ASC
            ");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->sendSuccess([
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
                'count' => count($categories)
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get single service by ID
     */
    public function getServiceById($serviceId = null) {
        try {
            if (!$serviceId) {
                $serviceId = $_GET['service_id'] ?? $_GET['id'] ?? null;
            }
            
            if (empty($serviceId)) {
                $this->sendError('Service ID parameter is required', 400);
            }
            
            $stmt = $this->conn->prepare("
                SELECT service_id, service_name, service_category, base_price, description, duration_minutes
                FROM services 
                WHERE service_id = :service_id
            ");
            $stmt->execute([':service_id' => $serviceId]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$service) {
                $this->sendError('Service not found', 404);
            }
            
            $this->sendSuccess([
                'message' => 'Service retrieved successfully',
                'data' => $service
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Send success response
     */
    private function sendSuccess($data, $httpCode = 200) {
        http_response_code($httpCode);
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }
    
    /**
     * Send error response
     */
    private function sendError($message, $httpCode = 400) {
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
}

// Handle direct API calls
if (basename($_SERVER['PHP_SELF']) === 'getServiceByCategoryService.php') {
    $service = new getServiceByCategoryService();
    
    // Route based on parameters
    if (isset($_GET['service_category']) || isset($_GET['category'])) {
        $service->getServicesByCategory();
    } elseif (isset($_GET['service_id']) || isset($_GET['id'])) {
        $service->getServiceById();
    } elseif (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'categories':
                $service->getCategories();
                break;
            case 'all':
            case 'grouped':
                $service->getAllServicesGroupedByCategory();
                break;
            default:
                $service->sendError('Invalid action parameter', 400);
        }
    } else {
        // Default: return all services grouped by category
        $service->getAllServicesGroupedByCategory();
    }
}
?>