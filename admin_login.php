<?php
session_start();
require_once 'ws/db.php';

$nom = $_POST['nom'] ?? '';
$motDePasse = $_POST['motDePasse'] ?? '';

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM Admin WHERE nom = ? AND motDePasse = ?");
$stmt->execute([$nom, $motDePasse]);
$admin = $stmt->fetch();

if ($admin) {
    $_SESSION['admin'] = $admin['nom'];
    header("Location: dashboard.html");  
    exit;
} else {
    echo "Nom ou mot de passe incorrect.";
}
