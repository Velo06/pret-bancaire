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

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPretWithDetails($pretId) {
        $db = getDB();
        $sql = "SELECT 
                p.*,
                c.nom AS client_nom, 
                c.email AS client_email,
                c.telephone AS client_telephone,
                rc.nom_role,
                c.date_inscription,
                tp.nom AS type_pret,
                tp.taux_interet_annuel,
                p.taux_assurance_annuel,
                ev.nom_etat_validation
            FROM pret p
            JOIN clients c ON p.client = c.id
            JOIN role_clients rc ON c.role = rc.id
            JOIN type_pret tp ON p.type_pret_id = tp.id
            JOIN etat_validation ev ON p.id_etat_validation = ev.id
            WHERE p.id = ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$pretId]);
        
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pret) {
            // Calcul de la durée en mois
            $date_debut = new DateTime($pret['date_debut']);
            $date_fin = new DateTime($pret['date_fin']);
            $interval = $date_debut->diff($date_fin);
            $pret['duree_mois'] = ($interval->y * 12) + $interval->m;
            
            // Formatage des dates
            $pret['date_debut_formatted'] = $date_debut->format('d/m/Y');
            $pret['date_fin_formatted'] = $date_fin->format('d/m/Y');
            $pret['date_creation_formatted'] = (new DateTime($pret['date_creation']))->format('d/m/Y');
            
            // Formatage du montant
            $pret['montant_formatted'] = number_format($pret['montant_emprunt'], 2, ',', ' ');
        }
        
        return $pret;
    }

    public static function getNomEtablissement() {
        $db = getDB();
        $sql = "SELECT nom FROM etablissement_financier";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pret;
    }
     
}