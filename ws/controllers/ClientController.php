<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController {
    public static function getAll() {
        $clients = Client::getAll();
        Flight::json($clients);
    }

    public static function getById($id) {
        $client = Client::getById($id);
        if (!$client) {
            Flight::halt(404, json_encode(['message' => 'Client non trouvé']));
        }
        Flight::json($client);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Client::create($data);
        Flight::json(['message' => 'Client créé', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Client::update($id, $data);
        Flight::json(['message' => 'Client mis à jour']);
    }

    public static function delete($id) {
        Client::delete($id);
        Flight::json(['message' => 'Client supprimé']);
    }
}