<?php
require_once __DIR__ . '/../controllers/IntererEF.php';

Flight::route('GET /interet_EF', ['IntererEF', 'getInteretEtablissement']);