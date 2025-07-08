<?php
require_once __DIR__ . '/../controllers/IntererEFController.php';

Flight::route('GET /interet_EF', ['IntererEFController', 'getInteretEtablissement']);
Flight::route('GET /interet_EF_filtre', ['IntererEFController', 'getInteretEtablissementByFiltre']);
Flight::route('GET /allPret/interet_EF', ['IntererEFController', 'getInteretEtablissementSimulation']);

// Flight::route('POST /simuler_pret', ['IntererEFController', 'simulerPret']);
Flight::route('POST /simuler_pret', function () {
    $body = Flight::request()->data;
    
    $montant = floatval($body['montant']);
    $duree = intval($body['duree_mois']);
    $tauxAnnuel = floatval($body['taux_annuel']);
    $assurance = floatval($body['assurance'] ?? 0);
    $dateDebut = new DateTime($body['date_debut']);

    if (!$montant || !$duree || !$tauxAnnuel || !$dateDebut) {
        Flight::halt(400, json_encode(['message' => 'Données invalides']));
    }

    $mensualite = $montant * (($tauxAnnuel / 100) / 12) / (1 - pow(1 + ($tauxAnnuel / 100 / 12), -$duree));
    $assuranceMensuelle = ($montant * $assurance / 100) / 12;

    $capitalRestant = $montant;
    $resultats = [];

    for ($i = 1; $i <= $duree; $i++) {
        $interet = $capitalRestant * ($tauxAnnuel / 100) / 12;
        $amortissement = $mensualite - $interet;
        $capitalRestant -= $amortissement;

        $resultats[] = [
            'date_mois' => $dateDebut->format('Y-m-d'),
            'mensualite' => round($mensualite, 2),
            'assurance' => round($assuranceMensuelle, 2),
            'total_paiement' => round($mensualite + $assuranceMensuelle, 2),
            'interet' => round($interet, 2),
            'amortissement' => round($amortissement, 2),
            'capital_restant' => max(round($capitalRestant, 2), 0)
        ];

        $dateDebut->modify('+1 month');
    }

    Flight::json([
        'mensualite' => round($mensualite),
        'assurance_mensuelle' => round($assuranceMensuelle),
        'total_interet' => round(array_sum(array_column($resultats, 'interet'))),
        'total_assurance' => round($assuranceMensuelle * $duree),
        'total_rembourse' => round(($mensualite + $assuranceMensuelle) * $duree),
        'details' => $resultats
    ]);
});
