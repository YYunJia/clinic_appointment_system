<?php

function getDBConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "badminton_tournament";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;  // Return the connection object
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}
