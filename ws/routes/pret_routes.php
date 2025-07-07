<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../db.php';
$pdo = getDB();

Flight::route('GET /prets', function() use ($pdo) {
    $controller = new PretController($pdo);
    $controller->listerPrets();
});

Flight::route('POST /prets', function() use ($pdo) {
    $controller = new PretController($pdo);
    $controller->creerPret();
});

Flight::route('PUT /prets/@id', function($id) use ($pdo) {
    $controller = new PretController($pdo);
    $controller->update($id);
});

Flight::route('DELETE /prets/@id', function($id) use ($pdo) {
    $controller = new PretController($pdo);
    $controller->delete($id);
});
?>