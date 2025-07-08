<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/Pret.php';

class Remboursement {
    public static function rembourser($data) {
        $db = getDb();
        
        // Validation des données
        if (!isset($data->idPret) || !is_numeric($data->idPret)) {
            throw new Exception("Identifiant du prêt requis.");
        }
        if (!isset($data->montantRembourse) || !is_numeric($data->montantRembourse) || $data->montantRembourse <= 0) {
            throw new Exception("Le montant remboursé doit être un nombre positif.");
        }
        if (!isset($data->dateRemboursement)) {
            throw new Exception("Date de remboursement requise.");
        }

        $idPret = (int)$data->idPret;
        $montantRembourse = (float)$data->montantRembourse;
        $dateRemboursement = $data->dateRemboursement;

        // Vérifier si le prêt existe et est en statut 'accorde'
        $pretStmt = $db->prepare("
            SELECT p.montant, p.interets, p.tauxAssurance, p.dureeMois, p.statut, p.dateAccord, p.delaiPremierRemboursementMois, t.tauxInteret
            FROM Pret_EF p
            JOIN TypePret_EF t ON p.idTypePret = t.idTypePret
            WHERE p.idPret = ?
        ");
        $pretStmt->execute([$idPret]);
        $pret = $pretStmt->fetch(PDO::FETCH_ASSOC);

        if (!$pret) {
            throw new Exception("Le prêt n'existe pas.");
        }
        if ($pret['statut'] !== 'accorde') {
            throw new Exception("Le prêt n'est pas en statut accordé.");
        }
        if (!$pret['dateAccord']) {
            throw new Exception("La date d'accord du prêt n'est pas définie.");
        }

        // Calculer l'annuité mensuelle
        $tauxMensuel = ($pret['tauxInteret'] + $pret['tauxAssurance']) / 12 / 100;
        $annuiteMensuelle = $pret['montant'] * ($tauxMensuel * pow(1 + $tauxMensuel, $pret['dureeMois'])) / (pow(1 + $tauxMensuel, $pret['dureeMois']) - 1);

        if (abs($montantRembourse - $annuiteMensuelle) > 0.01) {
            throw new Exception("Le montant remboursé doit être égal à l'annuité mensuelle (" . number_format($annuiteMensuelle, 2) . " €).");
        }

        // Vérifier la date de remboursement
        $minDatePremierRemboursement = date('Y-m-d', strtotime($pret['dateAccord'] . ' + ' . $pret['delaiPremierRemboursementMois'] . ' months'));
        $lastRembStmt = $db->prepare("SELECT MAX(dateRemboursement) as lastDate FROM Remboursement_EF WHERE idPret = ?");
        $lastRembStmt->execute([$idPret]);
        $lastDate = $lastRembStmt->fetch(PDO::FETCH_ASSOC)['lastDate'];

        $minDate = $lastDate ? date('Y-m-d', strtotime($lastDate . ' +1 day')) : $minDatePremierRemboursement;
        if ($dateRemboursement < $minDate) {
            throw new Exception("La date de remboursement ne peut pas être antérieure à $minDate.");
        }

        // Calculer le solde restant
        $sumRembStmt = $db->prepare("SELECT COALESCE(SUM(montantRembourse), 0) as totalRembourse FROM Remboursement_EF WHERE idPret = ?");
        $sumRembStmt->execute([$idPret]);
        $totalRembourse = $sumRembStmt->fetch(PDO::FETCH_ASSOC)['totalRembourse'];

        $nouveauTotalRembourse = $totalRembourse + $montantRembourse;
        $totalADeduire = $pret['montant'] + $pret['interets'];
        $soldeRestant = $totalADeduire - $nouveauTotalRembourse;

        // Insérer le remboursement
        $stmt = $db->prepare("INSERT INTO Remboursement_EF (idPret, montantRembourse, dateRemboursement) VALUES (?, ?, ?)");
        $stmt->execute([$idPret, $montantRembourse, $dateRemboursement]);
        $idRemboursement = $db->lastInsertId();

        // Mettre à jour le statut du prêt si entièrement remboursé
        if ($soldeRestant <= 0.01) {
            $updateStmt = $db->prepare("UPDATE Pret_EF SET statut = 'rembourse' WHERE idPret = ?");
            $updateStmt->execute([$idPret]);
        }

        return $idRemboursement;
    }

    public static function getRemboursements($idPret = null) {
        $db = getDb();
        if ($idPret) {
            $stmt = $db->prepare("
                SELECT r.*, c.nom, c.prenom 
                FROM Remboursement_EF r 
                JOIN Pret_EF p ON r.idPret = p.idPret 
                JOIN Client_EF c ON p.idClient = c.idClient 
                WHERE r.idPret = ?
                ORDER BY r.dateRemboursement DESC
            ");
            $stmt->execute([$idPret]);
        } else {
            $stmt = $db->prepare("
                SELECT r.*, c.nom, c.prenom 
                FROM Remboursement_EF r 
                JOIN Pret_EF p ON r.idPret = p.idPret 
                JOIN Client_EF c ON p.idClient = c.idClient
                ORDER BY r.dateRemboursement DESC
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}