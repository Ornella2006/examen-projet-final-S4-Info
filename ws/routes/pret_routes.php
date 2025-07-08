<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('POST /prets/@id/valider', ['PretController', 'valider']);
Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('POST /prets/simuler', ['PretController', 'simuler']);
?>