<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /type_pret', ['TypePretController', 'getAll']);
Flight::route('GET /type_pret/@id', ['TypePretController', 'getById']);
Flight::route('POST /type_pret', ['TypePretController', 'create']);
Flight::route('PUT /type_pret/@id', ['TypePretController', 'update']);
Flight::route('DELETE /type_pret/@id', ['TypePretController', 'delete']);
