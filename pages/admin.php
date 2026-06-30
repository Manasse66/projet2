<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['id_role'] != 2) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = '';
$messageClass = '';
$dossierImage = __DIR__ . '/../assets/img/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ajouter_service'])) {
        try {
            $nomService = trim(strip_tags($_POST['nom_service']));
            $description = trim(strip_tags($_POST['description']));
            $prix = trim(strip_tags($_POST['prix']));
            $duree = trim(strip_tags($_POST['duree']));
            $statut = 'activer';

            if (empty($nomService) || empty($description) || empty($prix) || empty($duree)) {
                throw new Exception('Remplis les champs obligatoires.');
            }

            if (empty($_FILES['image_service']['name'])) {
                throw new Exception('Choisis une image pour le service.');
            }

            $file = $_FILES['image_service'];
            $imageService = basename($file['name']);
            $cheminImage = $dossierImage . $imageService;

            if (!move_uploaded_file($file['tmp_name'], $cheminImage)) {
                throw new Exception('Erreur pendant l envoi de l image.');
            }

            $requete = $bdd->prepare(
                'INSERT INTO service (nom_service, description, prix, duree, image_service, statut)
                 VALUES (:nom_service, :description, :prix, :duree, :image_service, :statut)'
            );
            $requete->execute([
                'nom_service' => $nomService,
                'description' => $description,
                'prix' => $prix,
                'duree' => $duree,
                'image_service' => $imageService,
                'statut' => $statut
            ]);

            $message = 'Service ajoute avec succes.';
            $messageClass = 'alert-success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageClass = 'alert-error';
        }
    }

    if (isset($_POST['modifier_service'])) {
        try {
            $idService = trim(strip_tags($_POST['id_service']));
            $nomService = trim(strip_tags($_POST['nom_service']));
            $description = trim(strip_tags($_POST['description']));
            $prix = trim(strip_tags($_POST['prix']));
            $duree = trim(strip_tags($_POST['duree']));
            $imageService = trim(strip_tags($_POST['ancienne_image']));
            $statut = trim(strip_tags($_POST['statut']));

            if (empty($idService) || empty($nomService) || empty($description) || empty($prix) || empty($duree) || empty($statut)) {
                throw new Exception('Impossible de modifier ce service.');
            }

            if (!empty($_FILES['image_service']['name'])) {
                $file = $_FILES['image_service'];
                $imageService = basename($file['name']);
                $cheminImage = $dossierImage . $imageService;

                if (!move_uploaded_file($file['tmp_name'], $cheminImage)) {
                    throw new Exception('Erreur pendant l envoi de l image.');
                }
            }

            $requete = $bdd->prepare(
                'UPDATE service
                 SET nom_service = :nom_service,
                     description = :description,
                     prix = :prix,
                     duree = :duree,
                     image_service = :image_service,
                     statut = :statut
                 WHERE id_service = :id_service'
            );
            $requete->execute([
                'nom_service' => $nomService,
                'description' => $description,
                'prix' => $prix,
                'duree' => $duree,
                'image_service' => $imageService,
                'statut' => $statut,
                'id_service' => $idService
            ]);

            $message = 'Service modifie avec succes.';
            $messageClass = 'alert-success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageClass = 'alert-error';
        }
    }

    if (isset($_POST['desactiver_service'])) {
        $idService = trim(strip_tags($_POST['id_service']));
        $requete = $bdd->prepare('UPDATE service SET statut = :statut WHERE id_service = :id_service');
        $requete->execute([
            'statut' => 'desactiver',
            'id_service' => $idService
        ]);
        $message = 'Service desactive.';
        $messageClass = 'alert-success';
    }

    if (isset($_POST['supprimer_service'])) {
        $idService = trim(strip_tags($_POST['id_service']));
        $requete = $bdd->prepare('UPDATE service SET statut = :statut WHERE id_service = :id_service');
        $requete->execute([
            'statut' => 'supprimer',
            'id_service' => $idService
        ]);
        $message = 'Service supprime.';
        $messageClass = 'alert-success';
    }

    if (isset($_POST['accepter_reservation']) || isset($_POST['refuser_reservation'])) {
        try {
            $idReservation = trim(strip_tags($_POST['id_reservation']));
            $contenu = trim(strip_tags($_POST['contenu']));
            $statutReservation = isset($_POST['accepter_reservation']) ? 'accepter' : 'refuser';

            if (empty($idReservation) || empty($contenu)) {
                throw new Exception('Ecris un message pour le client.');
            }

            $requete = $bdd->prepare('SELECT statut FROM reservation WHERE id_reservation = :id_reservation');
            $requete->execute([
                'id_reservation' => $idReservation
            ]);
            $reservation = $requete->fetch();

            if (!$reservation || $reservation['statut'] != 'en attente') {
                throw new Exception('Cette reservation a deja ete traitee.');
            }

            $requete = $bdd->prepare('UPDATE reservation SET statut = :statut WHERE id_reservation = :id_reservation');
            $requete->execute([
                'statut' => $statutReservation,
                'id_reservation' => $idReservation
            ]);

            $requete = $bdd->prepare(
                'INSERT INTO message (contenu, date_message, id_reservation, id_role)
                 VALUES (:contenu, NOW(), :id_reservation, :id_role)'
            );
            $requete->execute([
                'contenu' => $contenu,
                'id_reservation' => $idReservation,
                'id_role' => 2
            ]);

            $message = 'Reservation traitee avec succes.';
            $messageClass = 'alert-success';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageClass = 'alert-error';
        }
    }
}

