<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier {
    public static function checkSoldeSuffisant($montant) {
        $db = getDB();
        $stmt = $db->prepare("SELECT solde_actuelle FROM etablissement_financier WHERE id = 1");
        $stmt->execute();
        $solde = $stmt->fetchColumn();
        return ($solde !== false && $solde >= $montant);
    }
    
    public static function debiterSolde($montant) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle - ? WHERE id = 1");
        return $stmt->execute([$montant]);
    }

    public static function getSoldeActuel() {
        $db = getDB();
        $stmt = $db->prepare("SELECT solde_actuelle FROM etablissement_financier WHERE id = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}