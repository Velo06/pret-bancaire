<?php
require_once __DIR__ . '/../models/PretModel.php';

class PretController {
    public static function getInterets() {
        $debut = Flight::request()->query['debut'] ?? date('Y-m', strtotime('-1 year'));
        $fin = Flight::request()->query['fin'] ?? date('Y-m');
    
        $debut .= '-01'; // compléter format Y-m → Y-m-01
        $fin .= '-01';
    
        $interets = PretModel::getInteretsMensuels($debut, $fin);
    
        Flight::json([
            'success' => true,
            'data' => $interets,
            'periode' => ['debut' => $debut, 'fin' => $fin]
        ]);
    }
    
}