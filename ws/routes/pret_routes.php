<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretComparaisonController.php';

Flight::route('POST /creation_pret', ['PretController', 'createPret']);
Flight::route('POST /prets_comparaison', ['PretComparaisonController', 'create']);
