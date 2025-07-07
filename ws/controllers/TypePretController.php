<?php
require_once __DIR__ . '/../models/TypePretModel.php';

class TypePretController {
    private $typePretModel;

    public function __construct($pdo) {
        $this->typePretModel = new TypePretModel($pdo);
    }

    public function listerTypesPret() {
        $stmt = $this->pdo->query("SELECT idTypePret, libelle, tauxInteret, dureeMaxMois FROM TypePret_EF");
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Flight::json($types);
    }

    public function showForm() {
        $data = Flight::request()->data;
        $result = $this->typePretModel->createTypePret($data->libelle ?? '', $data->tauxInteret ?? '', $data->dureeMaxMois ?? '');
        Flight::json($result);
    }

    public function update($id) {
        $data = Flight::request()->data;
        $result = $this->typePretModel->updateTypePret($id, $data->libelle ?? '', $data->tauxInteret ?? '', $data->dureeMaxMois ?? '');
        Flight::json($result);
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM TypePret_EF WHERE idTypePret = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                Flight::json(['success' => false, 'message' => 'Type de prêt non trouvé'], 404);
                return;
            }
            Flight::json(['success' => true, 'message' => 'Type de prêt supprimé avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 400);
        }
    }
}
?>