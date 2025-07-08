<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {
    public static function create() {
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes POST /prets: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées POST /prets: " . print_r($parsedData, true));
        error_log("Objet data: " . print_r($data, true));

        if (empty($data->idClient) || !is_numeric($data->idClient)) {
            error_log("Erreur: idClient manquant ou invalide");
            Flight::json(['error' => 'Identifiant du client requis'], 400);
            return;
        }
        if (empty($data->idTypePret) || !is_numeric($data->idTypePret)) {
            error_log("Erreur: idTypePret manquant ou invalide");
            Flight::json(['error' => 'Type de prêt requis'], 400);
            return;
        }
        if (empty($data->idEtablissementFinancier) || !is_numeric($data->idEtablissementFinancier)) {
            error_log("Erreur: idEtablissementFinancier manquant ou invalide");
            Flight::json(['error' => 'Établissement financier requis'], 400);
            return;
        }
        if (empty($data->montant) || !is_numeric($data->montant) || $data->montant <= 0) {
            error_log("Erreur: montant invalide");
            Flight::json(['error' => 'Montant doit être un nombre positif'], 400);
            return;
        }
        if (empty($data->dureeMois) || !is_numeric($data->dureeMois) || $data->dureeMois <= 0) {
            error_log("Erreur: dureeMois invalide");
            Flight::json(['error' => 'Durée doit être un entier positif'], 400);
            return;
        }
        if (empty($data->dateDemande)) {
            error_log("Erreur: dateDemande manquante");
            Flight::json(['error' => 'Date de demande requise'], 400);
            return;
        }
        // Valider le taux d'assurance
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            error_log("Erreur: tauxAssurance invalide");
            Flight::json(['error' => 'Le taux d\'assurance doit être compris entre 0 et 5%'], 400);
            return;
        }

        try {
            $id = Pret::create($data);
            Flight::json(['message' => 'Prêt créé, en attente de validation', 'id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur lors de la création: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function valider($id) {
        try {
            $affected = Pret::valider($id);
            if ($affected === 0) {
                error_log("Erreur: aucun prêt trouvé pour idPret=$id");
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }
            Flight::json(['message' => 'Prêt validé']);
        } catch (Exception $e) {
            error_log("Erreur lors de la validation: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }

      public static function simuler() {
        $rawInput = file_get_contents('php://input');
        error_log("Données brutes POST /prets/simuler: " . $rawInput);
        parse_str($rawInput, $parsedData);
        $data = (object) $parsedData;
        error_log("Données parsées POST /prets/simuler: " . print_r($parsedData, true));

        if (empty($data->idTypePret) || !is_numeric($data->idTypePret)) {
            error_log("Erreur: idTypePret manquant ou invalide");
            Flight::json(['error' => 'Type de prêt requis'], 400);
            return;
        }
        if (empty($data->montant) || !is_numeric($data->montant) || $data->montant <= 0) {
            error_log("Erreur: montant invalide");
            Flight::json(['error' => 'Montant doit être un nombre positif'], 400);
            return;
        }
        if (empty($data->dureeMois) || !is_numeric($data->dureeMois) || $data->dureeMois <= 0) {
            error_log("Erreur: dureeMois invalide");
            Flight::json(['error' => 'Durée doit être un entier positif'], 400);
            return;
        }
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            error_log("Erreur: tauxAssurance invalide");
            Flight::json(['error' => 'Le taux d\'assurance doit être compris entre 0 et 5%'], 400);
            return;
        }
        $delaiPremierRemboursementMois = isset($data->delaiPremierRemboursementMois) ? intval($data->delaiPremierRemboursementMois) : 0;
        if ($delaiPremierRemboursementMois < 0 || $delaiPremierRemboursementMois > 12) {
            error_log("Erreur: delaiPremierRemboursementMois invalide");
            Flight::json(['error' => 'Le délai de premier remboursement doit être compris entre 0 et 12 mois'], 400);
            return;
        }
        try {
            $result = Pret::simuler($data);
            Flight::json($result);
        } catch (Exception $e) {
            error_log("Erreur lors de la simulation: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}


  
    ?>