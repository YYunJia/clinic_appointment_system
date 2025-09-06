<?php

function getDBConnection() {
    $servername = "192.168.1.29";
    $username = "remote_user";
    $password = "dentalclinic123";
    $dbname = "dental_clinic";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;  // Return the connection object
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}
