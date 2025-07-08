<?php
require_once __DIR__ . '/../db.php';

class Client {
    public static function getAll() {
        $db = getDB();
        $query = "SELECT c.*, r.nom_role as role_nom, s.status_role as statut_nom 
                  FROM clients c
                  JOIN role_clients r ON c.role = r.id
                  JOIN status_clients s ON c.statut = s.id";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $query = "SELECT c.*, r.nom_role as role_nom, s.status_role as statut_nom 
                  FROM clients c
                  JOIN role_clients r ON c.role = r.id
                  JOIN status_clients s ON c.statut = s.id
                  WHERE c.id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO clients (nom, username, email, telephone, role, statut) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom, 
            $data->username, 
            $data->email, 
            $data->telephone, 
            1, 
            1
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE clients 
                             SET nom = ?, username = ?, email = ?, telephone = ?, role = ?, statut = ? 
                             WHERE id = ?");
        $stmt->execute([
            $data->nom, 
            $data->username, 
            $data->email, 
            $data->telephone, 
            $data->role, 
            $data->statut,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM clients WHERE id = ?");
        $stmt->execute([$id]);
    }
}