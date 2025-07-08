<?php
$page = isset($_GET['page']) ? basename($_GET['page']) : 'dashboard';

// Définir le chemin de la page
if ($page === 'interets_ef' || $page === 'ajouter_fonds') {
    $pagePath = __DIR__ . '/../' . $page . '.php'; // Cherche dans la racine
} else {
    $pagePath = __DIR__ . '/' . $page . '.php'; // Cherche dans template/
}

// Si le fichier n'existe pas, utiliser dashboard.php
if (!file_exists($pagePath)) {
    $page = 'dashboard';
    $pagePath = __DIR__ . '/dashboard.php';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Finance | <?php echo ucfirst(str_replace('-', ' ', $page)); ?></title>
    <link rel="stylesheet" href="../css/template.css">
     <?php if ($page === 'ajouter_fonds'): ?>
           <link rel="stylesheet" href="../css/ajout_fonds.css">
       <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">$</div>
            <div class="brand-name">FinVision</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-title">Navigation Principale</div>
            <div class="menu-item<?php echo $page === 'dashboard' ? ' active' : ''; ?>">
                <a href="template.php?page=interets_ef"><i class="fas fa-chart-pie"></i><span>Tableau de bord</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'dashboard' ? ' active' : ''; ?>">
                <a href="template.php?page=ajouter_fonds"><i class="fas fa-money-bill-wave"></i><span>Ajout de fond dans l'établissement financier (EF)</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'transactions' ? ' active' : ''; ?>">
                <a href="template.php?page=transactions"><i class="fas fa-exchange-alt"></i><span>Transactions</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'clients' ? ' active' : ''; ?>">
                <a href="template.php?page=clients"><i class="fas fa-users"></i><span>Gestion Clients</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'analytiques' ? ' active' : ''; ?>">
                <a href="template.php?page=analytiques"><i class="fas fa-chart-line"></i><span>Analytiques Financières</span></a>
            </div>

            <div class="menu-title">Services Financiers</div>
            <div class="menu-item<?php echo $page === 'cartes' ? ' active' : ''; ?>">
                <a href="template.php?page=cartes"><i class="fas fa-credit-card"></i><span>Cartes & Crédits</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'investissements' ? ' active' : ''; ?>">
                <a href="template.php?page=investissements"><i class="fas fa-percentage"></i><span>Taux & Investissements</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'prets' ? ' active' : ''; ?>">
                <a href="template.php?page=prets"><i class="fas fa-hand-holding-usd"></i><span>Prêts & Hypothèques</span></a>
            </div>

            <div class="menu-title">Administration</div>
            <div class="menu-item<?php echo $page === 'parametres' ? ' active' : ''; ?>">
                <a href="template.php?page=parametres"><i class="fas fa-cog"></i><span>Paramètres Système</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'securite' ? ' active' : ''; ?>">
                <a href="template.php?page=securite"><i class="fas fa-shield-alt"></i><span>Sécurité & Conformité</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'support' ? ' active' : ''; ?>">
                <a href="template.php?page=support"><i class="fas fa-headset"></i><span>Support Premium</span></a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php include($pagePath); ?>
    </div>
</body>
</html>