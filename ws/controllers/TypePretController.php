<?php
require_once 'models/TypePretModel.php';

class TypePretController {
    private $typePretModel;

    public function __construct($pdo) {
        $this->typePretModel = new TypePretModel($pdo);
    }

    public function showForm() {
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = Flight::request()->data->libelle ?? '';
            $tauxInteret = Flight::request()->data->tauxInteret ?? '';
            $dureeMaxMois = Flight::request()->data->dureeMaxMois ?? '';

            $result = $this->typePretModel->createTypePret($libelle, $tauxInteret, $dureeMaxMois);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }

        Flight::render('type_pret_form.php', ['error' => $error, 'success' => $success]);
    }
}
?>