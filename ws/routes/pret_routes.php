<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretComparaisonController.php';

Flight::route('POST /creation_pret', ['PretController', 'createPret']);

Flight::route('POST /prets_comparaison', ['PretComparaisonController', 'create']);
Flight::route('GET /list_pret_simuler', ['PretComparaisonController', 'listPretSimuler']);
Flight::route('GET /prets_comparaison/@id', function($id) {
    require_once __DIR__ . '/../models/PretComparaison.php';
    $pret = PretComparaison::getById($id);
    if ($pret) {
        Flight::json($pret);
    } else {
        Flight::halt(404, 'Prêt non trouvé');
    }
});
