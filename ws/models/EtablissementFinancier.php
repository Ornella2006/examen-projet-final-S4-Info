<?php
require_once __DIR__ . '/../db.php';

class EtablissementFinancier {
    public static function getEtablissementById($id) {
        $db = getDB();
        $sql = "SELECT * FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?";
        $stmt = $db->prepare($sql);
        $tab=array($id);
        $stmt->execute($tab);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function addFonds($idEtablissement, $montant, $dateAjout = null) {
        $db = getDB();
        try {
            $etablissement = self::getEtablissementById($idEtablissement);
            if (!$etablissement) {
                throw new Exception("Etablissement non trouve");
            }
            if ($montant <= 0) {
                throw new Exception("Le montant doit etre superieur a 0");
            }
            $nouveauSolde = $etablissement['fondTotal'] + $montant;

            $sql="UPDATE EtablissementFinancier_EF SET fondTotal = ? WHERE idEtablissementFinancier = ?";
            $stmt = $db->prepare($sql);
            $tab=array($nouveauSolde, $idEtablissement);
            $stmt->execute($tab);

            $sql2="INSERT INTO AjoutFonds_EF (idEtablissementFinancier, montant, dateAjout) VALUES (?, ?, ?)";
            $stmt2 = $db->prepare($sql2);
            $dateAjout = $dateAjout ?? date('Y-m-d H:i:s');
            $stmt2->execute([$idEtablissement, $montant, $dateAjout]);

            return ['message' => 'Fonds ajoutes avec succes', 'nouveauSolde' => $nouveauSolde];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function getAllEtablissement() {
        $db = getDB();
        $sql="SELECT * FROM EtablissementFinancier_EF";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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