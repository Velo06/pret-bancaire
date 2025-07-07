<?php

class Remboursement
{
    public static function getPret($db, $pretId)
    {
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$pretId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTotalRembourse($db, $pretId)
    {
        $stmt = $db->prepare("SELECT COALESCE(SUM(montant_rembourse), 0) FROM historique_remboursement WHERE pret_id = ?");
        $stmt->execute([$pretId]);
        return $stmt->fetchColumn();
    }

    public static function enregistrerRemboursement($db, $pretId, $montant)
    {
        $stmt = $db->prepare("INSERT INTO historique_remboursement (pret_id, montant_rembourse) VALUES (?, ?)");
        $stmt->execute([$pretId, $montant]);
    }

    public static function mettreAJourEtatPret($db, $etatId, $pretId)
    {
        $stmt = $db->prepare("UPDATE pret SET id_etat_validation = ? WHERE id = ?");
        $stmt->execute([$etatId, $pretId]);
    }

    public static function incrementerSoldeEtablissement($db, $montant)
    {
        $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle + ? WHERE id = 1");
        $stmt->execute([$montant]);
    }
}
