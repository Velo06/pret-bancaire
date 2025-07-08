<?php
require_once __DIR__ . '/../db.php';

class MontantModel {
    public static function calculMontantDisponible($debut, $fin) {
        $moisListe = [];
        $start = new DateTime("$debut-01");
        $end = new DateTime("$fin-01");
    
        while ($start <= $end) {
            $moisListe[] = $start->format('Y-m');
            $start->modify('+1 month');
        }
    
        $db = getDB();
    
        // 1. Récupérer tous les prêts par mois
        $stmtPret = $db->prepare("SELECT DATE_FORMAT(date_debut, '%Y-%m') AS mois, SUM(montant_emprunt) AS total_pret
            FROM pret
            GROUP BY mois");
        $stmtPret->execute();
        $prets = $stmtPret->fetchAll(PDO::FETCH_KEY_PAIR); // [mois => total_pret]
    
        // 2. Récupérer tous les remboursements par mois
        $stmtRemb = $db->prepare("SELECT DATE_FORMAT(date_remboursement, '%Y-%m') AS mois, SUM(montant_rembourse) AS total_rembourse
            FROM historique_remboursement
            WHERE etat_remboursement = TRUE
            GROUP BY mois");
        $stmtRemb->execute();
        $remboursements = $stmtRemb->fetchAll(PDO::FETCH_KEY_PAIR); // [mois => total_rembourse]
    
        // 3. Fusionner les données
        $resultats = [];
    
        foreach ($moisListe as $mois) {
            $resultats[] = [
                'mois' => $mois,
                'pret_mensuel' => isset($prets[$mois]) ? $prets[$mois] : 0,
                'remboursement_mensuel' => isset($remboursements[$mois]) ? $remboursements[$mois] : 0,
            ];
        }
    
        return $resultats;
    }
    

    public static function getSoldeActuelle() {
        $db = getDB();
        $sql = "SELECT solde_actuelle FROM etablissement_financier WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([1]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    
    
}
