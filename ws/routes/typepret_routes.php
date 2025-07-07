<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

// Afficher le formulaire
Flight::route('GET /typepret/form', function() {
    TypePretController::showForm();
});

// Traiter la soumission du formulaire
Flight::route('POST /typepret/form', function() {
    TypePretController::create();
});
