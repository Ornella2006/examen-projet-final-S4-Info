<?php

require_once 'controllers/TypePretController.php';
require_once 'db.php';
$pdo = getDB();

Flight::route('GET /type_pret/new', function() use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->showForm();
});

Flight::route('POST /type_pret/new', function() use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->showForm();
});
?>