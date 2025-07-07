<?php
class InteretsEFController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function showSynthese() {
        $debut = $_GET['debut'] ?? date('Y-m');
        $fin = $_GET['fin'] ?? date('Y-m');
        $dateDebut = $debut . '-01';
        $dateFin = $fin . '-31';
        $sql = "SELECT DATE_FORMAT(date_remboursement, '%Y-%m') AS periode, SUM(interet) AS total_interets FROM Remboursement_EF WHERE date_remboursement BETWEEN :date_debut AND :date_fin GROUP BY periode ORDER BY periode";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':date_debut' => $dateDebut, ':date_fin' => $dateFin]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Flight::json($data);

    }
}
