<?php
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__.'/../models/PDF.php';

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

    public static function getListePret() {
        $pret = PretModel::getAll();
        Flight::json($pret);
    }

    public static function export($pretId) {
        try {
            $pret = PretModel::getPretWithDetails($pretId);
            $etab = PretModel::getNomEtablissement();
            
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }

            $pdf = new PDF();
            $pdf->generatePretPDF($pret,$etab['nom']);
            $pdf->Output('D', 'pret_'.$pretId.'.pdf');
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            Flight::json(['error' => 'Erreur de génération PDF'], 500);
        }
    }
    
}