<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM TypePret_EF");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM TypePret_EF WHERE idTypePret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        // Vérifier si le libellé existe déjà
        $stmt = $db->prepare("SELECT COUNT(*) FROM TypePret_EF WHERE libelle = ?");
        $stmt->execute([$data->libelle]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Le libellé du type de prêt existe déjà.");
        }
        try {
            $stmt = $db->prepare("INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES (?, ?, ?)");
            $stmt->execute([$data->libelle, floatval($data->tauxInteret), intval($data->dureeMaxMois)]);
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur SQL dans create: " . $e->getMessage());
            throw new Exception("Erreur lors de l'ajout du type de prêt.");
        }
    }

    public static function update($id, $data) {
        $db = getDB();
        // Vérifier si le libellé existe déjà pour un autre ID
        $stmt = $db->prepare("SELECT COUNT(*) FROM TypePret_EF WHERE libelle = ? AND idTypePret != ?");
        $stmt->execute([$data->libelle, $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Le libellé du type de prêt existe déjà.");
        }
        try {
            $stmt = $db->prepare("UPDATE TypePret_EF SET libelle = ?, tauxInteret = ?, dureeMaxMois = ? WHERE idTypePret = ?");
            $stmt->execute([$data->libelle, floatval($data->tauxInteret), intval($data->dureeMaxMois), $id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Erreur SQL dans update: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour du type de prêt.");
        }
    }

    public static function delete($id) {
        $db = getDB();
        try {
            $stmt = $db->prepare("DELETE FROM TypePret_EF WHERE idTypePret = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Erreur SQL dans delete: " . $e->getMessage());
            throw new Exception("Erreur lors de la suppression du type de prêt.");
        }
    }
}