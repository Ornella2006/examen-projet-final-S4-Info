<?php
require 'vendor/autoload.php';
require 'db.php';
use App\Controllers\ClientController;


Flight::route('GET /clients', [ClientController::class, 'index']);
Flight::route('GET /clients/@id', [ClientController::class, 'show']);
Flight::route('POST /clients', [ClientController::class, 'store']);
Flight::route('PUT /clients/@id', [ClientController::class, 'update']);
Flight::route('DELETE /clients/@id', [ClientController::class, 'destroy']);

// Route pour afficher la vue HTML des clients
Flight::route('GET /clients-view', [ClientController::class, 'indexView']);

Flight::start();