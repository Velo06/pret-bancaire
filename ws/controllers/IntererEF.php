<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class IntererEF
{
    public static function getInteretEtablissement()
    {
        // tableau deux dimension
        // 
        // [
        //      [capital] [interet] [capital remboursé] [capital restant a payé]
        //      [capital] [interet] [capital remboursé] [capital restant a payé]
        // ]
        // [
        //      [capital] [interet] [capital remboursé] [capital restant a payé]
        //      [capital] [interet] [capital remboursé] [capital restant a payé]
        // ]

        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret WHERE id_etat_validation = 2");
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

            // Mensualité
            $mensualite = $capitalRestant * ($tauxMensuel / (1 - pow(1 + $tauxMensuel, -$nbMois)));

            // Cloner la date de début pour incrémenter mois par mois
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

            Flight::json($resultat);
            // Flight::json($resultat);
        }
    }
}
