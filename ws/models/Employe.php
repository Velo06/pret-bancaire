<?php
require_once __DIR__ . '/../db.php';

class Employe {
    public static function authenticate($username, $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, pseudo FROM employes WHERE pseudo = ? AND mot_de_passe = ?");
        $stmt->execute([$username, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}