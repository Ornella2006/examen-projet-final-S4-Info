<?php
$page = isset($_GET['page']) ? basename($_GET['page']) : 'dashboard';

// Définir le chemin de la page
if ($page === 'interets_ef' || $page === 'ajouter_fonds' || $page === 'types-prets' || $page === 'prets' || $page === 'simulers') {
    $pagePath = __DIR__ . '/../' . $page . '.php'; // Cherche dans la racine
} else {
    $pagePath = __DIR__ . '/' . $page . '.php'; // Cherche dans template/
}

// Si le fichier n'existe pas, utiliser dashboard.php
if (!file_exists($pagePath)) {
    $page = 'dashboard';
    $pagePath = __DIR__ . '/dashboard.php';
}

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
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
    <?php if ($page === 'types-prets'): ?>
        <link rel="stylesheet" href="../css/types-prets.css">
    <?php endif; ?>
    <?php if ($page === 'prets'): ?>
        <link rel="stylesheet" href="../css/prets.css">
    <?php endif; ?>
     <?php if ($page === 'prets'): ?>
        <link rel="stylesheet" href="../css/simulers.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* Sidebar Styles */
.sidebar {
    background-color: #003366; /* Bleu marine */
    color: white;
    width: 280px;
    height: 100vh;
    position: fixed;
    transition: all 0.3s;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    padding: 20px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
    font-size: 24px;
    font-weight: bold;
    margin-right: 10px;
    color: #66b3ff; /* Bleu clair */
}

.brand-name {
    font-size: 18px;
    font-weight: 600;
}

.sidebar-menu {
    padding: 20px 0;
    overflow-y: auto;
    height: calc(100vh - 60px);
}

.menu-title {
    padding: 15px 20px 5px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

.menu-item {
    margin: 5px 0;
    position: relative;
}

.menu-item a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    font-weight: 400;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.menu-item a:hover {
    background-color: rgba(255, 255, 255, 0.05);
    color: white;
    border-left-color: #66b3ff; /* Bleu clair */
}

.menu-item.active a {
    background-color: rgba(102, 179, 255, 0.1); /* Bleu clair avec transparence */
    color: white;
    border-left-color: #66b3ff; /* Bleu clair */
    font-weight: 500;
}

.menu-item a i {
    margin-right: 12px;
    font-size: 16px;
    width: 20px;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
}

.menu-item:hover a i,
.menu-item.active a i {
    color: #66b3ff; /* Bleu clair */
}

.menu-item a span {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Effet de surbrillance au survol */
.menu-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background-color: rgba(102, 179, 255, 0.1); /* Bleu clair avec transparence */
    transition: width 0.3s ease;
    z-index: 0;
}

.menu-item:hover::before {
    width: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        overflow: hidden;
    }
    
    .brand-name, 
    .menu-item span {
        display: none;
    }
    
    .menu-item a {
        justify-content: center;
        padding: 15px 0;
    }
    
    .menu-item a i {
        margin-right: 0;
        font-size: 18px;
    }
    
    .menu-title {
        display: none;
    }
}
</style>
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
                <a href="template.php?page=interets_ef"><i class="fas fa-chart-pie"></i><span>Interets par EF</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'ajouter_fonds' ? ' active' : ''; ?>">
                <a href="template.php?page=ajouter_fonds"><i class="fas fa-money-bill-wave"></i><span>Ajout de fond</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'types-prets' ? ' active' : ''; ?>">
                <a href="template.php?page=types-prets"><i class="fas fa-percentage"></i><span>Création type de prêt</span></a>
            </div>
            <div class="menu-item<?php echo $page === 'prets' ? ' active' : ''; ?>">
                <a href="template.php?page=prets"><i class="fas fa-hand-holding-usd"></i><span>Gestion de prêt clients</span></a>
            </div>

             <div class="menu-item<?php echo $page === 'simulers' ? ' active' : ''; ?>">
                <a href="template.php?page=simulers"><i class="fas fa-chart-line"></i><span>Simulation</span></a>
            </div>

            <div class="menu-item">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a>
            </div>

           

            <!-- <div class="menu-title">Services Financiers</div>
            <div class="menu-item<php echo $page === 'cartes' ? ' active' : ''; ?>">
                <a href="template.php?page=cartes"><i class="fas fa-credit-card"></i><span>Cartes & Crédits</span></a>
            </div>
            <div class="menu-item<php echo $page === 'investissements' ? ' active' : ''; ?>">
                <a href="template.php?page=investissements"><i class="fas fa-percentage"></i><span>Taux & Investissements</span></a>
            </div>
            <div class="menu-item<php echo $page === 'prets' ? ' active' : ''; ?>">
                <a href="template.php?page=prets"><i class="fas fa-hand-holding-usd"></i><span>Prêts & Hypothèques</span></a>
            </div>

            <div class="menu-title">Administration</div>
            <div class="menu-item<php echo $page === 'parametres' ? ' active' : ''; ?>">
                <a href="template.php?page=parametres"><i class="fas fa-cog"></i><span>Paramètres Système</span></a>
            </div>
            <div class="menu-item<php echo $page === 'securite' ? ' active' : ''; ?>">
                <a href="template.php?page=securite"><i class="fas fa-shield-alt"></i><span>Sécurité & Conformité</span></a>
            </div>
            <div class="menu-item<php echo $page === 'support' ? ' active' : ''; ?>">
                <a href="template.php?page=support"><i class="fas fa-headset"></i><span>Support Premium</span></a>
            </div> -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php include($pagePath); ?>
    </div>
</body>
</html>