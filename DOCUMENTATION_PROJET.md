# Documentation du projet MY LAVAGE

## 1. Présentation

MY LAVAGE est une application web en PHP pour une entreprise de lavage. Le site permet aux clients de consulter les services, créer un compte, se connecter, faire une réservation et voir la réponse de l'administrateur.

L'administrateur peut gérer les services, accepter ou refuser les réservations et envoyer un message explicatif au client.

## 2. Organisation du projet

```text
projet2/
├── annexe/
│   └── creation_tables.sql
├── assets/
│   ├── css/
│   │   └── style.css
│   └── img/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── admin.php
│   ├── connexion.php
│   ├── contact.php
│   ├── deconnexion.php
│   ├── inscription.php
│   ├── reservation.php
│   └── services.php
├── index.php
└── README.md
```

## 3. Rôle des dossiers

`annexe/` contient le fichier SQL de création de la base de données.

`assets/css/` contient le style du site.

`assets/img/` contient les images du site et les images des services ajoutées par l'admin.

`config/` contient la connexion à la base de données.

`includes/` contient les parties communes du site, comme le header et le footer.

`pages/` contient les pages principales : connexion, inscription, services, réservation, administration.

## 4. Base de données

La base de données s'appelle :

```sql
mylavage
```

Le fichier principal est :

```text
annexe/creation_tables.sql
```

Il crée les tables dans le bon ordre pour éviter les erreurs de clés étrangères.

Les tables sont :

- `roles`
- `utilisateur`
- `service`
- `reservation`
- `message`

## 5. Table roles

Cette table contient les rôles des utilisateurs.

```sql
CREATE TABLE roles (
   id_role BIGINT AUTO_INCREMENT,
   description VARCHAR(50),
   PRIMARY KEY (id_role)
);
```

Les deux rôles du projet sont :

```sql
INSERT INTO roles (id_role, description) VALUES
(1, 'utilisateur simple'),
(2, 'administrateur');
```

Le rôle `1` correspond au client simple.

Le rôle `2` correspond à l'administrateur.

## 6. Table utilisateur

Cette table garde les informations des personnes inscrites.

Champs importants :

- `id_utilisateur`
- `nom`
- `prenom`
- `email`
- `password`
- `id_role`

Le champ `password` contient un mot de passe hashé, pas le mot de passe original.

## 7. Table service

Cette table contient les services de lavage.

Champs importants :

- `id_service`
- `nom_service`
- `description`
- `prix`
- `duree`
- `image_service`
- `statut`

Le champ `statut` permet de savoir si le service est visible ou non.

Statuts possibles :

```text
activer
desactiver
supprimer
```

Un service avec `activer` est visible par le client.

Un service avec `desactiver` ou `supprimer` n'est plus visible par le client.

## 8. Table reservation

Cette table contient les réservations faites par les clients.

Champs importants :

- `id_reservation`
- `date_reservation`
- `statut`
- `numreservation`
- `id_service`
- `id_utilisateur`

Le champ `numreservation` est unique. Il permet d'identifier clairement une réservation.

Statuts possibles :

```text
en attente
accepter
refuser
```

## 9. Table message

Cette table contient les messages envoyés par l'administrateur après acceptation ou refus d'une réservation.

Champs importants :

- `id_message`
- `contenu`
- `date_message`
- `id_reservation`
- `id_role`

Le message est lié à une réservation.

## 10. Connexion à la base de données

Le fichier est :

```text
config/database.php
```

Code important :

```php
$bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$username,$password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
```

La variable `$bdd` est utilisée dans les pages pour faire les requêtes SQL.

## 11. Header et footer

Le header est dans :

```text
includes/header.php
```

Le footer est dans :

```text
includes/footer.php
```

Chaque page les inclut avec `require_once`.

Exemple :

```php
require_once __DIR__ . '/../includes/header.php';
```

`__DIR__` permet d'utiliser le chemin du dossier du fichier actuel.

## 12. Session utilisateur

Le projet utilise les sessions PHP pour savoir si un utilisateur est connecté.

Code important :

```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

Pour vérifier si quelqu'un est connecté :

```php
$isConnected = isset($_SESSION['id_utilisateur']);
```

Les informations importantes stockées dans la session sont :

```php
$_SESSION['id_utilisateur'];
$_SESSION['nom'];
$_SESSION['prenom'];
$_SESSION['email'];
$_SESSION['id_role'];
```

## 13. Menu du site

Dans `includes/header.php`, le menu change selon l'utilisateur.

Si l'utilisateur n'est pas connecté, il voit :

- Accueil
- Services
- Contact
- Connexion
- Inscription

S'il est connecté, il voit :

- son prénom et son nom
- Accueil
- Services
- Contact
- Mes reservations
- Deconnecter

S'il est administrateur, il voit aussi :

- Admin

## 14. Inscription

La page est :

```text
pages/inscription.php
```

Elle permet à un client de créer un compte.

Les champs sont :

- nom
- prénom
- email
- mot de passe
- confirmation du mot de passe

Le formulaire est traité seulement si la méthode est `POST` :

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
```

