<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /clients/@id/prets', ['PretController', 'getByClientId']);
Flight::route('GET /prets/@id', ['PretController', 'getDetails']);
Flight::route('PUT /prets/@id/valider', ['PretController', 'validerPret']);