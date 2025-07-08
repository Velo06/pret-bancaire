<?php
require_once __DIR__ . '/../models/Remboursement.php';

class RemboursementController
{
    public static function effectuerRemboursement()
    {
        $data = Flight::request()->data;
        $db = getDB();

        $montant = floatval($data->montant);
        if (!$montant) {
            Flight::halt(400, json_encode(['message' => 'Montant invalide']));
        }

        $pret = Remboursement::getPret($db, $data->pret_id);
        if (!$pret) {
            Flight::halt(404, json_encode(['message' => 'Prêt introuvable', 'pret_id' => $data->pret_id]));
        }

        $total_rembourse = Remboursement::getTotalRembourse($db, $data->pret_id);
        $nouveau_total = $total_rembourse + $montant;

        if ($nouveau_total > $pret['montant_emprunt']) {
            Flight::halt(400, json_encode(['message' => 'Montant remboursé dépasse le montant emprunté']));
        }

        Remboursement::enregistrerRemboursement($db, $data->pret_id, $montant);

        $etat = ($nouveau_total == $pret['montant_emprunt']) ? 5 : 4;
        Remboursement::mettreAJourEtatPret($db, $etat, $data->pret_id);

        Remboursement::incrementerSoldeEtablissement($db, $montant);

        Flight::json(['message' => 'Remboursement effectué avec succès']);
    }
}
