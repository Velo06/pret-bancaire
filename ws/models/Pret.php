<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function getByClientId($clientId) {
        $db = getDB();
        $query = "SELECT 
                    p.*, 
                    tp.nom AS type_pret_nom, 
                    tp.taux_interet_annuel, 
                    ev.nom_etat_validation AS etat_validation
                  FROM pret p
                  JOIN type_pret tp ON p.type_pret_id = tp.id
                  JOIN etat_validation ev ON p.id_etat_validation = ev.id
                  WHERE p.client = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    
    public static function getPretDetails($pretId) {
        $db = getDB();
        $query = "SELECT p.*, 
                         tp.nom as type_pret, 
                         tp.taux_interet_annuel,
                         tp.duree_max_mois,
                         c.nom as client_nom, 
                         ev.nom_etat_validation as statut
                  FROM pret p
                  JOIN type_pret tp ON p.type_pret_id = tp.id
                  JOIN clients c ON p.client = c.id
                  JOIN etat_validation ev ON p.id_etat_validation = ev.id
                  WHERE p.id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$pretId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function updateStatut($pretId, $newStatutId) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET id_etat_validation = ? WHERE id = ?");
        return $stmt->execute([$newStatutId, $pretId]);
    }
    
    public static function getHistoriqueRemboursements($pretId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM historique_remboursement WHERE pret_id = ? ORDER BY date_remboursement DESC");
        $stmt->execute([$pretId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculerAssuranceMensuelle($montant, $tauxAssurance) {
        $assurance = $montant * $tauxAssurance;   
        return $assurance / 12;
    }
}