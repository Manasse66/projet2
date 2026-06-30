<?php
$basePath = '../';
$pageTitle = 'Connexion - MY LAVAGE';
require_once __DIR__ . '/../includes/header.php';
?>

    <main class="main-service">
        <section class="auth-box">
            <h1>Connexion</h1>
            <form class="auth-form" action="#" method="post">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>

                <button type="submit">Se connecter</button>
            </form>
            <p class="auth-link">Pas encore de compte ? <a href="./inscription.php">Créer un compte</a></p>
        </section>
    </main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

