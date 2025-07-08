<?php
require 'vendor/autoload.php';
require 'db.php';
// require 'routes/etudiant_routes.php';
require 'routes/type_pret_routes.php';
require 'routes/interets_ef_routes.php';
require 'routes/pret_routes.php';
require 'routes/client_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/login_routes.php'; 
require 'routes/simulation_routes.php';
require 'routes/remboursement_routes.php';


Flight::route('GET /ajouter_fonds.html', function() {
    echo file_get_contents(__DIR__ . '/../ajouter_fonds.html');
});

Flight::route('GET /test', function() {
    error_log("Test de journalisation depuis la route /test");
    echo "Test terminé.";
});


Flight::start();
error_log("Test de journalisation depuis index.php");