<?php
require_once __DIR__ . '/db.php';

// Inclure FPDF (version sans Composer)
require_once __DIR__ . '/fpdf/fpdf.php';

$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM Client_EF ORDER BY idClient");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF('L', 'mm', 'A4'); // Paysage, pour plus de largeur
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Liste des clients',0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'ID',1);
$pdf->Cell(40,10,'Nom',1);
$pdf->Cell(40,10,'Prenom',1);
$pdf->Cell(80,10,'Adresse',1);
$pdf->Cell(35,10,'Telephone',1);
$pdf->Cell(60,10,'Email',1);
$pdf->Ln();
$pdf->SetFont('Arial','',11);
foreach($clients as $c) {
    $pdf->Cell(20,8,$c['idClient'],1);
    $pdf->Cell(40,8,utf8_decode($c['nom']),1);
    $pdf->Cell(40,8,utf8_decode($c['prenom']),1);
    $pdf->Cell(80,8,utf8_decode($c['adresse']),1);
    $pdf->Cell(35,8,utf8_decode($c['telephone']),1);
    $pdf->Cell(60,8,utf8_decode($c['email']),1);
    $pdf->Ln();
}
$pdf->Output('D', 'clients.pdf');
