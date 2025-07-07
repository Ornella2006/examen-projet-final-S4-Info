<?php
namespace App\Controllers;
use App\Models\Client;

class ClientController {
    public static function index() {
        $db = getDB();
        $clients = Client::getAll($db);
        \Flight::json($clients);
    }
    public static function show($id) {
        $db = getDB();
        $client = Client::getById($db, $id);
        \Flight::json($client);
    }
    public static function store() {
        $data = \Flight::request()->data;
        $db = getDB();
        $id = Client::create($db, $data);
        \Flight::json(['message' => 'Client ajouté', 'id' => $id]);
    }
    public static function update($id) {
        $data = \Flight::request()->data;
        $db = getDB();
        Client::update($db, $id, $data);
        \Flight::json(['message' => 'Client modifié']);
    }
    public static function destroy($id) {
        $db = getDB();
        Client::delete($db, $id);
        \Flight::json(['message' => 'Client supprimé']);
    }
    public static function indexView() {
        $db = getDB();
        $clients = Client::getAll($db);
        \Flight::render('clients/index.php', ['clients' => $clients]);
    }
}
