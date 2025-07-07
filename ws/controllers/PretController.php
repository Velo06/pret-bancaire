<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../models/Rembourssement.php';

class PretController {
    public static function getByClientId($clientId) {
        $prets = Pret::getByClientId($clientId);
        Flight::json($prets);
    }

    public static function getDetails($pretId) {
        $pret = Pret::getPretDetails($pretId);
        if (!$pret) {
            Flight::halt(404, json_encode(['message' => 'Prêt non trouvé']));
        }
        
        $pret['historique_remboursements'] = Rembourssement::getRemboursementsByPret($pretId);
        Flight::json($pret);
    }

    public static function validerPret($pretId) {
        $pret = Pret::getPretDetails($pretId);
        if (!$pret) {
            Flight::halt(404, json_encode(['success' => false, 'message' => 'Prêt non trouvé']));
        }

        if (!EtablissementFinancier::checkSoldeSuffisant($pret['montant_emprunt'])) {
            Flight::halt(400, json_encode([
                'success' => false, 
                'message' => 'Solde insuffisant pour valider ce prêt'
            ]));
        }

        $db = getDB();
        $db->beginTransaction();
    
        try {
            $debited = EtablissementFinancier::debiterSolde($pret['montant_emprunt']);
            if (!$debited) {
                throw new Exception('Échec du débit du solde');
            }

            $updated = Pret::updateStatut($pretId, 2);
            if (!$updated) {
                throw new Exception('Échec de la mise à jour du statut');
            }

            $montant = (float)$pret['montant_emprunt'];
            $taux = (float)$pret['taux_interet_annuel'];
            $duree = (int)$pret['duree_max_mois'];
            
            Rembourssement::planifierRemboursements(
                $pretId,
                $montant,
                $taux,
                $duree
            );
    
            $db->commit();
    
            Flight::json([
                'success' => true, 
                'message' => 'Prêt validé et solde débité avec succès',
                'nouveau_solde' => EtablissementFinancier::getSoldeActuel()
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            Flight::halt(500, json_encode([
                'success' => false, 
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ]));
        }
    }
}