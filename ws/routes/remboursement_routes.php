<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('POST /remboursement', ['RemboursementController', 'effectuerRemboursement']);
