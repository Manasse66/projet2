CREATE TABLE service (
   id_service INT AUTO_INCREMENT,
   nom_service VARCHAR(50) NOT NULL,
   description VARCHAR(100) NOT NULL,
   prix DECIMAL(15,2) NOT NULL,
   duree INT NOT NULL,
   image_service VARCHAR(255),
   PRIMARY KEY (id_service)
);

CREATE TABLE role (
   role BIGINT AUTO_INCREMENT,
   description VARCHAR(50),
   PRIMARY KEY (role)
);

CREATE TABLE utilisateur (
   id_utilisateur BIGINT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50),
   email VARCHAR(50) NOT NULL,
   password VARCHAR(50),
   role BIGINT NOT NULL,
   PRIMARY KEY (id_utilisateur),
   UNIQUE (email),
   FOREIGN KEY (role) REFERENCES role(role)
);

CREATE TABLE reservation (
   id_reservation BIGINT AUTO_INCREMENT,
   date_reservation DATETIME NOT NULL,
   statut VARCHAR(50) NOT NULL,
   id_service INT NOT NULL,
   id_utilisateur BIGINT NOT NULL,
   PRIMARY KEY (id_reservation),
   FOREIGN KEY (id_service) REFERENCES service(id_service),
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE message (
   id_message BIGINT AUTO_INCREMENT,
   contenu TEXT NOT NULL,
   date_message DATETIME,
   id_reservation BIGINT NOT NULL,
   role BIGINT NOT NULL,
   PRIMARY KEY (id_message),
   FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation),
   FOREIGN KEY (role) REFERENCES role(role)
);
