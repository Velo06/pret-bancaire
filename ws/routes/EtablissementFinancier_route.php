<?php
require_once __DIR__ . '/../controllers/EtablissementFinancierController.php';

Flight::route('GET /etat_fond', ['EtablissementFinancierController', 'getEtatFond']);
Flight::route('POST /creation_fond', ['EtablissementFinancierController', 'creationFond']);
