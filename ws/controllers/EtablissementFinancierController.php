<?php
require_once __DIR__ . '/../models/EtablissementFinancier.php';
require_once __DIR__ . '/../helpers/Utils.php';


class EtablissementFinancierController {
    public static function getAll() {
        $etablissements = EtablissementFinancier::getAll();
        Flight::json($etablissements);
    }




    public static function getById($id) {
        $etablissement = EtablissementFinancier::getById($id);
        if (!$etablissement) {
            Flight::json(['error' => 'Établissement non trouvé'], 404);
            return;
        }
        Flight::json($etablissement);
    }

    public static function create() {
        $data = Flight::request()->data;
        if (empty($data->nomEtablissementFinancier) || !isset($data->fondTotal) || $data->fondTotal < 0) {
            Flight::json(['error' => 'Nom et fonds total requis, fonds total doit être positif'], 400);
            return;
        }
        $id = EtablissementFinancier::create($data);
        Flight::json(['message' => 'Établissement financier ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        // Log des données reçues
        error_log("Données reçues pour PUT /etablissements/$id: " . print_r($data, true));
        
        if (empty($data->nomEtablissementFinancier) || !isset($data->fondTotal) || !is_numeric($data->fondTotal) || $data->fondTotal < 0) {
            Flight::json(['error' => 'Nom et fonds total requis, fonds total doit être un nombre positif'], 400);
            return;
        }
        $affected = EtablissementFinancier::update($id, $data);
        if ($affected === 0) {
            Flight::json(['error' => 'Établissement non trouvé ou aucune modification effectuée'], 404);
            return;
        }
        Flight::json(['message' => 'Établissement financier modifié']);
    }

    public static function delete($id) {
        $affected = EtablissementFinancier::delete($id);
        if ($affected === 0) {
            Flight::json(['error' => 'Établissement non trouvé'], 404);
            return;
        }
        Flight::json(['message' => 'Établissement financier supprimé']);
    }

    public static function getAllEtablissement() {
        $etablissements = EtablissementFinancier::getAllEtablissement();
        Flight::json($etablissements);
    }

    public static function ajouterFonds($idEtablissement) {
        $data = Flight::request()->data;
        $montant = $data->montant ?? null;
        $dateAjout = $data->dateAjout ?? null;

        if (!$montant || $montant <= 0) {
            Flight::json(['error' => 'Montant invalide'], 400);
            return;
        }

        $result = EtablissementFinancier::addFonds($idEtablissement, $montant, $dateAjout);
        if (isset($result['error'])) {
            Flight::json(['error' => $result['error']], 400);
        } else {
            Flight::json($result, 200);
        }
    }

      public static function getMonthlyAvailableFunds($idEtablissementFinancier = null) {
        $db = getDB();
        $startMonth = isset($_GET['start_month']) ? $_GET['start_month'] : date('Y-m-01');
        $endMonth = isset($_GET['end_month']) ? $_GET['end_month'] : date('Y-m-t', strtotime('now'));

        // Validate and format dates
        $start = DateTime::createFromFormat('Y-m-d', $startMonth);
        $end = DateTime::createFromFormat('Y-m-d', $endMonth);
        if (!$start || !$end || $start > $end) {
            Flight::json(['error' => 'Dates invalides ou plage incorrecte'], 400);
            return;
        }

        $results = [];
        $current = clone $start;
        $end = clone $end;

        // Get initial fund total
        $initialFundStmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $initialFundStmt->execute([$idEtablissementFinancier]);
        $initialFund = $initialFundStmt->fetchColumn();
        if ($initialFund === false) {
            Flight::json(['error' => 'Établissement financier non trouvé'], 404);
            return;
        }
        $availableFunds = $initialFund;

        // Calculate cumulative loans and repayments up to start month
        $startYearMonth = $start->format('Y-m');
        $cumulativeLoansStmt = $db->prepare("
            SELECT COALESCE(SUM(montant), 0) as totalLoans 
            FROM Pret_EF 
            WHERE idEtablissementFinancier = ? 
            AND dateDemande < ?
        ");
        $cumulativeLoansStmt->execute([$idEtablissementFinancier, $startYearMonth . '-01']);
        $cumulativeLoans = $cumulativeLoansStmt->fetchColumn();

        $cumulativeRepayStmt = $db->prepare("
            SELECT COALESCE(SUM(montantRembourse), 0) as totalRepay 
            FROM Remboursement_EF r
            JOIN Pret_EF p ON r.idPret = p.idPret
            WHERE p.idEtablissementFinancier = ?
            AND r.dateRemboursement < ?
        ");
        $cumulativeRepayStmt->execute([$idEtablissementFinancier, $startYearMonth . '-01']);
        $cumulativeRepay = $cumulativeRepayStmt->fetchColumn();

        $availableFunds -= $cumulativeLoans;
        $availableFunds += $cumulativeRepay;

        while ($current <= $end) {
            $monthYear = $current->format('Y-m');
            $results[$monthYear] = $availableFunds;

            // Add new loans and repayments for the current month
            $loanStmt = $db->prepare("
                SELECT COALESCE(SUM(montant), 0) as totalLoans 
                FROM Pret_EF 
                WHERE idEtablissementFinancier = ? 
                AND DATE_FORMAT(dateDemande, '%Y-%m') = ?
            ");
            $loanStmt->execute([$idEtablissementFinancier, $monthYear]);
            $totalLoans = $loanStmt->fetchColumn();

            $repayStmt = $db->prepare("
                SELECT COALESCE(SUM(montantRembourse), 0) as totalRepay 
                FROM Remboursement_EF r
                JOIN Pret_EF p ON r.idPret = p.idPret
                WHERE p.idEtablissementFinancier = ?
                AND DATE_FORMAT(r.dateRemboursement, '%Y-%m') = ?
            ");
            $repayStmt->execute([$idEtablissementFinancier, $monthYear]);
            $totalRepay = $repayStmt->fetchColumn();

            $availableFunds -= $totalLoans;
            $availableFunds += $totalRepay;

            $current->modify('+1 month');
        }

        Flight::json(array_map('floatval', $results));
    }
}