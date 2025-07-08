<?php
require_once __DIR__ . '/../controllers/EmployeController.php';

Flight::route('POST /connexion', ['EmployeController', 'login']);