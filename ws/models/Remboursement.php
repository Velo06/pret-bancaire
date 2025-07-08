<?php
require_once __DIR__ . '/../db.php';

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

    public static function calculerMensualite($montant, $tauxAnnuel, $dureeMois) {
        $i = $tauxAnnuel / 12;

        $annuite = $montant * ($i / (1 - pow(1 + $i, -$dureeMois)));
        
        return round($annuite, precision: 2);
    }
    
    public static function planifierRemboursements($pretId, $montant, $tauxAnnuel, $dureeMois) {
        $db = getDB();
        $pret = Pret::getPretDetails($pretId);
    
        if (empty($pret['date_debut'])) {
            throw new Exception("La date de début du prêt n'est pas définie");
        }
    
        try {
            $dateDebut = new DateTime($pret['date_debut']);
            $dateFin = new DateTime($pret['date_fin']);
            $dureeCalculee = $dateDebut->diff($dateFin)->m + ($dateDebut->diff($dateFin)->y * 12);
            
            if ($dureeCalculee != $dureeMois) {
                error_log("Avertissement: Durée fournie ($dureeMois mois) ne correspond pas à la durée réelle ($dureeCalculee mois)");
                $dureeMois = $dureeCalculee;
            }

            $tauxAssurance = (float)$pret['taux_assurance_annuel'];
            $assurance = Pret::calculerAssuranceMensuelle($montant, $tauxAssurance);
            $mensualite = self::calculerMensualite($montant, $tauxAnnuel, $dureeMois) + $assurance;

            $delaiMois = (int)$pret['delai_premier_remboursement_mois'];
            $dateRemboursement = clone $dateDebut;
            
            if ($delaiMois > 0) {
                $dateRemboursement->add(new DateInterval("P{$delaiMois}M"));
            }

            $stmt = $db->prepare("INSERT INTO historique_remboursement 
                                (pret_id, montant_rembourse, date_remboursement, etat_remboursement) 
                                VALUES (?, ?, ?, ?)");

            for ($i = 1; $i <= $dureeMois; $i++) {
                $dateRemboursement->add(new DateInterval('P1M'));

                if ($dateRemboursement->format('d') != $dateDebut->format('d')) {
                    $dateRemboursement->modify('last day of previous month');
                }
    
                $stmt->execute([
                    $pretId,
                    round($mensualite, 2),
                    $dateRemboursement->format('Y-m-d'),
                    0
                ]);
            }
    
            return true;
        } catch (Exception $e) {
            error_log("ERREUR dans planifierRemboursements (PretID: $pretId): " . $e->getMessage());
            throw $e;
        }
    }

    public static function getRemboursementsByPret($pretId) {

        $db = getDB();
        $stmt = $db->prepare("SELECT 
                            id,
                            pret_id,
                            montant_rembourse,
                            date_remboursement,
                            etat_remboursement,
                            CASE 
                                WHEN etat_remboursement = 1 THEN 'Payé'
                                ELSE 'Non payé'
                            END AS statut
                        FROM historique_remboursement
                        WHERE pret_id = ?
                        ORDER BY date_remboursement ASC");
        $stmt->execute([$pretId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
