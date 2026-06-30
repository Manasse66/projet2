<?php
$pageTitle = 'Services - MY LAVAGE';
$pageActive = 'services';
require_once __DIR__ . '/../config/database.php';

$requete = $bdd->prepare(
    'SELECT id_service, nom_service, description, prix, duree, image_service
     FROM service
     WHERE statut = :statut
     ORDER BY id_service DESC'
);
$requete->execute([
    'statut' => 'activer'
]);
$services = $requete->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
    <main class="main-service">
        <h1 class="main-service-title">Nos Services</h1>

        <?php if (empty($services)): ?>
            <p>Aucun service disponible pour le moment.</p>
        <?php endif; ?>

        <?php foreach ($services as $service): ?>
            <article>
                <div class="carte">
                    <div class="display-service">
                        <?php if (!empty($service['image_service'])): ?>
                            <img src="../assets/img/<?= htmlspecialchars($service['image_service']) ?>" class="carte-img" alt="">
                        <?php endif; ?>

                        <div class="carte-body">
                            <h2><?= htmlspecialchars($service['nom_service']) ?></h2>
                            <p>
                                <strong>Description :</strong>
                                <?= htmlspecialchars($service['description']) ?>
                            </p>
                            <p>
                                <strong>Duree :</strong>
                                <?= htmlspecialchars($service['duree']) ?> minutes
                            </p>
                            <p>
                                <strong>Tarif : <?= htmlspecialchars($service['prix']) ?> $</strong>
                                <a class="reservation-btn" href="./reservation.php?id_service=<?= htmlspecialchars($service['id_service']) ?>">Reserver</a>
                            </p>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
