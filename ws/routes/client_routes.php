<?php
require_once __DIR__ . '/../controllers/ClientController.php';
Flight::route('GET /clients', ['ClientController', 'getAll']);
?>