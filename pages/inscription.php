<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Inscription - MY LAVAGE';
$pageActive = 'inscription';
$message = '';
$messageClass = '';
$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/database.php';

    try {
        if (!isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
            throw new Exception('Veuillez remplir tous les champs du formulaire.');
        }

        $nom = trim(strip_tags($_POST['nom']));
        $prenom = trim(strip_tags($_POST['prenom']));
        $email = trim(strip_tags($_POST['email']));
        $password = htmlspecialchars($_POST['password']);
        $confirmPassword = htmlspecialchars($_POST['confirm_password']);

        if ($nom == '' || $email == '' || $password == '' || $confirmPassword == '') {
            throw new Exception('Veuillez remplir tous les champs obligatoires.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Adresse email invalide.');
        }

        if ($password !== $confirmPassword) {
            throw new Exception('Les mots de passe ne correspondent pas.');
        }

        $requete = $bdd->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
        $requete->execute([
            'email' => $email
        ]);

        if ($requete->fetch()) {
            throw new Exception('Cette adresse email est deja utilisee.');
        }

        $motDePasseHash = password_hash($password, PASSWORD_DEFAULT);
        $idRoleClient = 1;

        $requete = $bdd->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, password, id_role)
             VALUES (:nom, :prenom, :email, :password, :id_role)'
        );
        $requete->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $motDePasseHash,
            'id_role' => $idRoleClient
        ]);

        $_SESSION['id_utilisateur'] = $bdd->lastInsertId();
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;
        $_SESSION['id_role'] = $idRoleClient;

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
            <h1>Inscription</h1>

            <?php if ($message != ''): ?>
                <div class="alert <?= $messageClass ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="./inscription.php" method="post">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?= htmlspecialchars($nom) ?>" required>

                <label for="prenom">Prenom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Votre prenom" value="<?= htmlspecialchars($prenom) ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?= htmlspecialchars($email) ?>" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>

                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>

                <button type="submit">S'inscrire</button>
            </form>
            <p class="auth-link">Deja un compte ? <a href="./connexion.php">Se connecter</a></p>
        </section>
    </main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
