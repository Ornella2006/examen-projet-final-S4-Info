<?php
require_once 'controllers/InteretsEFController.php';
require_once 'db.php';
$pdo = getDB();

Flight::route('GET /interets_ef', function() use ($pdo) {
    $controller = new InteretsEFController($pdo);
    $controller->showSynthese();
});
