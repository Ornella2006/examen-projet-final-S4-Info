<?php
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../db.php';
$pdo = getDB();

Flight::route('GET /types-prets', function() use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->listerTypesPret();
});

Flight::route('POST /types-prets', function() use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->showForm();
});

Flight::route('PUT /types-prets/@id', function($id) use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->update($id);
});

Flight::route('DELETE /types-prets/@id', function($id) use ($pdo) {
    $controller = new TypePretController($pdo);
    $controller->delete($id);
});
?>