Les champs texte sont nettoyés :

```php
$nom = trim(strip_tags($_POST['nom']));
$prenom = trim(strip_tags($_POST['prenom']));
$email = trim(strip_tags($_POST['email']));
```

On vérifie que les deux mots de passe correspondent :

```php
if ($password !== $confirmPassword) {
    throw new Exception('Les mots de passe ne correspondent pas.');
}
```

On vérifie si l'adresse email existe déjà :

```php
$requete = $bdd->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
$requete->execute([
    'email' => $email
]);
```

Le mot de passe est hashé :

```php
$motDePasseHash = password_hash($password, PASSWORD_DEFAULT);
```

Puis on ajoute l'utilisateur :

```php
$requete = $bdd->prepare(
    'INSERT INTO utilisateur (nom, prenom, email, password, id_role)
     VALUES (:nom, :prenom, :email, :password, :id_role)'
);
```

Après l'inscription, la session démarre directement :

```php
$_SESSION['id_utilisateur'] = $bdd->lastInsertId();
$_SESSION['nom'] = $nom;
$_SESSION['prenom'] = $prenom;
$_SESSION['email'] = $email;
$_SESSION['id_role'] = $idRoleClient;
```

## 15. Connexion

La page est :

```text
pages/connexion.php
```

Elle récupère l'utilisateur avec son email :

```php
$requete = $bdd->prepare(
    'SELECT id_utilisateur, nom, prenom, email, password, id_role
     FROM utilisateur
     WHERE email = :email'
);
```

Ensuite elle vérifie le mot de passe :

```php
if (!$utilisateur || !password_verify($password, $utilisateur['password'])) {
    throw new Exception('Email ou mot de passe incorrect.');
}
```

Si la connexion réussit, les informations sont mises en session.

## 16. Déconnexion

La page est :

```text
pages/deconnexion.php
```

Elle vide la session :

```php
session_unset();
session_destroy();
```

Puis elle renvoie vers l'accueil.

## 17. Services

La page est :

```text
pages/services.php
```

Elle affiche les services actifs seulement :

```php
$requete = $bdd->prepare(
    'SELECT id_service, nom_service, description, prix, duree, image_service
     FROM service
     WHERE statut = :statut
     ORDER BY id_service DESC'
);
$requete->execute([
    'statut' => 'activer'
]);
```

Le bouton de réservation envoie vers la page de réservation avec l'id du service :

```php
<a class="reservation-btn" href="./reservation.php?id_service=<?= htmlspecialchars($service['id_service']) ?>">Reserver</a>
```

## 18. Réservation côté client

La page est :

```text
pages/reservation.php
```

Elle est réservée aux utilisateurs connectés :

```php
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: ./connexion.php');
    exit;
}
```

Le client choisit une date et une heure :

```html
<input type="datetime-local" name="date_reservation" id="date_reservation" required>
```

Quand la réservation est créée, le statut est :

```php
$statut = 'en attente';
```

Le numéro unique est créé ici :

```php
$numreservation = strtoupper(uniqid('RES-'));
```

Puis on insère la réservation :

```php
$requete = $bdd->prepare(
    'INSERT INTO reservation (date_reservation, statut, numreservation, id_service, id_utilisateur)
     VALUES (:date_reservation, :statut, :numreservation, :id_service, :id_utilisateur)'
);
```

## 19. Suivi des réservations client

Dans `pages/reservation.php`, le client peut voir ses réservations.

Il voit :

- le numéro de réservation
- le service
- la date
- le statut
- le message de l'admin

La requête sélectionne seulement les réservations de l'utilisateur connecté :

```php
WHERE reservation.id_utilisateur = :id_utilisateur
```

## 20. Administration

La page est :

```text
pages/admin.php
```

Elle est protégée :

```php
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['id_role'] != 2) {
    header('Location: ../index.php');
    exit;
}
```

Seuls les administrateurs peuvent accéder à cette page.

## 21. Gestion des services

Dans l'administration, l'admin peut :

- ajouter un service
- modifier un service
- désactiver un service
- supprimer logiquement un service

Le formulaire d'ajout utilise :

```html
enctype="multipart/form-data"
```

C'est obligatoire pour envoyer une image.

## 22. Upload d'image

Les images des services sont envoyées dans :

```text
assets/img/
```

Le champ HTML est :

```html
<input type="file" name="image_service" accept="image/*">
```

La partie PHP importante :

```php
$file = $_FILES['image_service'];
$imageService = basename($file['name']);
$cheminImage = $dossierImage . $imageService;

if (!move_uploaded_file($file['tmp_name'], $cheminImage)) {
    throw new Exception('Erreur pendant l envoi de l image.');
}
```

