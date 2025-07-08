<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('POST /remboursements', ['RemboursementController', 'rembourser']);
Flight::route('GET /remboursements', ['RemboursementController', 'getRemboursements']);
Flight::route('GET /prets/@id/remboursements', ['RemboursementController', 'getRemboursements']);