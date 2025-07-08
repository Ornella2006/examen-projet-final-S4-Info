<?php
class InteretsEFController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function showSynthese() {
        try {
            $debut = Flight::request()->query->debut ?? date('Y-m');
            $fin = Flight::request()->query->fin ?? date('Y-m');
            $dateDebut = $debut . '-01';
            $dateFin = date('Y-m-t', strtotime($fin . '-01')); // Dernier jour du mois

            // Requête pour calculer les intérêts par période et par EF
            $sql = "
                SELECT 
                    DATE_FORMAT(r.dateRemboursement, '%Y-%m') AS periode,
                    ef.idEtablissementFinancier,
                    ef.nomEtablissementFinancier,
                    SUM(p.interets / p.dureeMois) AS total_interets
                FROM Remboursement_EF r
                INNER JOIN Pret_EF p ON r.idPret = p.idPret
                INNER JOIN EtablissementFinancier_EF ef ON p.idEtablissementFinancier = ef.idEtablissementFinancier
                WHERE r.dateRemboursement BETWEEN :date_debut AND :date_fin
                GROUP BY periode, ef.idEtablissementFinancier, ef.nomEtablissementFinancier
                ORDER BY periode, ef.idEtablissementFinancier
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Données des intérêts: " . print_r($data, true));
            Flight::json($data);
        } catch (PDOException $e) {
            error_log("Erreur SQL dans showSynthese: " . $e->getMessage());
            Flight::json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
?>