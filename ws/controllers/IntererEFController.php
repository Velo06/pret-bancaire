<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class IntererEFController
{

    public static function getInteretEtablissementByFiltre()
    {
        $db = getDB();

        // Récupérer les filtres de date s'ils sont fournis
        $moisDebut = isset($_GET['mois_debut']) ? $_GET['mois_debut'] : null; // ex: "2025-01"
        $moisFin   = isset($_GET['mois_fin'])   ? $_GET['mois_fin']   : null; // ex: "2025-05"

        // Préparer la base de la requête
        $sql = "SELECT * FROM pret WHERE id_etat_validation = 2";
        $params = [];

        // Ajouter les conditions de date (date_debut entre moisDebut et moisFin)
        if ($moisDebut) {
            $sql .= " AND date_debut >= ?";
            $params[] = $moisDebut . "-01"; // ex: "2025-01-01"
        }
        if ($moisFin) {
            $sql .= " AND date_fin <= ?";
            $params[] = $moisFin . "-31"; // ex: "2025-05-31"
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultat = [];

        foreach ($prets as $pret) {
            $typePret = TypePret::getById($pret['type_pret_id']);
            $capitalRestant = $pret['montant_emprunt'];
            $tauxAnnuel = $typePret['taux_interet_annuel'];
            $tauxMensuel = $tauxAnnuel / 12 / 100;

            $dateDebut = new DateTime($pret['date_debut']);
            $dateFin = new DateTime($pret['date_fin']);
            $interval = $dateDebut->diff($dateFin);
            $nbMois = ($interval->y * 12) + $interval->m;

            $mensualite = $capitalRestant * ($tauxMensuel / (1 - pow(1 + $tauxMensuel, -$nbMois)));
            $datePaiement = clone $dateDebut;

            for ($i = 1; $i <= $nbMois; $i++) {
                $interet = $capitalRestant * $tauxMensuel;
                $amortissement = $mensualite - $interet;
                $capitalRestant -= $amortissement;

                $resultat[] = [
                    'pret_id' => $pret['id'],
                    'client_id' => $pret['client'],
                    'date_mois' => $datePaiement->format('Y-m-d'),
                    'interet' => round($interet, 2),
                    'amortissement' => round($amortissement, 2),
                    'mensualite' => round($mensualite, 2),
                    'capital_restant' => max(round($capitalRestant, 2), 0)
                ];

                $datePaiement->modify('+1 month');
            }
        }

        Flight::json($resultat);
    }

    public static function getInteretEtablissement()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret WHERE id_etat_validation = 2 ORDER BY date_debut");
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultat = [];

        foreach ($prets as $pret) {
            $typePret = TypePret::getById($pret['type_pret_id']);
            $capitalRestant = $pret['montant_emprunt'];
            $tauxAnnuel = $typePret['taux_interet_annuel'];
            $tauxMensuel = $tauxAnnuel / 12 / 100;

            $dateDebut = new DateTime($pret['date_debut']);
            $dateFin = new DateTime($pret['date_fin']);
            $interval = $dateDebut->diff($dateFin);
            $nbMois = ($interval->y * 12) + $interval->m;

            $mensualite = $capitalRestant * ($tauxMensuel / (1 - pow(1 + $tauxMensuel, -$nbMois)));
            $datePaiement = clone $dateDebut;

            for ($i = 1; $i <= $nbMois; $i++) {
                $interet = $capitalRestant * $tauxMensuel;
                $amortissement = $mensualite - $interet;
                $capitalRestant -= $amortissement;

                $resultat[] = [
                    'pret_id' => $pret['id'],
                    'client_id' => $pret['client'],
                    'date_mois' => $datePaiement->format('Y-m-d'),
                    'interet' => round($interet, 2),
                    'amortissement' => round($amortissement, 2),
                    'mensualite' => round($mensualite, 2),
                    'capital_restant' => max(round($capitalRestant, 2), 0)
                ];

                $datePaiement->modify('+1 month');
            }
        }

        // ✅ À mettre en dehors de la boucle
        Flight::json($resultat);
    }
}