La base de données garde le nom de l'image dans `image_service`.

## 23. Désactiver ou supprimer un service

Pour désactiver :

```php
$requete = $bdd->prepare('UPDATE service SET statut = :statut WHERE id_service = :id_service');
$requete->execute([
    'statut' => 'desactiver',
    'id_service' => $idService
]);
```

Pour supprimer logiquement :

```php
$requete->execute([
    'statut' => 'supprimer',
    'id_service' => $idService
]);
```

On ne supprime pas forcément la ligne, car une réservation peut déjà être liée à ce service.

## 24. Gestion des réservations admin

Dans `pages/admin.php`, l'admin voit toutes les réservations.

Il voit :

- numéro de réservation
- nom du client
- email
- service
- date
- statut

Si la réservation est `en attente`, l'admin peut accepter ou refuser.

Si la réservation est déjà `accepter` ou `refuser`, il ne peut plus répondre.

## 25. Protection contre une double réponse

Avant d'accepter ou refuser, le code vérifie que la réservation est encore en attente :

```php
$requete = $bdd->prepare('SELECT statut FROM reservation WHERE id_reservation = :id_reservation');
$requete->execute([
    'id_reservation' => $idReservation
]);
$reservation = $requete->fetch();

if (!$reservation || $reservation['statut'] != 'en attente') {
    throw new Exception('Cette reservation a deja ete traitee.');
}
```

Cette logique empêche l'admin de répondre deux fois à la même réservation.

## 26. Acceptation ou refus

Si l'admin accepte, le statut devient :

```text
accepter
```

Si l'admin refuse, le statut devient :

```text
refuser
```

Le changement se fait avec :

```php
$requete = $bdd->prepare('UPDATE reservation SET statut = :statut WHERE id_reservation = :id_reservation');
$requete->execute([
    'statut' => $statutReservation,
    'id_reservation' => $idReservation
]);
```

## 27. Message admin

Après acceptation ou refus, l'admin écrit un message.

Exemple pour une acceptation :

```text
Votre reservation est acceptee. On vous attend a 10h. Numero : RES-...
```

Exemple pour un refus :

```text
Votre reservation est refusee parce que cette date est deja prise. Choisissez demain entre 9h et 11h.
```

Le message est enregistré ici :

```php
$requete = $bdd->prepare(
    'INSERT INTO message (contenu, date_message, id_reservation, id_role)
     VALUES (:contenu, NOW(), :id_reservation, :id_role)'
);
```

## 28. Sécurité utilisée

Le projet utilise :

- `isset()` pour vérifier si une donnée existe.
- `trim()` pour enlever les espaces inutiles.
- `strip_tags()` pour retirer les balises HTML.
- `htmlspecialchars()` pour afficher une donnée sans exécuter du HTML.
- `password_hash()` pour protéger les mots de passe.
- `password_verify()` pour vérifier un mot de passe.
- `prepare()` et `execute()` pour sécuriser les requêtes SQL.

Exemple de requête préparée avec marqueur nominatif :

```php
$requete = $bdd->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
$requete->execute([
    'email' => $email
]);
```

## 29. Parcours client

1. Le client arrive sur le site.
2. Il crée un compte ou se connecte.
3. Il consulte les services.
4. Il clique sur `Reserver`.
5. Il choisit une date et une heure.
6. Le système crée un numéro de réservation.
7. La réservation passe en `en attente`.
8. L'admin traite la réservation.
9. Le client voit le statut et le message dans `Mes reservations`.

## 30. Parcours administrateur

1. L'admin se connecte.
2. Il voit le lien `Admin`.
3. Il ajoute ou modifie les services.
4. Il désactive ou supprime les services si nécessaire.
5. Il voit les réservations.
6. Il accepte ou refuse les réservations en attente.
7. Il écrit un message au client.

## 31. Fichiers importants

```text
config/database.php        Connexion à MySQL
includes/header.php        En-tête et menu
includes/footer.php        Pied de page
pages/inscription.php      Création de compte
pages/connexion.php        Connexion utilisateur
pages/deconnexion.php      Déconnexion
pages/services.php         Liste des services actifs
pages/reservation.php      Réservation client et suivi
pages/admin.php            Administration
assets/css/style.css       CSS du site
assets/img/                Images
annexe/creation_tables.sql Création de la base de données
```

## 32. Résumé de la logique

La logique principale repose sur :

- la session pour savoir qui est connecté
- le rôle pour savoir si l'utilisateur est client ou admin
- le statut du service pour savoir s'il est visible
- le statut de la réservation pour savoir si l'admin peut encore répondre
- le numéro de réservation pour identifier clairement chaque demande

Le client réserve, l'admin répond, et le client consulte la réponse dans son espace.
