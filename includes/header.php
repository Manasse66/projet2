<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = $basePath ?? './';
$pageTitle = $pageTitle ?? 'MY LAVAGE';
$currentPage = basename($_SERVER['PHP_SELF']);
$isConnected = isset($_SESSION['id_utilisateur']);

$navLinks = [
    ['href' => 'index.php', 'label' => 'Accueil', 'page' => 'index.php'],
    ['href' => 'pages/services.php', 'label' => 'Services', 'page' => 'services.php'],
    ['href' => 'pages/contact.php', 'label' => 'Contact', 'page' => 'contact.php'],
];

if ($isConnected) {
    $navLinks[] = ['href' => 'pages/deconnexion.php', 'label' => 'Deconnecter', 'page' => 'deconnexion.php'];
} else {
    $navLinks[] = ['href' => 'pages/connexion.php', 'label' => 'Connexion', 'page' => 'connexion.php'];
    $navLinks[] = ['href' => 'pages/inscription.php', 'label' => 'Inscription', 'page' => 'inscription.php'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
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

            <?php foreach ($navLinks as $link): ?>
                <a href="<?= $basePath . $link['href'] ?>" class="<?= $currentPage === $link['page'] ? 'active' : '' ?>">
                    <?= $link['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>
