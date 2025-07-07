<?php 
Flight::route('GET /test', function() {
    error_log("Test de journalisation depuis la route /test");
    echo "Test terminé.";
});
