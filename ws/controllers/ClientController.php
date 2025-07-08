<?php
require_once __DIR__ . '/../helpers/Utils.php';

class ClientController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public static function getAll() {
        $clients = Client::getAll();
        Flight::json($clients);
    }
    
    public function listerClients() {
        $stmt = $this->pdo->query("SELECT idClient, nom, prenom, adresse, telephone, email FROM Client_EF WHERE actif = 1 ORDER BY idClient");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Flight::json($clients);
    }

    public function create() {
        $data = Flight::request()->data;
        if (empty($data->nom) || empty($data->prenom) || empty($data->email)) {
            Flight::json(['success' => false, 'message' => 'Nom, prénom et email sont requis'], 400);
            return;
        }
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(['success' => false, 'message' => 'Email invalide'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Client_EF WHERE email = ?");
        $stmt->execute([$data->email]);
        if ($stmt->fetchColumn() > 0) {
            Flight::json(['success' => false, 'message' => 'Cet email est déjà utilisé'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO Client_EF (nom, prenom, adresse, telephone, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data->nom, $data->prenom, $data->adresse, $data->telephone, $data->email]);
            Flight::json(['success' => true, 'message' => 'Client ajouté avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de l\'ajout : ' . $e->getMessage()], 400);
        }
    }

    public function update($id) {
        $data = Flight::request()->data;
        if (empty($data->nom) || empty($data->prenom) || empty($data->email)) {
            Flight::json(['success' => false, 'message' => 'Nom, prénom et email sont requis'], 400);
            return;
        }
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(['success' => false, 'message' => 'Email invalide'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Client_EF WHERE email = ? AND idClient != ?");
        $stmt->execute([$data->email, $id]);
        if ($stmt->fetchColumn() > 0) {
            Flight::json(['success' => false, 'message' => 'Cet email est déjà utilisé'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE Client_EF SET nom = ?, prenom = ?, adresse = ?, telephone = ?, email = ? WHERE idClient = ?");
            $stmt->execute([$data->nom, $data->prenom, $data->adresse, $data->telephone, $data->email, $id]);
            if ($stmt->rowCount() === 0) {
                Flight::json(['success' => false, 'message' => 'Client non trouvé'], 404);
                return;
            }
            Flight::json(['success' => true, 'message' => 'Client modifié avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la modification : ' . $e->getMessage()], 400);
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE Client_EF SET actif = 0 WHERE idClient = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                Flight::json(['success' => false, 'message' => 'Client non trouvé'], 404);
                return;
            }
            Flight::json(['success' => true, 'message' => 'Client supprimé avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 400);
        }
    }
}
?>