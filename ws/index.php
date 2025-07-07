<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'vendor/autoload.php';
require 'db.php';

Flight::route('GET /etudiants', function () {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM etudiant");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

Flight::route('GET /etudiants/@id', function ($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json($stmt->fetch(PDO::FETCH_ASSOC));
});

Flight::route('POST /etudiants', function () {
    $data = Flight::request()->data;
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
    Flight::json(['message' => 'Étudiant ajouté', 'id' => $db->lastInsertId()]);
});

Flight::route('PUT /etudiants/@id', function ($id) {
    $data = Flight::request()->data;
    $db = getDB();
    $stmt = $db->prepare("UPDATE etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
    $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $id]);
    Flight::json(['message' => 'Étudiant modifié']);
});

Flight::route('DELETE /etudiants/@id', function ($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM etudiant WHERE id = ?");
    $stmt->execute([$id]);
    Flight::json(['message' => 'Étudiant supprimé']);
});

// ------------------ Creation fond
Flight::route('GET /etat_fond', function () {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM etablissement_financier WHERE id = ?");
    // 1 par defaut satrie 1 iany ny etablissement financier
    $stmt->execute([1]);

    Flight::json($stmt->fetch(PDO::FETCH_ASSOC));
});

Flight::route('POST /creation_fond', function () {
    $data = Flight::request()->data;
    $montant = isset($data->montant) ? floatval($data->montant) : 0;
    $db = getDB();

    if ($montant > 0) {
        // Historique
        $stmt = $db->prepare("INSERT INTO historique_emprunt (montant) VALUES (?)");
        $stmt->execute([$montant]);

        // Mise à jour solde
        $stmtUpdate = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle + ? WHERE id = 1");
        $stmtUpdate->execute([$montant]);

        Flight::json(['message' => 'Montant bien enregistré']);
    } else {
        Flight::json(['message' => 'Montant invalide']);
    }
});

Flight::start();
