<?php
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__ . '/../models/TypePretModel.php';

class PretController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function creerPret() {
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idClient = $_POST['idClient'] ?? null;
            $idTypePret = $_POST['idTypePret'] ?? null;
            $montant = $_POST['montant'] ?? null;
            $dureeMois = $_POST['dureeMois'] ?? null;
            $dateDemande = $_POST['dateDemande'] ?? date('Y-m-d');
            $dateAccord = $_POST['dateAccord'] ?? date('Y-m-d');
            $datePremiereEcheance = $_POST['datePremiereEcheance'] ?? date('Y-m-d');

            
            $typePretModel = new TypePretModel($this->pdo);
            $typePret = $typePretModel->getTypePretById($idTypePret);
            $tauxInteret = $typePret['tauxInteret'] ?? 0;

            $pretModel = new PretModel($this->pdo);
            $result = $pretModel->creerPretAvecRemboursements($idClient, $idTypePret, $montant, $dureeMois, $dateDemande, $dateAccord, $tauxInteret, $datePremiereEcheance);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
       
        Flight::render('pret_form.php', ['error' => $error, 'success' => $success]);
    }
}
