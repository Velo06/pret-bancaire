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
    nom VARCHAR(255) ,
    taux_interet_annuel DECIMAL(5,2),
    duree_max_mois INT,
    montant_max_pres DECIMAL(15,2)
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
    id_etat_validation INT,
    date_creation TIMESTAMP NULL,
    FOREIGN KEY (client) REFERENCES clients(id),
    FOREIGN KEY (type_pret_id) REFERENCES type_pret(id),
    FOREIGN KEY (id_etat_validation) REFERENCES etat_validation(id)
);

ALTER TABLE `pret` ADD `is_pret_simuler` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_creation`;

CREATE TABLE historique_remboursement (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pret_id INT NOT NULL,
  montant_rembourse DECIMAL(15,2) NOT NULL,
  date_remboursement DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_remboursement_pret FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE
);

ALTER TABLE `clients` ADD `revenu` DECIMAL(30,3) NOT NULL AFTER `statut`;

INSERT INTO type_pret (nom, taux_interet_annuel, duree_max_mois, montant_max_pres) VALUES
('Prêt Personnel', 0.0500, 60, 10000000),
('Prêt Immobilier', 0.0350, 240, 100000000),
('Prêt Étudiant', 0.0200, 48, 5000000),
('Micro-crédit', 0.0800, 12, 1000000),
('Prêt Automobile', 0.0450, 72, 30000000);

-- Table role_clients
INSERT INTO role_clients (id, nom) VALUES
(1, 'Client Standard'),
(2, 'Client Premium');

-- Table status_clients
INSERT INTO status_clients (id, libelle) VALUES
(1, 'Actif'),
(2, 'Inactif'),
(3, 'Suspendu');

INSERT INTO clients (id, nom, username, email, telephone, date_inscription, role, statut) VALUES
(1, 'Rakoto Jean', 'rakotoj', 'jean.rakoto@example.com', '0321234567', NOW(), 1, 1),
(2, 'Rasoanaivo Lea', 'lea.rasoa', 'lea.r@example.com', '0349876543', NOW(), 2, 1),
(3, 'Andrianina Marc', 'marc.andry', 'marc.a@example.com', '0331239876', NOW(), 1, 2),
(4, 'Rabe Alice', 'alice.rabe', 'alice.r@example.com', '0321122334', NOW(), 2, 1),
(5, 'Randrianarivo Kevin', 'kevin.randria', 'kevin.r@example.com', '0344455667', NOW(), 1, 3);

INSERT INTO etat_validation (id, nom_etat_validation) VALUES
(1, 'En attente'),
(2, 'Validé'),
(3, 'Rejeté');

INSERT INTO etat_validation (id, nom_etat_validation) VALUES
(4, 'Remboursé partiellement'),
(5, 'Remboursé totalement');
