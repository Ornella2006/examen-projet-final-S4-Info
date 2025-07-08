<?php
require_once __DIR__ . '/../db.php';

class Login {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function verifyAdmin($nom, $motDePasse) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom, motDePasse FROM Admin WHERE nom = ?");
            $stmt->execute([$nom]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Résultat de la requête pour nom=$nom: " . print_r($admin, true));

            if ($admin && ($motDePasse == $admin['motDePasse'])) {
                error_log("Mot de passe vérifié avec succès pour nom=$nom");
                return $admin;
            }
            error_log("Échec de la vérification: utilisateur non trouvé ou mot de passe incorrect pour nom=$nom");
            return false;
        } catch (PDOException $e) {
            error_log("Erreur SQL dans verifyAdmin: " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification des identifiants");
        }
    }
}
?>