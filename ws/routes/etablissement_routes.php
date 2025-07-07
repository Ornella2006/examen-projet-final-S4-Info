<?php
require_once __DIR__ . '/../controllers/EtablissementFinancierController.php';

Flight::route('GET /etablissements', ['EtablissementFinancierController', 'getAll']);
Flight::route('GET /etablissements/@id', ['EtablissementFinancierController', 'getById']);
Flight::route('POST /etablissements', ['EtablissementFinancierController', 'create']);
Flight::route('PUT /etablissements/@id', ['EtablissementFinancierController', 'update']);
Flight::route('DELETE /etablissements/@id', ['EtablissementFinancierController', 'delete']);