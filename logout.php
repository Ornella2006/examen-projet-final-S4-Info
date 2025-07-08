<?php
session_start();

// Détruire toutes les données de la session
session_unset();
session_destroy();

// Rediriger vers login.php
header("Location: login.php");
exit;
?>