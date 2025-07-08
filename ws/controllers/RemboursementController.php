<?php
require_once __DIR__ . '/../models/Remboursement.php';

class RemboursementController {
    public static function rembourser() {
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes POST /remboursements: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées POST /remboursements: " . print_r($parsedData, true));

        if (empty($data->idPret) || !is_numeric($data->idPret)) {
            error_log("Erreur: idPret manquant ou invalide");
            Flight::json(['error' => 'Identifiant du prêt requis'], 400);
            return;
        }
        if (empty($data->montantRembourse) || !is_numeric($data->montantRembourse) || $data->montantRembourse <= 0) {
            error_log("Erreur: montantRembourse invalide");
            Flight::json(['error' => 'Montant remboursé doit être un nombre positif'], 400);
            return;
        }
        if (empty($data->dateRemboursement)) {
            error_log("Erreur: dateRemboursement manquante");
            Flight::json(['error' => 'Date de remboursement requise'], 400);
            return;
        }

        try {
            $id = Remboursement::rembourser($data);
            Flight::json(['message' => 'Remboursement effectué avec succès', 'id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur lors du remboursement: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function getRemboursements($idPret = null) {
        try {
            $remboursements = Remboursement::getRemboursements($idPret);
            Flight::json($remboursements);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des remboursements: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}