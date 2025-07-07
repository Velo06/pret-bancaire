<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController
{
    public static function createPret()
    {
        $data = Flight::request()->data;
        $db = getDB();

        $solde = Pret::verifierSoldeDisponible($db);
        if ($data->montant_emprunt > $solde) {
            Flight::json(['message' => 'Fonds insuffisants dans l’établissement financier']);
            return;
        }

        if (Pret::clientADejaPret($db, $data->client, $data->type_pret_id) > 0) {
            Flight::halt(400, json_encode(['message' => 'Le client possède déjà un prêt actif de ce type']));
        }

        $revenu = Pret::getRevenuClient($db, $data->client);
        if (!$revenu) {
            Flight::halt(404, json_encode(['message' => 'Client introuvable']));
        }

        $plafond = $revenu * 0.33;
        if ($data->montant_emprunt > $plafond) {
            Flight::json(['message' => 'Le montant demandé dépasse 33% du revenu du client']);
            return;
        }

        $pretId = Pret::creerPret($db, $data->client, $data->type_pret_id, $data->montant_emprunt, $data->date_debut);
        Pret::mettreAJourSolde($db, $data->montant_emprunt);

        Flight::json(['message' => 'Prêt créé avec succès', 'id' => $pretId]);
    }
}
