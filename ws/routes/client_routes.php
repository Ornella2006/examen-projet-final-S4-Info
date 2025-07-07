<?php
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../db.php';
$pdo = getDB();

Flight::route('GET /clients', function() use ($pdo) {
    $controller = new ClientController($pdo);
    $controller->listerClients();
});

Flight::route('POST /clients', function() use ($pdo) {
    $controller = new ClientController($pdo);
    $controller->create();
});

Flight::route('PUT /clients/@id', function($id) use ($pdo) {
    $controller = new ClientController($pdo);
    $controller->update($id);
});

Flight::route('DELETE /clients/@id', function($id) use ($pdo) {
    $controller = new ClientController($pdo);
    $controller->delete($id);
});
?>