<?php

function getDBConnection() {

    $servername = "175.142.243.200";
    $username = "remote_user";
    $password = "dentalclinic123";
    $dbname = "dental_clinic";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}


// Initialize database tables
function initializeDatabase() {
    $conn = getDBConnection();
    
    try {
        // Check if default admin user exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_type = 'admin'");
        $stmt->execute();
        $adminCount = $stmt->fetchColumn();
        
        if ($adminCount == 0) {
            $adminPassword = password_hash('admin123', PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 3,
            ]);
            
            // Generate unique user_id
            $userId = 'A' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $stmt = $conn->prepare("
                INSERT INTO users (user_id, username, email, password_hash, name, user_type)
                VALUES (:user_id, 'admin', 'admin@dentalclinic.com', :password, 'System Administrator', 'admin')
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':password' => $adminPassword
            ]);
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

// Call initialization on first load
initializeDatabase();