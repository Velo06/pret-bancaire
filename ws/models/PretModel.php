<?php
require_once __DIR__ . '/../db.php';

class PretModel {
    public static function getInteretsMensuels($debut, $fin) {
        $db = getDB();
    
        // Déclarer les variables pour le CTE
        $db->prepare("SET @date_debut = :debut, @date_fin = :fin")->execute([
            ':debut' => $debut,
            ':fin' => $fin
        ]);
    
        // Requête principale
        $sql = "WITH RECURSIVE mois_periode AS (
                SELECT DATE_FORMAT(@date_debut, '%Y-%m-01') AS mois
                UNION ALL
                SELECT DATE_ADD(mois, INTERVAL 1 MONTH)
                FROM mois_periode
                WHERE mois < @date_fin
            )
            SELECT 
                DATE_FORMAT(mp.mois, '%Y-%m') AS mois_annee,
                SUM( 
                    (p.montant_emprunt * tp.taux_interet_annuel) /
                    NULLIF(TIMESTAMPDIFF(MONTH, p.date_debut, p.date_fin), 0)
                ) AS interets_mensuels,
                COUNT(p.id) AS nombre_prets
            FROM mois_periode mp
            JOIN pret p
                ON mp.mois BETWEEN DATE_FORMAT(p.date_debut, '%Y-%m-01') AND DATE_FORMAT(p.date_fin, '%Y-%m-01')
            JOIN type_pret tp ON tp.id = p.type_pret_id
            GROUP BY mois_annee
            ORDER BY mois_annee;
        ";
    
        $stmt = $db->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     
}