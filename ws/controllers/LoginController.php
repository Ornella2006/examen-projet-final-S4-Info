<?php
require_once __DIR__ . '/../models/Login.php';

class LoginController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public static function login() {
        error_log("Appel de LoginController::login");
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes POST /login: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées POST /login: " . print_r($parsedData, true));

        if (empty($data->nom)) {
            $response = ['success' => false, 'message' => 'Identifiant requis'];
            error_log("Erreur: nom manquant, réponse: " . json_encode($response));
            Flight::json($response, 400);
            return;
        }
        if (empty($data->motDePasse)) {
            $response = ['success' => false, 'message' => 'Mot de passe requis'];
            error_log("Erreur: motDePasse manquant, réponse: " . json_encode($response));
            Flight::json($response, 400);
            return;
        }

        try {
            $loginModel = new Login(getDB());
            $admin = $loginModel->verifyAdmin($data->nom, $data->motDePasse);
            if ($admin) {
                session_start();
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $response = ['success' => true, 'message' => 'Connexion réussie'];
                error_log("Connexion réussie pour nom=" . $data->nom . ", réponse: " . json_encode($response));
                Flight::json($response);
            } else {
                $response = ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect'];
                error_log("Erreur: identifiants incorrects pour nom=" . $data->nom . ", réponse: " . json_encode($response));
                Flight::json($response, 401);
            }
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()];
            error_log("Erreur lors de la connexion: " . $e->getMessage() . ", réponse: " . json_encode($response));
            Flight::json($response, 500);
        }
    }
}
?>