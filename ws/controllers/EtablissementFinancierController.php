<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';

class EtablissementFinancierController {

    public static function getAllEtablissement() {
        $etablissements = EtablissementFinancier::getAllEtablissement();
        Flight::json($etablissements);
    }

    public static function ajouterFonds($idEtablissement) {
        $data = Flight::request()->data;
        $montant = $data->montant ?? null;
        $dateAjout = $data->dateAjout ?? null;

        if (!$montant || $montant <= 0) {
            Flight::json(['error' => 'Montant invalide'], 400);
            return;
        }

        $result = EtablissementFinancier::addFonds($idEtablissement, $montant, $dateAjout);
        if (isset($result['error'])) {
            Flight::json(['error' => $result['error']], 400);
        } else {
            Flight::json($result, 200);
        }
    }
}