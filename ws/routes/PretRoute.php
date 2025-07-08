<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /mensuels', ['PretController', 'getInterets']);
Flight::route('GET /pret', ['PretController', 'getListePret']);
Flight::route('GET /export/@id', ['PretController', 'export']);