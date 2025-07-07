<?php
require_once 'controllers/ClientController.php';
require_once 'db.php';
$pdo = getDB();

Flight::route('GET /clients', function() use ($pdo) {
    $controller = new ClientController($pdo);
    $controller->listerClients();
});
