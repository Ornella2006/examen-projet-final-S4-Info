<?php
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__ . '/../models/TypePretModel.php';
require_once __DIR__ . '/../helpers/Utils.php';

class PretController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listerPrets() {
        $stmt = $this->pdo->query("
            SELECT p.idPret, p.idClient, p.idTypePret, p.idEtablissementFinancier, p.montant, p.dureeMois, 
                   p.dateDemande, p.dateAccord, p.statut, p.interets, p.dateRetourEstimee, p.tauxAssurance,
                   t.libelle, c.nom, c.prenom
            FROM Pret_EF p
            JOIN TypePret_EF t ON p.idTypePret = t.idTypePret
            JOIN Client_EF c ON p.idClient = c.idClient
            WHERE p.statut != 'rembourse'
        ");
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prets as &$pret) {
            $tauxEffectifMensuel = ($pret['tauxInteret'] + $pret['tauxAssurance']) / 100 / 12;
            $n = $pret['dureeMois'];
            $montant = $pret['montant'];
            if ($tauxEffectifMensuel > 0) {
                $puissance = pow(1 + $tauxEffectifMensuel, $n);
                $annuiteMensuelle = $montant * ($tauxEffectifMensuel * $puissance) / ($puissance - 1);
            } else {
                $annuiteMensuelle = $montant / $n;
            }
            $pret['annuiteMensuelle'] = round($annuiteMensuelle, 2);
            $pret['sommeTotaleRembourser'] = round($annuiteMensuelle * $n, 2);
        }

        Flight::json($prets);
    }

    public function creerPret() {
        $data = Flight::request()->data;
        if (empty($data->idClient) || !is_numeric($data->idClient)) {
            Flight::json(['success' => false, 'message' => 'Identifiant du client requis'], 400);
            return;
        }
        if (empty($data->idTypePret) || !is_numeric($data->idTypePret)) {
            Flight::json(['success' => false, 'message' => 'Type de prêt requis'], 400);
            return;
        }
        if (empty($data->idEtablissementFinancier) || !is_numeric($data->idEtablissementFinancier)) {
            Flight::json(['success' => false, 'message' => 'Établissement financier requis'], 400);
            return;
        }
        if (empty($data->montant) || !is_numeric($data->montant) || $data->montant <= 0) {
            Flight::json(['success' => false, 'message' => 'Montant doit être un nombre positif'], 400);
            return;
        }
        if (empty($data->dureeMois) || !is_numeric($data->dureeMois) || $data->dureeMois <= 0) {
            Flight::json(['success' => false, 'message' => 'Durée doit être un entier positif'], 400);
            return;
        }
        if (empty($data->dateDemande)) {
            Flight::json(['success' => false, 'message' => 'Date de demande requise'], 400);
            return;
        }
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            Flight::json(['success' => false, 'message' => 'Le taux d\'assurance doit être compris entre 0 et 5%'], 400);
            return;
        }

        $pretModel = new PretModel($this->pdo);
        $result = $pretModel->creerPretAvecRemboursements(
            $data->idClient,
            $data->idTypePret,
            $data->idEtablissementFinancier,
            $data->montant,
            $data->dureeMois,
            $data->dateDemande,
            $data->dateAccord ?? null,
            $tauxAssurance
        );
        Flight::json($result);
    }

    public function update($id) {
        $data = Flight::request()->data;
        if (empty($data->idClient) || !is_numeric($data->idClient)) {
            Flight::json(['success' => false, 'message' => 'Identifiant du client requis'], 400);
            return;
        }
        if (empty($data->idTypePret) || !is_numeric($data->idTypePret)) {
            Flight::json(['success' => false, 'message' => 'Type de prêt requis'], 400);
            return;
        }
        if (empty($data->idEtablissementFinancier) || !is_numeric($data->idEtablissementFinancier)) {
            Flight::json(['success' => false, 'message' => 'Établissement financier requis'], 400);
            return;
        }
        if (empty($data->montant) || !is_numeric($data->montant) || $data->montant <= 0) {
            Flight::json(['success' => false, 'message' => 'Montant doit être un nombre positif'], 400);
            return;
        }
        if (empty($data->dureeMois) || !is_numeric($data->dureeMois) || $data->dureeMois <= 0) {
            Flight::json(['success' => false, 'message' => 'Durée doit être un entier positif'], 400);
            return;
        }
        if (empty($data->dateDemande)) {
            Flight::json(['success' => false, 'message' => 'Date de demande requise'], 400);
            return;
        }
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            Flight::json(['success' => false, 'message' => 'Le taux d\'assurance doit être compris entre 0 et 5%'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("
                UPDATE Pret_EF 
                SET idClient = ?, idTypePret = ?, idEtablissementFinancier = ?, montant = ?, 
                    dureeMois = ?, dateDemande = ?, dateAccord = ?, tauxAssurance = ?
                WHERE idPret = ?
            ");
            $stmt->execute([
                $data->idClient,
                $data->idTypePret,
                $data->idEtablissementFinancier,
                $data->montant,
                $data->dureeMois,
                $data->dateDemande,
                $data->dateAccord ?? null,
                $tauxAssurance,
                $id
            ]);
            if ($stmt->rowCount() === 0) {
                Flight::json(['success' => false, 'message' => 'Prêt non trouvé'], 404);
                return;
            }
            Flight::json(['success' => true, 'message' => 'Prêt modifié avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la modification : ' . $e->getMessage()], 400);
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Pret_EF WHERE idPret = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                Flight::json(['success' => false, 'message' => 'Prêt non trouvé'], 404);
                return;
            }
            Flight::json(['success' => true, 'message' => 'Prêt supprimé avec succès']);
        } catch (PDOException $e) {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 400);
        }
    }
}
?>