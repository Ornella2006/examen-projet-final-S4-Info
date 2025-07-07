<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/type_pret_routes.php';
require 'routes/pret_routes.php';
require 'routes/client_routes.php';
require 'routes/simulation_routes.php';



Flight::route('GET /test', function() {
    error_log("Test de journalisation depuis la route /test");
    echo "Test terminé.";
});


Flight::start();
error_log("Test de journalisation depuis index.php");