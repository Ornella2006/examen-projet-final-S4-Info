<?php
require 'vendor/autoload.php';
require 'db.php';
// require 'routes/etudiant_routes.php';
require 'routes/type_pret_routes.php';
require 'routes/interets_ef_routes.php';
require 'routes/pret_routes.php';
require 'routes/client_routes.php';
require 'routes/etablissement_routes.php';


Flight::route('GET /ajouter_fonds.html', function() {
    echo file_get_contents(__DIR__ . '/../ajouter_fonds.html');
});


Flight::start();