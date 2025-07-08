<?php
require_once __DIR__ . '/ws/db.php';
require_once __DIR__ . '/fpdf/fpdf.php';

// Vérifier si idPret est fourni
if (!isset($_GET['idPret']) || !is_numeric($_GET['idPret'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'ID du prêt manquant ou invalide']);
    exit;
}

$idPret = (int)$_GET['idPret'];
$pdo = getDB();

try {
    // Récupérer les détails du prêt
    $sql = "
        SELECT 
            p.idPret, p.montant, p.dureeMois, p.dateDemande, p.dateAccord, p.interets, p.dateRetourEstimee,
            c.nom, c.prenom, c.email,
            t.libelle, t.tauxInteret,
            ef.nomEtablissementFinancier
        FROM Pret_EF p
        INNER JOIN Client_EF c ON p.idClient = c.idClient
        INNER JOIN TypePret_EF t ON p.idTypePret = t.idTypePret
        INNER JOIN EtablissementFinancier_EF ef ON p.idEtablissementFinancier = ef.idEtablissementFinancier
        WHERE p.idPret = :idPret
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['idPret' => $idPret]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Prêt non trouvé']);
        exit;
    }

    // Créer le PDF
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'Contrat de Pret', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            $this->Cell(0, 10, 'Genere le ' . date('d/m/Y'), 0, 0, 'R');
        }
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Informations sur l'EF
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Etablissement Financier', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, utf8_decode($pret['nomEtablissementFinancier']), 0, 1);
    $pdf->Ln(5);

    // Informations sur le client
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Client', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'Nom: ' . utf8_decode($pret['nom'] . ' ' . $pret['prenom']), 0, 1);
    $pdf->Cell(0, 8, 'Email: ' . utf8_decode($pret['email']), 0, 1);
    $pdf->Ln(5);

    // Détails du prêt
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Details du Pret', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'ID Pret: ' . $pret['idPret'], 0, 1);
    $pdf->Cell(0, 8, 'Type de Pret: ' . utf8_decode($pret['libelle']), 0, 1);
    $pdf->Cell(0, 8, 'Montant: ' . number_format($pret['montant'], 2) . ' EUR', 0, 1);
    $pdf->Cell(0, 8, 'Taux d\'interet: ' . number_format($pret['tauxInteret'], 2) . '% par an', 0, 1);
    $pdf->Cell(0, 8, 'Duree: ' . $pret['dureeMois'] . ' mois', 0, 1);
    $pdf->Cell(0, 8, 'Date de demande: ' . date('d/m/Y', strtotime($pret['dateDemande'])), 0, 1);
    $pdf->Cell(0, 8, 'Date d\'accord: ' . date('d/m/Y', strtotime($pret['dateAccord'])), 0, 1);
    $pdf->Cell(0, 8, 'Date de retour estimee: ' . date('d/m/Y', strtotime($pret['dateRetourEstimee'])), 0, 1);
    $pdf->Cell(0, 8, 'Interets totaux: ' . number_format($pret['interets'], 2) . ' EUR', 0, 1);
    $pdf->Ln(10);

    // Tableau d'amortissement
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Tableau d\'Amortissement', 0, 1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 8, 'Periode', 1);
    $pdf->Cell(35, 8, 'Mensualite (EUR)', 1);
    $pdf->Cell(35, 8, 'Interets (EUR)', 1);
    $pdf->Cell(35, 8, 'Capital (EUR)', 1);
    $pdf->Cell(35, 8, 'Capital Restant (EUR)', 1);
    $pdf->Ln();

    // Calculer l'annuité constante
    $montant = $pret['montant'];
    $tauxMensuel = $pret['tauxInteret'] / (12 * 100);
    $dureeMois = $pret['dureeMois'];
    $annuite = $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $dureeMois)) / (pow(1 + $tauxMensuel, $dureeMois) - 1);

    $capitalRestant = $montant;
    $dateDebut = new DateTime($pret['dateAccord']);
    $dateDebut->modify('first day of next month'); // Premier remboursement au mois suivant

    $pdf->SetFont('Arial', '', 10);
    for ($mois = 1; $mois <= $dureeMois; $mois++) {
        $interets = $capitalRestant * $tauxMensuel;
        $capitalRembourse = $annuite - $interets;
        $capitalRestant -= $capitalRembourse;

        // Éviter les petites erreurs d'arrondi sur le dernier mois
        if ($mois == $dureeMois && abs($capitalRestant) < 0.01) {
            $capitalRestant = 0;
        }

        $periode = $dateDebut->format('Y-m');
        $pdf->Cell(30, 8, $periode, 1);
        $pdf->Cell(35, 8, number_format($annuite, 2), 1, 0, 'R');
        $pdf->Cell(35, 8, number_format($interets, 2), 1, 0, 'R');
        $pdf->Cell(35, 8, number_format($capitalRembourse, 2), 1, 0, 'R');
        $pdf->Cell(35, 8, number_format(max(0, $capitalRestant), 2), 1, 0, 'R');
        $pdf->Ln();

        $dateDebut->modify('+1 month');
    }

    // Générer le PDF
    $pdf->Output('D', 'pret_' . $pret['idPret'] . '.pdf');
} catch (PDOException $e) {
    error_log("Erreur SQL dans export_pret_pdf: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
    exit;
}
?>