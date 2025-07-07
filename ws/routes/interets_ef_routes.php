<?php
require_once __DIR__ . '/../controllers/InteretsEFController.php';
require_once __DIR__ . '/../db.php';
$pdo = getDB();

Flight::route('GET /interets-ef', function() use ($pdo) {
    $controller = new InteretsEFController($pdo);
    $controller->showSynthese();
});
?>