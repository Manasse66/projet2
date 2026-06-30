<?php
$basePath = $basePath ?? './';
$pageTitle = $pageTitle ?? 'MY LAVAGE';
$currentPage = basename($_SERVER['PHP_SELF']);

$navLinks = [
    ['href' => 'index.php', 'label' => 'Accueil', 'page' => 'index.php'],
    ['href' => 'pages/services.php', 'label' => 'Services', 'page' => 'services.php'],
    ['href' => 'pages/contact.php', 'label' => 'Contact', 'page' => 'contact.php'],
    ['href' => 'pages/connexion.php', 'label' => 'Connexion', 'page' => 'connexion.php'],
    ['href' => 'pages/inscription.php', 'label' => 'Inscription', 'page' => 'inscription.php'],
];
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
            <?php foreach ($navLinks as $link): ?>
                <a href="<?= $basePath . $link['href'] ?>" class="<?= $currentPage === $link['page'] ? 'active' : '' ?>">
                    <?= $link['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>
