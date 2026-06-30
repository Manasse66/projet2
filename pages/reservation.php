<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: ./connexion.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = '';
$messageClass = '';
$idUtilisateur = $_SESSION['id_utilisateur'];
$idService = isset($_GET['id_service']) ? trim(strip_tags($_GET['id_service'])) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $idService = trim(strip_tags($_POST['id_service']));
        $dateReservation = trim(strip_tags($_POST['date_reservation']));
        $statut = 'en attente';
        $numreservation = strtoupper(uniqid('RES-'));

        if (empty($idService) || empty($dateReservation)) {
            throw new Exception('Choisis une date et une heure.');
        }

        $dateReservation = str_replace('T', ' ', $dateReservation);

        $requete = $bdd->prepare('SELECT id_service FROM service WHERE id_service = :id_service AND statut = :statut');
        $requete->execute([
            'id_service' => $idService,
            'statut' => 'activer'
        ]);

        if (!$requete->fetch()) {
            throw new Exception('Ce service n est pas disponible.');
        }

        $requete = $bdd->prepare(
            'INSERT INTO reservation (date_reservation, statut, numreservation, id_service, id_utilisateur)
             VALUES (:date_reservation, :statut, :numreservation, :id_service, :id_utilisateur)'
        );
        $requete->execute([
            'date_reservation' => $dateReservation,
            'statut' => $statut,
            'numreservation' => $numreservation,
            'id_service' => $idService,
            'id_utilisateur' => $idUtilisateur
        ]);

        $message = 'Reservation envoyee. Numero : ' . $numreservation;
        $messageClass = 'alert-success';
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageClass = 'alert-error';
    }
}

$service = null;

if (!empty($idService)) {
    $requete = $bdd->prepare(
        'SELECT id_service, nom_service, prix, duree
         FROM service
         WHERE id_service = :id_service AND statut = :statut'
    );
    $requete->execute([
        'id_service' => $idService,
        'statut' => 'activer'
    ]);
    $service = $requete->fetch();
}

$requete = $bdd->prepare(
    'SELECT reservation.id_reservation,
            reservation.date_reservation,
            reservation.statut,
            reservation.numreservation,
            service.nom_service
     FROM reservation
     INNER JOIN service ON reservation.id_service = service.id_service
     WHERE reservation.id_utilisateur = :id_utilisateur
     ORDER BY reservation.id_reservation DESC'
);
$requete->execute([
    'id_utilisateur' => $idUtilisateur
]);
$reservations = $requete->fetchAll();

$pageTitle = 'Reservation - MY LAVAGE';
$pageActive = 'reservation';
require_once __DIR__ . '/../includes/header.php';
?>
    <main class="main-service">
        <section class="auth-box">
            <h1>Reservation</h1>

            <?php if (!empty($message)): ?>
                <div class="alert <?= $messageClass ?>">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($service): ?>
                <p>
                    <strong><?= htmlspecialchars($service['nom_service']) ?></strong>
                    <?= htmlspecialchars($service['prix']) ?> $
                </p>

                <form class="auth-form" method="post">
                    <input type="hidden" name="id_service" value="<?= htmlspecialchars($service['id_service']) ?>">

                    <label for="date_reservation">Date et heure</label>
                    <input type="datetime-local" name="date_reservation" id="date_reservation" required>

                    <button type="submit" name="passer_reservation">Passer la reservation</button>
                </form>
            <?php else: ?>
                <p>Choisis un service avant de passer une reservation.</p>
            <?php endif; ?>
        </section>

        <section class="admin-list">
            <h2>Mes reservations</h2>

            <?php if (empty($reservations)): ?>
                <p>Aucune reservation pour le moment.</p>
            <?php endif; ?>

            <?php foreach ($reservations as $reservation): ?>
                <div class="reservation-item">
                    <p><strong>Numero :</strong> <?= htmlspecialchars($reservation['numreservation']) ?></p>
                    <p><strong>Service :</strong> <?= htmlspecialchars($reservation['nom_service']) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($reservation['date_reservation']) ?></p>
                    <p><strong>Statut :</strong> <?= htmlspecialchars($reservation['statut']) ?></p>

                    <?php
                    $requeteMessage = $bdd->prepare(
                        'SELECT contenu, date_message
                         FROM message
                         WHERE id_reservation = :id_reservation
                         ORDER BY id_message DESC'
                    );
                    $requeteMessage->execute([
                        'id_reservation' => $reservation['id_reservation']
                    ]);
                    $messages = $requeteMessage->fetchAll();
                    ?>

                    <?php foreach ($messages as $msg): ?>
                        <p><strong>Message admin :</strong> <?= htmlspecialchars($msg['contenu']) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
