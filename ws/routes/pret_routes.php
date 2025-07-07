<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /creation_pret', ['PretController', 'createPret']);
