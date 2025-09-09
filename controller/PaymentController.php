<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON response header
header('Content-Type: application/json');

// Include necessary files
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../services/PaymentProcessor.php';
require_once __DIR__ . '/../strategies/CreditCardStrategy.php';
require_once __DIR__ . '/../strategies/EWalletStrategy.php';
require_once __DIR__ . '/../strategies/PackageStrategy.php';

try {
    // Ensure POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. POST required.');
    }

    // Check payment method
    $method = $_POST['payment_method'] ?? '';
    if (!$method) {
        throw new Exception('Payment method is required.');
    }

    // Select strategy based on method
    switch (strtolower($method)) {
        case 'visa':
        case 'mastercard':
            $strategy = new CreditCardStrategy();
            break;
        case 'ewallet':
            $strategy = new EWalletStrategy();
            break;
        case 'package':
            $strategy = new PackageStrategy();
            break;
        default:
            throw new Exception('Invalid payment method.');
    }

    // Execute payment
    $processor = new PaymentProcessor($strategy);
    $result = $processor->executePayment($_POST);

    // Return JSON response
    echo json_encode([
        'success' => $result['success'] ?? true,
        'message' => $result['message'] ?? 'Payment processed successfully.',
        'payment_id' => $result['payment_id'] ?? 'PAY1234'
    ]);

} catch (Exception $e) {
    http_response_code(400); // Bad request
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
