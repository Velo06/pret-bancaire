<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /mensuels', ['PretController', 'getInterets']);