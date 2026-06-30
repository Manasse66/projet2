<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'MY LAVAGE';
$pageActive = $pageActive ?? '';
$isConnected = isset($_SESSION['id_utilisateur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title><?= $pageTitle ?></title>
</head>
<body>
    <header class="header">
        <h1 class="header-title">MY LAVAGE</h1>
        <nav class="header-nav">
            <?php if ($isConnected): ?>
                <span class="user-name">
                    <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
                </span>
            <?php endif; ?>

            <a href="/index.php" class="<?= $pageActive == 'accueil' ? 'active' : '' ?>">Accueil</a>
            <a href="/pages/services.php" class="<?= $pageActive == 'services' ? 'active' : '' ?>">Services</a>
            <a href="/pages/contact.php" class="<?= $pageActive == 'contact' ? 'active' : '' ?>">Contact</a>

            <?php if ($isConnected): ?>
                <?php if ($_SESSION['id_role'] == 2): ?>
                    <a href="/pages/admin.php" class="<?= $pageActive == 'admin' ? 'active' : '' ?>">Admin</a>
                <?php endif; ?>
                <a href="/pages/reservation.php" class="<?= $pageActive == 'reservation' ? 'active' : '' ?>">Mes reservations</a>
                <a href="/pages/deconnexion.php">Deconnecter</a>
            <?php else: ?>
                <a href="/pages/connexion.php" class="<?= $pageActive == 'connexion' ? 'active' : '' ?>">Connexion</a>
                <a href="/pages/inscription.php" class="<?= $pageActive == 'inscription' ? 'active' : '' ?>">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>
