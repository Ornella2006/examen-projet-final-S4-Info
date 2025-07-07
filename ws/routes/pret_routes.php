<?php
require_once 'controllers/PretController.php';
require_once 'db.php';
$pdo = getDB();

Flight::route('GET /pret/new', function() use ($pdo) {
    $controller = new PretController($pdo);
    $controller->creerPret();
});

Flight::route('POST /pret/new', function() use ($pdo) {
    $controller = new PretController($pdo);
    $controller->creerPret();
});
