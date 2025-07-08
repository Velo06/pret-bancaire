<?php
require_once __DIR__ . '/../models/PretComparaison.php';

class PretComparaisonController {
    public static function create() {
        $data = Flight::request()->data;

        // Vérification des champs obligatoires
        $requiredFields = ['client', 'type_pret_id', 'montant_emprunt', 'date_debut', 'date_fin', 'id_etat_validation', 'mensualite', 'assurance_mensuelle', 'total_interets', 'total_assurances', 'montant_total_rembourse'];
        foreach ($requiredFields as $field) {
            if (!isset($data->$field)) {
                Flight::halt(400, json_encode(["message" => "Champ manquant : $field"]));
                return;
            }
        }

        // Enregistrement dans la base
        $id = PretComparaison::create($data);
        Flight::json(['message' => 'Prêt enregistré avec succès', 'id' => $id]);
    }
}
