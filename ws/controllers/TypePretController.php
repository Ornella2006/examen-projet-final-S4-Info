<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function getAll() {
        $types = TypePret::getAll();
        Flight::json($types);
    }

    public static function getById($id) {
        $type = TypePret::getById($id);
        if (!$type) {
            Flight::json(['error' => 'Type de prêt non trouvé'], 404);
            return;
        }
        Flight::json($type);
    }

    public static function create() {
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes POST /types-prets: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées POST /types-prets: " . print_r($parsedData, true));
        error_log("Objet data: " . print_r($data, true));
        
        if (empty($data->libelle)) {
            error_log("Erreur: libelle est vide");
            Flight::json(['error' => 'Libellé requis'], 400);
            return;
        }
        if (!isset($data->tauxInteret) || !is_numeric($data->tauxInteret)) {
            error_log("Erreur: tauxInteret non défini ou non numérique");
            Flight::json(['error' => 'Taux d\'intérêt doit être un nombre'], 400);
            return;
        }
        if (floatval($data->tauxInteret) < 0) {
            error_log("Erreur: tauxInteret négatif");
            Flight::json(['error' => 'Taux d\'intérêt doit être positif'], 400);
            return;
        }
        if (!isset($data->dureeMaxMois) || !is_numeric($data->dureeMaxMois)) {
            error_log("Erreur: dureeMaxMois non défini ou non numérique");
            Flight::json(['error' => 'Durée maximale doit être un nombre'], 400);
            return;
        }
        if (intval($data->dureeMaxMois) <= 0) {
            error_log("Erreur: dureeMaxMois négatif ou zéro");
            Flight::json(['error' => 'Durée maximale doit être positive'], 400);
            return;
        }
        try {
            $id = TypePret::create($data);
            Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function update($id) {
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes PUT /types-prets/$id: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées PUT /types-prets/$id: " . print_r($parsedData, true));
        error_log("Objet data: " . print_r($data, true));
        
        if (empty($data->libelle)) {
            error_log("Erreur: libelle est vide");
            Flight::json(['error' => 'Libellé requis'], 400);
            return;
        }
        if (!isset($data->tauxInteret) || !is_numeric($data->tauxInteret)) {
            error_log("Erreur: tauxInteret non défini ou non numérique");
            Flight::json(['error' => 'Taux d\'intérêt doit être un nombre'], 400);
            return;
        }
        if (floatval($data->tauxInteret) < 0) {
            error_log("Erreur: tauxInteret négatif");
            Flight::json(['error' => 'Taux d\'intérêt doit être positif'], 400);
            return;
        }
        if (!isset($data->dureeMaxMois) || !is_numeric($data->dureeMaxMois)) {
            error_log("Erreur: dureeMaxMois non défini ou non numérique");
            Flight::json(['error' => 'Durée maximale doit être un nombre'], 400);
            return;
        }
        if (intval($data->dureeMaxMois) <= 0) {
            error_log("Erreur: dureeMaxMois négatif ou zéro");
            Flight::json(['error' => 'Durée maximale doit être positive'], 400);
            return;
        }

        try {
            $affected = TypePret::update($id, $data);
            if ($affected === 0) {
                error_log("Erreur: aucune ligne affectée pour idTypePret=$id");
                Flight::json(['error' => 'Type de prêt non trouvé ou aucune modification effectuée'], 404);
                return;
            }
            Flight::json(['message' => 'Type de prêt modifié']);
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function delete($id) {
        $affected = TypePret::delete($id);
        if ($affected === 0) {
            Flight::json(['error' => 'Type de prêt non trouvé'], 404);
            return;
        }
        Flight::json(['message' => 'Type de prêt supprimé']);
    }
}