$requete = $bdd->query('SELECT id_service, nom_service, description, prix, duree, image_service, statut FROM service ORDER BY id_service DESC');
$services = $requete->fetchAll();

$requete = $bdd->query(
    'SELECT reservation.id_reservation,
            reservation.date_reservation,
            reservation.statut,
            reservation.numreservation,
            service.nom_service,
            utilisateur.nom,
            utilisateur.prenom,
            utilisateur.email
     FROM reservation
     INNER JOIN service ON reservation.id_service = service.id_service
     INNER JOIN utilisateur ON reservation.id_utilisateur = utilisateur.id_utilisateur
     ORDER BY reservation.id_reservation DESC'
);
$reservations = $requete->fetchAll();

$pageTitle = 'Admin - MY LAVAGE';
$pageActive = 'admin';
require_once __DIR__ . '/../includes/header.php';
?>

    <main class="main-service">
        <section class="admin-box">
            <h1>Administration</h1>
            <p>Bienvenue <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>.</p>

            <?php if (!empty($message)): ?>
                <div class="alert <?= $messageClass ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <h2>Ajouter un service</h2>
            <form class="auth-form" method="post" enctype="multipart/form-data">
                <label for="nom_service">Nom du service</label>
                <input type="text" name="nom_service" id="nom_service" required>

                <label for="description">Description</label>
                <input type="text" name="description" id="description" required>

                <label for="prix">Prix</label>
                <input type="number" step="0.01" name="prix" id="prix" required>

                <label for="duree">Duree</label>
                <input type="number" name="duree" id="duree" required>

                <label for="image_service">Image dans assets/img</label>
                <input type="file" name="image_service" id="image_service" accept="image/*" required>

                <button type="submit" name="ajouter_service">Ajouter</button>
            </form>
        </section>

        <section class="admin-list">
            <h2>Services</h2>

            <?php foreach ($services as $service): ?>
                <form class="admin-service" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_service" value="<?= htmlspecialchars($service['id_service']) ?>">
                    <input type="hidden" name="ancienne_image" value="<?= htmlspecialchars($service['image_service'] ?? '') ?>">

                    <label>Nom</label>
                    <input type="text" name="nom_service" value="<?= htmlspecialchars($service['nom_service']) ?>" required>

                    <label>Description</label>
                    <input type="text" name="description" value="<?= htmlspecialchars($service['description']) ?>" required>

                    <label>Prix</label>
                    <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($service['prix']) ?>" required>

                    <label>Duree</label>
                    <input type="number" name="duree" value="<?= htmlspecialchars($service['duree']) ?>" required>

                    <label>Image</label>
                    <input type="file" name="image_service" accept="image/*">

                    <label>Statut</label>
                    <select name="statut">
                        <option value="activer" <?= $service['statut'] == 'activer' ? 'selected' : '' ?>>activer</option>
                        <option value="desactiver" <?= $service['statut'] == 'desactiver' ? 'selected' : '' ?>>desactiver</option>
                        <option value="supprimer" <?= $service['statut'] == 'supprimer' ? 'selected' : '' ?>>supprimer</option>
                    </select>

                    <div class="admin-actions">
                        <button type="submit" name="modifier_service">Modifier</button>
                        <button type="submit" name="desactiver_service">Desactiver</button>
                        <button type="submit" name="supprimer_service">Supprimer</button>
                    </div>
                </form>
            <?php endforeach; ?>
        </section>

        <section class="admin-list">
            <h2>Reservations</h2>

            <?php if (empty($reservations)): ?>
                <p>Aucune reservation pour le moment.</p>
            <?php endif; ?>

            <?php foreach ($reservations as $reservation): ?>
                <div class="admin-reservation">
                    <p><strong>Numero :</strong> <?= htmlspecialchars($reservation['numreservation']) ?></p>
                    <p><strong>Client :</strong> <?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($reservation['email']) ?></p>
                    <p><strong>Service :</strong> <?= htmlspecialchars($reservation['nom_service']) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($reservation['date_reservation']) ?></p>
                    <p><strong>Statut :</strong> <?= htmlspecialchars($reservation['statut']) ?></p>

                    <?php if ($reservation['statut'] == 'en attente'): ?>
                        <form method="post">
                            <input type="hidden" name="id_reservation" value="<?= htmlspecialchars($reservation['id_reservation']) ?>">

                            <label>Message pour le client</label>
                            <textarea name="contenu" required placeholder="Exemple : votre reservation est acceptee, on vous attend a 10h."></textarea>

                            <div class="admin-actions">
                                <button type="submit" name="accepter_reservation">Accepter</button>
                                <button type="submit" name="refuser_reservation">Refuser</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p>Reservation deja traitee.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
