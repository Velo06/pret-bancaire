<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'vendor/autoload.php';
require 'db.php';

require 'routes/etudiant_routes.php';
require 'routes/employes_routes.php';
require 'routes/clients_routes.php'; 
require 'routes/prets_routes.php';

Flight::start();