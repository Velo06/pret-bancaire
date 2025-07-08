<?php
require_once __DIR__ . '/../db.php';

class Pret
{

    public static function getAllPret()
    {
        $db = getDB();
        $query = "SELECT *
                  FROM pret 
                  WHERE is_pret_simuler = 0";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllPretSimuler()
    {
        $db = getDB();
        $query = "SELECT *
                  FROM pret 
                  WHERE is_pret_simuler = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updatePretSimule($id, $client, $typePretId, $montant, $dateDebut)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret 
                          SET client = ?, type_pret_id = ?, montant_emprunt = ?, date_debut = ?
                          WHERE id = ? AND is_pret_simuler = 1");
        return $stmt->execute([$client, $typePretId, $montant, $dateDebut, $id]);
    }

    public static function deletePretSimule($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE id = ? AND is_pret_simuler = 1");
        return $stmt->execute([$id]);
    }

    public static function getPretSimuleById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ? AND is_pret_simuler = 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByClientId($clientId)
    {
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

    public static function getPretDetails($pretId)
    {
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

    public static function updateStatut($pretId, $newStatutId)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET id_etat_validation = ? WHERE id = ?");
        return $stmt->execute([$newStatutId, $pretId]);
    }

    public static function getHistoriqueRemboursements($pretId)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM historique_remboursement WHERE pret_id = ? ORDER BY date_remboursement DESC");
        $stmt->execute([$pretId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculerAssuranceMensuelle($montant, $tauxAssurance)
    {
        $assurance = $montant * $tauxAssurance;
        return $assurance / 12;
    }

    public static function verifierSoldeDisponible($db)
    {
        $stmt = $db->prepare("SELECT solde_actuelle FROM etablissement_financier WHERE id = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function clientADejaPret($db, $clientId, $typePretId)
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM pret WHERE client = ? AND type_pret_id = ? AND id_etat_validation IN (1, 2)");
        $stmt->execute([$clientId, $typePretId]);
        return $stmt->fetchColumn();
    }

    public static function getRevenuClient($db, $clientId)
    {
        $stmt = $db->prepare("SELECT revenu FROM clients WHERE id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn();
    }

    public static function creerPret($db, $client, $typePretId, $montant, $dateDebut, $dateFin, $is_pret_simulation)
    {
        $stmt = $db->prepare("INSERT INTO pret (client, type_pret_id, montant_emprunt, date_debut, date_fin, id_etat_validation, date_creation, is_pret_simuler)
                              VALUES (?, ?, ?, ?, ?, 1, NOW(), ?)");
        $stmt->execute([$client, $typePretId, $montant, $dateDebut, $dateFin, $is_pret_simulation]);
        return $db->lastInsertId();
    }

    public static function mettreAJourSolde($db, $montant)
    {
        $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle - ? WHERE id = 1");
        $stmt->execute([$montant]);
    }
}
