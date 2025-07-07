<?php

class EtablissementFinancierController
{
    public static function getEtatFond()
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM etablissement_financier WHERE id = ?");
        $stmt->execute([1]); // établissement par défaut
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        Flight::json($result);
    }

    public static function creationFond()
    {
        $data = Flight::request()->data;
        $montant = isset($data->montant) ? floatval($data->montant) : 0;
        $db = getDB();

        if ($montant > 0) {
            // Historique
            $stmt = $db->prepare("INSERT INTO historique_emprunt (montant) VALUES (?)");
            $stmt->execute([$montant]);

            // Mise à jour solde
            $stmtUpdate = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle + ? WHERE id = 1");
            $stmtUpdate->execute([$montant]);

            Flight::json(['message' => 'Montant bien enregistré']);
        } else {
            Flight::json(['message' => 'Montant invalide']);
        }
    }
}
