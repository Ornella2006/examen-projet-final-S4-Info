<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../helpers/Utils.php';

class EtablissementFinancierController {
    public static function getAll() {
        $etablissements = EtablissementFinancier::getAll();
        Flight::json($etablissements);
    }

    public static function getById($id) {
        $etablissement = EtablissementFinancier::getById($id);
        if (!$etablissement) {
            Flight::json(['error' => 'Établissement non trouvé'], 404);
            return;
        }
        Flight::json($etablissement);
    }

    public static function create() {
        $data = Flight::request()->data;
        if (empty($data->nomEtablissementFinancier) || !isset($data->fondTotal) || $data->fondTotal < 0) {
            Flight::json(['error' => 'Nom et fonds total requis, fonds total doit être positif'], 400);
            return;
        }
        $id = EtablissementFinancier::create($data);
        Flight::json(['message' => 'Établissement financier ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        error_log("Données reçues pour PUT /etablissements/$id: " . print_r($data, true));
        
        if (empty($data->nomEtablissementFinancier) || !isset($data->fondTotal) || !is_numeric($data->fondTotal) || $data->fondTotal < 0) {
            Flight::json(['error' => 'Nom et fonds total requis, fonds total doit être un nombre positif'], 400);
            return;
        }
        $affected = EtablissementFinancier::update($id, $data);
        if ($affected === 0) {
            Flight::json(['error' => 'Établissement non trouvé ou aucune modification effectuée'], 404);
            return;
        }
        Flight::json(['message' => 'Établissement financier modifié']);
    }

    public static function delete($id) {
        $affected = EtablissementFinancier::delete($id);
        if ($affected === 0) {
            Flight::json(['error' => 'Établissement non trouvé'], 404);
            return;
        }
        Flight::json(['message' => 'Établissement financier supprimé']);
    }
}