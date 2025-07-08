<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('POST /prets/@id/valider', ['PretController', 'valider']);
Flight::route('GET /prets', ['PretController', 'getAll']);
// Flight::route('POST /prets/simuler', ['PretController', 'simuler']);

Flight::route('GET /prets/@idPret/pdf', function($idPret) {
    require_once __DIR__ . '/../../export_pret_pdf.php';
});
?>