<?php

function getDBConnection() {
    $servername = "175.142.243.200";
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
