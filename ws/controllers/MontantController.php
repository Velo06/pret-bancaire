<?php
require_once __DIR__ . '/../models/MontantModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

class MontantController {
    public static function getMontantDisponibleParMois() {
        $debut = $_GET['debut'] ?? null;
        $fin = $_GET['fin'] ?? null;

        // Vérification des paramètres
        if (!$debut || !$fin) {
            echo json_encode([
                "success" => false,
                "message" => "Veuillez fournir une date de début et de fin au format YYYY-MM."
            ]);
            return;
        }

        try {
            // 1. Obtenir le solde initial (à adapter si tu veux utiliser la date du début)
            $soldeRow = MontantModel::getSoldeActuelle();
            $soldeInitial = floatval($soldeRow['solde_actuelle'] ?? 0);

            // 2. Récupérer les montants prêtés et remboursés par mois
            $rows = MontantModel::calculMontantDisponible($debut, $fin);

            // 3. Calculer le solde par mois
            $solde = $soldeInitial;
            foreach ($rows as &$r) {
                $r['pret_mensuel'] = floatval($r['pret_mensuel']);
                $r['remboursement_mensuel'] = floatval($r['remboursement_mensuel']);
                $solde = $solde - $r['pret_mensuel'] + $r['remboursement_mensuel'];
                $r['solde_disponible'] = round($solde, 2);
            }

            echo json_encode([
                "success" => true,
                "data" => $rows,
                "solde_initial" => $soldeInitial
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Erreur : " . $e->getMessage()
            ]);
        }
    }
}
