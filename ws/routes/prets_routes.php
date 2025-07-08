<?php
require_once __DIR__ . '/../controllers/PretController.php';


Flight::route('GET /AllpretsSimuler', ['PretController', 'getAllPretSimuler']);
Flight::route('GET /Allprets', ['PretController', 'getAllPret']);
Flight::route('GET /clients/@id/prets', ['PretController', 'getByClientId']);
Flight::route('GET /prets/@id', ['PretController', 'getDetails']);
Flight::route('PUT /prets/@id/valider', ['PretController', 'validerPret']);

Flight::route('PUT /pret_simule/@id', ['PretController', 'updatePretSimule']);
Flight::route('DELETE /pret_simule/@id', ['PretController', 'deletePretSimule']);
Flight::route('GET /pret_simule/@id', ['PretController', 'getPretSimuleById']);
