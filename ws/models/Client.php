<?php
namespace App\Models;

class Client {
    public static function getAll($db) {
        return $db->query("SELECT * FROM Client")->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function getById($db, $id) {
        $stmt = $db->prepare("SELECT * FROM Client WHERE id_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public static function create($db, $data) {
        $stmt = $db->prepare("INSERT INTO Client (nom, prenom, adresse, telephone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->adresse, $data->telephone, $data->email]);
        return $db->lastInsertId();
    }
    public static function update($db, $id, $data) {
        $stmt = $db->prepare("UPDATE Client SET nom = ?, prenom = ?, adresse = ?, telephone = ?, email = ? WHERE id_client = ?");
        return $stmt->execute([$data->nom, $data->prenom, $data->adresse, $data->telephone, $data->email, $id]);
    }
    public static function delete($db, $id) {
        $stmt = $db->prepare("DELETE FROM Client WHERE id_client = ?");
        return $stmt->execute([$id]);
    }
}
