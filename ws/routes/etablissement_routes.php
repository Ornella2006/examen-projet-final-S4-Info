<?php
require_once __DIR__ . '/../controllers/EtablissementFinancierController.php';
Flight::route('GET /etablissements', ['EtablissementFinancierController', 'getAllEtablissement']);
Flight::route('POST /etablissements/@id/fonds', ['EtablissementFinancierController', 'ajouterFonds']);