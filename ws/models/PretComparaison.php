<?php
require_once __DIR__ . '/../db.php';

class PretComparaison
{
    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO pret_a_comparer 
            (client, type_pret_id, montant_emprunt, date_debut, date_fin, id_etat_validation, date_creation, mensualite, assurance_mensuelle, total_interets, total_assurances, montant_total_rembourse)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data->client,
            $data->type_pret_id,
            $data->montant_emprunt,
            $data->date_debut,
            $data->date_fin,
            $data->id_etat_validation,
            $data->mensualite,
            $data->assurance_mensuelle,
            $data->total_interets,
            $data->total_assurances,
            $data->montant_total_rembourse
        ]);

        return $db->lastInsertId();
    }

    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret_a_comparer ORDER BY date_creation DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret_a_comparer WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE pret_a_comparer SET 
                client = ?,
                type_pret_id = ?,
                montant_emprunt = ?,
                date_debut = ?,
                date_fin = ?,
                id_etat_validation = ?,
                mensualite = ?,
                assurance_mensuelle = ?,
                total_interets = ?,
                total_assurances = ?,
                montant_total_rembourse = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data->client,
            $data->type_pret_id,
            $data->montant_emprunt,
            $data->date_debut,
            $data->date_fin,
            $data->id_etat_validation,
            $data->mensualite,
            $data->assurance_mensuelle,
            $data->total_interets,
            $data->total_assurances,
            $data->montant_total_rembourse,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret_a_comparer WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
