<?php

require_once __DIR__ . '/../controller/dentistsController.php';

$controller = new dentistsController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
        $controller->getAllDentist();

}
