CREATE TABLE role_clients (
    id INT PRIMARY KEY,
    nom_role VARCHAR(255)
);

CREATE TABLE status_clients (
    id INT PRIMARY KEY,
    status_role VARCHAR(255)
);

CREATE TABLE clients (
    id INT PRIMARY KEY,
    nom VARCHAR(255),
    username VARCHAR(255),
    email VARCHAR(255),
    telephone VARCHAR(255),
    date_inscription TIMESTAMP,
    role INT,
    statut INT,
    FOREIGN KEY (role) REFERENCES role_clients(id),
    FOREIGN KEY (statut) REFERENCES status_clients(id)
);

CREATE TABLE etablissement_financier (
    id INT PRIMARY KEY,
    solde_actuelle DECIMAL(15,2)
);

CREATE TABLE type_pret (
    id INT PRIMARY KEY,
    nom VARCHAR(255),
    taux_interet_annuel INT,
    duree_max_mois INT
);

CREATE TABLE etat_validation (
    id INT PRIMARY KEY,
    nom_etat_validation VARCHAR(255)
);

CREATE TABLE historique_emprunt (
    montant DECIMAL(15,2)
);

CREATE TABLE pret (
    id INT PRIMARY KEY,
    client INT,
    type_pret_id INT,
    montant_emprunt INT,
    date_debut TIMESTAMP,
    date_fin TIMESTAMP NULL,
    taux_interet DECIMAL(5,2),
    id_etat_validation INT,
    montant_remboursé DECIMAL(15,2),
    date_creation TIMESTAMP NULL,
    FOREIGN KEY (client) REFERENCES clients(id),
    FOREIGN KEY (type_pret_id) REFERENCES type_pret(id),
    FOREIGN KEY (id_etat_validation) REFERENCES etat_validation(id)
);
