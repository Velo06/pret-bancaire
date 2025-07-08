<?php
require_once __DIR__ . '/../db.php';
class SimulationModel {
    public static function calculerPret($montant, $duree, $tauxAnnuel, $tauxAssurance, $dateDebut) {
        $tauxMensuel = $tauxAnnuel / 12 / 100;
        $mensualite = $montant * ($tauxMensuel / (1 - pow(1 + $tauxMensuel, -$duree)));
        $assuranceMensuelle = $tauxAssurance > 0 ? round(($montant * $tauxAssurance / 100) / $duree, 2) : 0;

        $capitalRestant = $montant;
        $details = [];
        $totalInteret = 0;
        $totalAssurance = 0;

        for ($i = 0; $i < $duree; $i++) {
            $interet = $capitalRestant * $tauxMensuel;
            $amortissement = $mensualite - $interet;
            $capitalRestant -= $amortissement;

            $paiementTotal = $mensualite + $assuranceMensuelle;

            $details[] = [
                "date_mois" => $dateDebut->format("Y-m-d"),
                "mensualite" => round($mensualite, 2),
                "assurance" => round($assuranceMensuelle, 2),
                "total_paiement" => round($paiementTotal, 2),
                "interet" => round($interet, 2),
                "amortissement" => round($amortissement, 2),
                "capital_restant" => max(round($capitalRestant, 2), 0)
            ];

            $totalInteret += $interet;
            $totalAssurance += $assuranceMensuelle;
            $dateDebut->modify("+1 month");
        }

        return [
            "mensualite" => round($mensualite, 2),
            "assurance_mensuelle" => round($assuranceMensuelle, 2),
            "total_interet" => round($totalInteret, 2),
            "total_assurance" => round($totalAssurance, 2),
            "total_rembourse" => round(($mensualite + $assuranceMensuelle) * $duree, 2),
            "details" => $details
        ];
    }
}
