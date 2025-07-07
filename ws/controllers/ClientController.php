<?php
class ClientController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function listerClients() {
        $stmt = $this->pdo->query("SELECT * FROM Client_EF ORDER BY idClient");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Flight::render('clients_list.php', ['clients' => $clients]);
    }
}
