<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__.'/../models/PDF.php';

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
        
        $pret['historique_remboursements'] = Remboursement::getRemboursementsByPret($pretId);
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

            $dateDebut = new DateTime($pret['date_debut']);
            $dateFin = new DateTime($pret['date_fin']);
            $montant = (float)$pret['montant_emprunt'];
            $taux = (float)$pret['taux_interet_annuel'];
            $interval = $dateDebut->diff($dateFin);
            $dureeMois = $interval->y * 12 + $interval->m;
        
        // Si la durée est inférieure à 1 mois, on met au moins 1 mois
        $dureeMois = max(1, $dureeMois); 
            
            Remboursement::planifierRemboursements(
                $pretId,
                $montant,
                $taux,
                $dureeMois
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
    
    public static function createPret()
    {
        $data = Flight::request()->data;
        $db = getDB();

        $solde = Pret::verifierSoldeDisponible($db);
        if ($data->montant_emprunt > $solde) {
            Flight::json(['message' => 'Fonds insuffisants dans l’établissement financier']);
            return;
        }

        if (Pret::clientADejaPret($db, $data->client, $data->type_pret_id) > 0) {
            Flight::halt(400, json_encode(['message' => 'Le client possède déjà un prêt actif de ce type']));
        }

        $revenu = Pret::getRevenuClient($db, $data->client);
        if (!$revenu) {
            Flight::halt(404, json_encode(['message' => 'Client introuvable']));
        }

        $plafond = $revenu * 0.33;
        if ($data->montant_emprunt > $plafond) {
            Flight::json(['message' => 'Le montant demandé dépasse 33% du revenu du client']);
            return;
        }

        $pretId = Pret::creerPret($db, $data->client, $data->type_pret_id, $data->montant_emprunt, $data->date_debut);
        Pret::mettreAJourSolde($db, $data->montant_emprunt);

        Flight::json(['message' => 'Prêt créé avec succès', 'id' => $pretId]);
    }
    public static function getInterets() {
        $debut = Flight::request()->query['debut'] ?? date('Y-m', strtotime('-1 year'));
        $fin = Flight::request()->query['fin'] ?? date('Y-m');
    
        $debut .= '-01'; // compléter format Y-m → Y-m-01
        $fin .= '-01';
    
        $interets = PretModel::getInteretsMensuels($debut, $fin);
    
        Flight::json([
            'success' => true,
            'data' => $interets,
            'periode' => ['debut' => $debut, 'fin' => $fin]
        ]);
    }

    public static function getListePret() {
        $pret = PretModel::getAll();
        Flight::json($pret);
    }

    public static function export($pretId) {
        try {
            $pret = PretModel::getPretWithDetails($pretId);
            $etab = PretModel::getNomEtablissement();
            
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }

            $pdf = new PDF();
            $pdf->generatePretPDF($pret,$etab['nom']);
            $pdf->Output('D', 'pret_'.$pretId.'.pdf');
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            Flight::json(['error' => 'Erreur de génération PDF'], 500);
        }
    }
    
}
