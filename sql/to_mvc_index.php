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

// ------------------ Type pres
Flight::route('GET /type_pret', function () {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM type_pret");
    Flight::json($stmt->fetchAll(PDO::FETCH_ASSOC));
});

// ------------------ pres
Flight::route('POST /creation_pret', function () {
    $data = Flight::request()->data;
    $db = getDB();

    // Vérifie solde disponible dans l'établissement financier
    $stmt = $db->prepare("SELECT solde_actuelle FROM etablissement_financier WHERE id = ?");
    $stmt->execute([1]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $montant_ep = $result['solde_actuelle'];

    if ($data->montant_emprunt > $montant_ep) {
        Flight::json(['message' => 'Fonds insuffisants dans l’établissement financier']);
        return;
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM pret 
                      WHERE client = ? AND type_pret_id = ? AND id_etat_validation IN (1, 2)");
    $stmt->execute([$data->client, $data->type_pret_id]);
    $deja_pret = $stmt->fetchColumn();

    if ($deja_pret > 0) {
        Flight::halt(400, json_encode(['message' => 'Le client possède déjà un prêt actif de ce type']));
    }

    // Vérifie le revenu du client
    $stmt = $db->prepare("SELECT revenu FROM clients WHERE id = ?");
    $stmt->execute([$data->client]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        Flight::halt(404, json_encode(['message' => 'Client introuvable']));
    }

    $revenu_client = $client['revenu'];
    $plafond = $revenu_client * 0.33;

    if ($data->montant_emprunt > $plafond) {
        Flight::json(['message' => 'Le montant demandé dépasse 33% du revenu du client']);
        return;
    }

    // Insertion du prêt
    $stmt = $db->prepare("INSERT INTO pret (client, type_pret_id, montant_emprunt, date_debut, id_etat_validation, date_creation)
                          VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data->client,
        $data->type_pret_id,
        $data->montant_emprunt,
        $data->date_debut,
        1 // en attente
    ]);

    // Mise à jour du solde de l'établissement
    $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle - ? WHERE id = 1");
    $stmt->execute([$data->montant_emprunt]);

    Flight::json(['message' => 'Prêt créé avec succès', 'id' => $db->lastInsertId()]);
});

// Historique remboursement
Flight::route('POST /remboursement', function () {
    $data = Flight::request()->data;
    $db = getDB();
    $montant = floatval($data->montant);

    if (!$montant) {
        Flight::halt(400, json_encode(['message' => 'Champ(s) manquant(s) ou invalide(s)']));
    }

    // Vérifie si le prêt existe
    $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
    $stmt->execute([$data->pret_id]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        Flight::halt(404, json_encode(['message' => 'Prêt introuvable', 'pret_id' =>  $data->pret_id]));
    }

    // Vérifie que le prêt est validé
    // if (!in_array($pret['id_etat_validation'], [2, 4])) { // Validé ou Remboursé partiellement
    //     Flight::halt(400, json_encode(['message' => 'Ce prêt ne peut pas être remboursé']));
    // }

    // Somme des remboursements déjà effectués
    $stmt = $db->prepare("SELECT COALESCE(SUM(montant_rembourse)) as total_rembourse FROM historique_remboursement WHERE pret_id = ?");
    $stmt->execute([$data->pret_id]);
    $total_rembourse = $stmt->fetchColumn() ?? 0;

    $nouveau_total = $total_rembourse + $data->montant;
    $montant_emprunt = $pret['montant_emprunt'];

    if ($nouveau_total > $montant_emprunt) {
        Flight::halt(400, json_encode(['message' => 'Montant remboursé dépasse le montant emprunté']));
    }

    // Insertion du remboursement
    $stmt = $db->prepare("INSERT INTO historique_remboursement (pret_id, montant_rembourse) VALUES (?, ?)");
    $stmt->execute([$data->pret_id, $data->montant]);

    // Mise à jour de l'état du prêt selon remboursement
    if ($nouveau_total == $montant_emprunt) {
        $etat = 5; // Remboursé totalement
    } else {
        $etat = 4; // Remboursé partiellement
    }

    $stmt = $db->prepare("UPDATE pret SET id_etat_validation = ? WHERE id = ?");
    $stmt->execute([$etat, $data->pret_id]);

    // Remboursement = retour du montant dans l’établissement
    $stmt = $db->prepare("UPDATE etablissement_financier SET solde_actuelle = solde_actuelle + ? WHERE id = 1");
    $stmt->execute([$data->montant]);

    Flight::json(['message' => 'Remboursement effectué avec succès']);
});




Flight::start();
