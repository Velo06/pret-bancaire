<?php
require_once __DIR__ . '/../models/Employe.php';

class EmployeController {
    public static function login() {
        $request = Flight::request();
        $username = $request->data->username;
        $password = $request->data->password;

        if (empty($username) || empty($password)) {
            Flight::halt(400, json_encode([
                'success' => false, 
                'message' => 'Identifiants manquants'
            ]));
            return;
        }

        $user = Employe::authenticate($username, $password);

        if (!$user) {
            Flight::json([
                'success' => false, 
                'message' => 'Identifiants incorrects'
            ]);
            return;
        }

        Flight::json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['id'],
                'username' => $user['pseudo']
            ]
        ]);
    }
}