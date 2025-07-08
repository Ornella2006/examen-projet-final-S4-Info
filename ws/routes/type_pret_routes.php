<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /types-prets', ['TypePretController', 'getAll']);
Flight::route('GET /types-prets/@id', ['TypePretController', 'getById']);
Flight::route('POST /types-prets', ['TypePretController', 'create']);
Flight::route('PUT /types-prets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /types-prets/@id', ['TypePretController', 'delete']);