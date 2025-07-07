<?php
class TypePret {
    public $idTypePret;
    public $libelle;
    public $tauxInteret;
    public $dureeMaxMois;
    public $dateCreation;

    public static function create($libelle, $tauxInteret, $dureeMaxMois) {
        require_once __DIR__ . '/../db.php';
        $db = getDB();
        // Vérifier unicité du nom
        $stmt = $db->prepare('SELECT COUNT(*) FROM TypePret_EF WHERE libelle = ?');
        $stmt->execute([$libelle]);
        if ($stmt->fetchColumn() > 0) {
            return ['error' => 'Nom déjà existant'];
        }
        // Vérifier taux >= 0
        if (!is_numeric($tauxInteret) || $tauxInteret < 0) {
            return ['error' => 'Taux invalide'];
        }
        // Vérifier nom non vide
        if (empty($libelle)) {
            return ['error' => 'Nom obligatoire'];
        }
        $stmt = $db->prepare('INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES (?, ?, ?)');
        if ($stmt->execute([$libelle, $tauxInteret, $dureeMaxMois])) {
            return ['success' => true];
        } else {
            return ['error' => 'Erreur lors de l\'enregistrement'];
        }
    }
}
