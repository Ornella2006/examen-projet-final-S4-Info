<?php
require_once 'ws/db.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM EtablissementFinancier_EF");
    $etablissements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h1>Test de connexion à la base de données</h1>";
    echo "<pre>";
    print_r($etablissements);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>