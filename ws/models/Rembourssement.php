<?php
require_once __DIR__ . '/../db.php';

class Rembourssement {
    public static function calculerMensualite($montant, $tauxAnnuel, $dureeMois) {
        $i = $tauxAnnuel / 12;

        $annuite = $montant * ($i / (1 - pow(1 + $i, -$dureeMois)));
        
        return round($annuite, precision: 2);
    }
    
    public static function planifierRemboursements($pretId, $montant, $tauxAnnuel, $dureeMois) {
        $db = getDB();
        
        // Log des paramètres reçus
        error_log("Planification remboursement - PretID: $pretId, Montant: $montant, Taux: $tauxAnnuel, Durée: $dureeMois mois");
    
        try {
            $mensualite = self::calculerMensualite($montant, $tauxAnnuel, $dureeMois);
            error_log("Mensualité calculée: $mensualite");
    
            $date = new DateTime();
            $insertQuery = "INSERT INTO historique_remboursement 
                           (pret_id, montant_rembourse, date_remboursement, etat_remboursement) 
                           VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($insertQuery);
            error_log("Requête préparée: $insertQuery");
            
            for ($i = 1; $i <= $dureeMois; $i++) {
                $date->add(new DateInterval('P1M'));
                $dateRemb = $date->format('Y-m-d');
                
                error_log("Tentative insertion - Mois $i: $dateRemb, Montant: $mensualite");
                
                $success = $stmt->execute([
                    $pretId,
                    $mensualite,
                    $dateRemb,
                    false
                ]);
                
                if (!$success) {
                    $error = $stmt->errorInfo();
                    throw new Exception("Erreur SQL: " . json_encode($error));
                }
                
                error_log("Insertion réussie pour le mois $i");
            }
            
            return true;
        } catch (Exception $e) {
            error_log("ERREUR dans planifierRemboursements: " . $e->getMessage());
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