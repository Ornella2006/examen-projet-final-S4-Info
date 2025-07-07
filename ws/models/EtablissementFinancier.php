<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT idEtablissementFinancier, nomEtablissementFinancier, fondTotal FROM EtablissementFinancier_EF");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal) VALUES (?, ?)");
        $stmt->execute([$data->nomEtablissementFinancier, $data->fondTotal]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE EtablissementFinancier_EF SET nomEtablissementFinancier = ?, fondTotal = ? WHERE idEtablissementFinancier = ?");
        $stmt->execute([$data->nomEtablissementFinancier, floatval($data->fondTotal), $id]);
        return $stmt->rowCount();
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}