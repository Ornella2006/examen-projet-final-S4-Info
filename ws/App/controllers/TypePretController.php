<?php
require_once __DIR__ . '/../models/TypePret.php';

class TypePretController {
    public static function showForm($error = '', $old = []) {
        include __DIR__ . '/../views/typepret/form.php';
    }

    public static function create() {
        $libelle = $_POST['libelle'] ?? '';
        $tauxInteret = $_POST['tauxInteret'] ?? '';
        $dureeMaxMois = $_POST['dureeMaxMois'] ?? '';
        $result = TypePret::create($libelle, $tauxInteret, $dureeMaxMois);
        if (isset($result['success'])) {
            include __DIR__ . '/../views/typepret/success.php';
        } else {
            self::showForm($result['error'], ['libelle' => $libelle, 'tauxInteret' => $tauxInteret, 'dureeMaxMois' => $dureeMaxMois]);
        }
    }
}
