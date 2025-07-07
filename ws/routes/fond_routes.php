<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';

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
