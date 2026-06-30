CREATE DATABASE IF NOT EXISTS mylavage;

USE mylavage;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS reservation;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS service;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
   id_role BIGINT AUTO_INCREMENT,
   description VARCHAR(50),
   PRIMARY KEY (id_role)
);

INSERT INTO roles (id_role, description) VALUES
(1, 'utilisateur simple'),
(2, 'administrateur');

CREATE TABLE service (
   id_service INT AUTO_INCREMENT,
   nom_service VARCHAR(50) NOT NULL,
   description VARCHAR(100) NOT NULL,
   prix DECIMAL(15,2) NOT NULL,
   duree INT NOT NULL,
   image_service VARCHAR(255),
   statut VARCHAR(50) NOT NULL DEFAULT 'activer',
   PRIMARY KEY (id_service)
);

INSERT INTO service (nom_service, description, prix, duree, image_service, statut) VALUES
('Lavage Exterieur', 'Lavage carrosserie, jantes, vitres et phares avec produits de qualite.', 30.00, 30, 'netoyage_exterieur.jpg', 'activer'),
('Lavage Interieur', 'Aspiration, plastiques, vitres interieures et tableau de bord.', 30.00, 45, 'interieur.jpg', 'activer'),
('Lavage Moteur', 'Lavage moteur, degraissage et nettoyage des elements accessibles.', 50.00, 45, 'moteur3.jpg', 'activer');

CREATE TABLE utilisateur (
   id_utilisateur BIGINT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50),
   email VARCHAR(50) NOT NULL,
   password VARCHAR(255),
   id_role BIGINT NOT NULL,
   PRIMARY KEY (id_utilisateur),
   UNIQUE (email),
   FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

CREATE TABLE reservation (
   id_reservation BIGINT AUTO_INCREMENT,
   date_reservation DATETIME NOT NULL,
   statut VARCHAR(50) NOT NULL,
   numreservation VARCHAR(50) NOT NULL,
   id_service INT NOT NULL,
   id_utilisateur BIGINT NOT NULL,
   PRIMARY KEY (id_reservation),
   UNIQUE (numreservation),
   FOREIGN KEY (id_service) REFERENCES service(id_service),
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE message (
   id_message BIGINT AUTO_INCREMENT,
   contenu TEXT NOT NULL,
   date_message DATETIME,
   id_reservation BIGINT NOT NULL,
   id_role BIGINT NOT NULL,
   PRIMARY KEY (id_message),
   FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation),
   FOREIGN KEY (id_role) REFERENCES roles(id_role)
);
