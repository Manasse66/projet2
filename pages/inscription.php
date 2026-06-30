<?php
$basePath = '../';
$pageTitle = 'Inscription - MY LAVAGE';
require_once __DIR__ . '/../includes/header.php';
?>

    <main class="main-service">
        <section class="auth-box">
            <h1>Inscription</h1>
            <form class="auth-form" action="#" method="post">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Votre nom" required>

                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>

                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>

                <button type="submit">S'inscrire</button>
            </form>
            <p class="auth-link">Déjà un compte ? <a href="./connexion.php">Se connecter</a></p>
        </section>
    </main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

