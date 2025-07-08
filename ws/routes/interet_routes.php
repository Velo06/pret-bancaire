<?php
require_once __DIR__ . '/../controllers/IntererEFController.php';

Flight::route('GET /interet_EF', ['IntererEFController', 'getInteretEtablissement']);
Flight::route('GET /interet_EF_filtre', ['IntererEFController', 'getInteretEtablissementByFiltre']);
Flight::route('GET /allPret/interet_EF', ['IntererEFController', 'getInteretEtablissementSimulation']);
