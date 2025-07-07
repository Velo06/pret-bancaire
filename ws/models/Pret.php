<?php

class Pret
{
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

    public static function creerPret($db, $client, $typePretId, $montant, $dateDebut)
    {
        $stmt = $db->prepare("INSERT INTO pret (client, type_pret_id, montant_emprunt, date_debut, id_etat_validation, date_creation)
                              VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$client, $typePretId, $montant, $dateDebut]);
        return $db->lastInsertId();
    }

    public static function mettreAJourSolde($db, $montant)
    {
        $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle - ? WHERE id = 1");
        $stmt->execute([$montant]);
    }
}
