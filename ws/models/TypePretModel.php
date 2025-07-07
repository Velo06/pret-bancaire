<?php
require_once __DIR__ . '/../db.php';

class TypePretModel {

    public static function createTypePret($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_pret (nom, taux_interet_annuel, duree_max_mois, montant_max_pres) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->tauxInteret/100, $data->dureeMax, $data->montantMax]);
        return $db->lastInsertId();
    }

}
