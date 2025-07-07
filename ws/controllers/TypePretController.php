<?php
require_once __DIR__ . '/../models/TypePretModel.php';

class TypePretController {

    public static function create() {
        $data = Flight::request()->data;
        $id = TypePretModel::createTypePret($data);
        Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
    }
}
