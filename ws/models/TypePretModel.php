<?php
class TypePretModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createTypePret($libelle, $tauxInteret, $dureeMaxMois) {
        if (empty($libelle)) {
            return ['success' => false, 'message' => 'Le nom du type de prêt est obligatoire'];
        }
        if (!is_numeric($tauxInteret) || $tauxInteret < 0) {
            return ['success' => false, 'message' => 'Le taux d\'intérêt doit être un nombre positif'];
        }
        if (!is_numeric($dureeMaxMois) || $dureeMaxMois <= 0) {
            return ['success' => false, 'message' => 'La durée maximale doit être un nombre positif'];
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM TypePret_EF WHERE libelle = ?");
        $stmt->execute([$libelle]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Le nom du type de prêt existe déjà'];
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES (?, ?, ?)");
            $stmt->execute([$libelle, $tauxInteret, $dureeMaxMois]);
            return ['success' => true, 'message' => 'Type de prêt enregistré avec succès'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()];
        }
    }

    public function updateTypePret($id, $libelle, $tauxInteret, $dureeMaxMois) {
        if (empty($libelle)) {
            return ['success' => false, 'message' => 'Le nom du type de prêt est obligatoire'];
        }
        if (!is_numeric($tauxInteret) || $tauxInteret < 0) {
            return ['success' => false, 'message' => 'Le taux d\'intérêt doit être un nombre positif'];
        }
        if (!is_numeric($dureeMaxMois) || $dureeMaxMois <= 0) {
            return ['success' => false, 'message' => 'La durée maximale doit être un nombre positif'];
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM TypePret_EF WHERE libelle = ? AND idTypePret != ?");
        $stmt->execute([$libelle, $id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Le nom du type de prêt existe déjà'];
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE TypePret_EF SET libelle = ?, tauxInteret = ?, dureeMaxMois = ? WHERE idTypePret = ?");
            $stmt->execute([$libelle, $tauxInteret, $dureeMaxMois, $id]);
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Type de prêt non trouvé'];
            }
            return ['success' => true, 'message' => 'Type de prêt modifié avec succès'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la modification : ' . $e->getMessage()];
        }
    }

    public function getTypePretById($idTypePret) {
        $stmt = $this->pdo->prepare("SELECT * FROM TypePret_EF WHERE idTypePret = ?");
        $stmt->execute([$idTypePret]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>