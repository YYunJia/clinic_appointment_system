<?php
require_once __DIR__ . '/../database.php';

header('Content-Type: application/json');

if (!isset($_GET['username'])) {
    echo json_encode([
        "success" => false,
        "message" => "No username provided"
    ]);
    exit;
}

$username = $_GET['username'];

try {
    $conn = getDBConnection();

    // Prepare and execute query
    $stmt = $conn->prepare("
        SELECT user_id, username, email, user_type, name, phone_number
        FROM users
        WHERE username = :username
        LIMIT 1
    ");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "success" => true,
            "user" => $user
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
    }

} catch (PDOException $e) {
    // Return error JSON
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
