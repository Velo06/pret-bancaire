<?php
require_once __DIR__ . '/../controllers/IntererEFController.php';

Flight::route('GET /interet_EF', ['IntererEFController', 'getInteretEtablissement']);
