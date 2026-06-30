<?php
session_start();

$basePath = '../';
$pageTitle = 'Connexion - MY LAVAGE';
$message = '';
$messageClass = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php';

    try {
        if (!isset($_POST['email'], $_POST['password'])) {
            throw new Exception('Veuillez remplir le formulaire.');
        }

        $email = trim(strip_tags($_POST['email']));
        $password = trim(strip_tags($_POST['password']));

        if ($email == '' || $password == '') {
            throw new Exception('Veuillez remplir tous les champs.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Adresse email invalide.');
        }

        $requete = $bdd->prepare(
            'SELECT id_utilisateur, nom, prenom, email, password, id_role
             FROM utilisateur
             WHERE email = :email'
        );
        $requete->execute([
            'email' => $email
        ]);
        $utilisateur = $requete->fetch();

        if (!$utilisateur || !password_verify($password, $utilisateur['password'])) {
            throw new Exception('Email ou mot de passe incorrect.');
        }

        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        $_SESSION['email'] = $utilisateur['email'];
        $_SESSION['id_role'] = $utilisateur['id_role'];

        header('Location: ../index.php');
        exit;
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageClass = 'alert-error';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

    <main class="main-service">
        <section class="auth-box">
            <h1>Connexion</h1>

            <?php if ($message != ''): ?>
                <div class="alert <?= $messageClass ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="./connexion.php" method="post">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?= htmlspecialchars($email) ?>" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>

                <button type="submit">Se connecter</button>
            </form>
            <p class="auth-link">Pas encore de compte ? <a href="./inscription.php">Creer un compte</a></p>
        </section>
    </main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
