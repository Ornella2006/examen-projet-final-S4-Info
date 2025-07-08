<?php
require_once __DIR__ . '/../db.php';

class Client {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT idClient, nom, prenom, email FROM Client_EF WHERE actif = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}