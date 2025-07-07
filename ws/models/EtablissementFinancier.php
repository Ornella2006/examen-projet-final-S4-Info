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
}