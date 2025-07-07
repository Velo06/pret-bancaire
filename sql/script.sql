CREATE DATABASE tp_flight CHARACTER SET utf8mb4;

USE tp_flight;

CREATE TABLE etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    age INT
);

CREATE DATABASE etablissement_financier;
USE etablissement_financier;

CREATE TABLE type_pret(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    taux_interet_annuel DECIMAL(5,2) NOT NULL,
    duree_max_mois INT NOT NULL,
    montant_max_emprunt DECIMAL(15,2) NOT NULL
);