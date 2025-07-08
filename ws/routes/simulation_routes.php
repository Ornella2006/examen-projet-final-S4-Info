<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/EtablissementFinancierController.php';

Flight::route('POST /prets/simuler', ['PretController', 'simuler']);
Flight::route('GET /clients', ['ClientController', 'getAll']);
Flight::route('GET /etablissements', ['EtablissementFinancierController', 'getAll']);
Flight::route('GET /simulations', ['PretController', 'getAllSimulations']);
?>