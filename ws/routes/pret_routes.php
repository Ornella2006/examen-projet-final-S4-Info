<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('PUT /prets/@id/valider', ['PretController', 'valider']);
Flight::route('GET /prets', ['PretController', 'getAll']);

Flight::route('GET /prets/@idPret/pdf', function($idPret) {
    require_once __DIR__ . '/../../export_pret_pdf.php';
});

// Route pour le PDF
Flight::route('GET /prets/@idPret/pdf', function($idPret) {
    require_once __DIR__ . '/../../export_pret_pdf.php';
});
?>