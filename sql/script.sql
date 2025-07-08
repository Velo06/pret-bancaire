CREATE DATABASE etablissement_financier;
USE etablissement_financier;

CREATE TABLE employes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(100),
    mot_de_passe VARCHAR(25)
);
INSERT INTO employes(pseudo, mot_de_passe) VALUES('admin','admin123');
 
CREATE TABLE role_clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_role VARCHAR(255)
);

CREATE TABLE status_clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_role VARCHAR(255)
);

CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    id INT PRIMARY KEY AUTO_INCREMENT,
    solde_actuelle DECIMAL(15,2)
);
ALTER TABLE etablissement_financier ADD nom VARCHAR(100);

CREATE TABLE type_pret (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) ,
    taux_interet_annuel DECIMAL(5,2),
    duree_max_mois INT,
    montant_max_pres DECIMAL(15,2)
);

CREATE TABLE etat_validation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_etat_validation VARCHAR(255)
);

CREATE TABLE historique_emprunt (
    montant DECIMAL(15,2)
);

CREATE TABLE pret (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
ALTER TABLE pret ADD date_validation TIMESTAMP;
ALTER TABLE pret MODIFY montant_emprunt DECIMAL(15,2);
ALTER TABLE pret ADD COLUMN taux_assurance_annuel DECIMAL(5,2) DEFAULT 0;
ALTER TABLE pret ADD COLUMN delai_premier_remboursement_mois INT DEFAULT 0;

ALTER TABLE `pret` ADD `is_pret_simuler` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_creation`;

CREATE TABLE historique_remboursement (
  id INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
  pret_id INT NOT NULL,
  montant_rembourse DECIMAL(15,2) NOT NULL,
  date_remboursement DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_remboursement_pret FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE
);
ALTER TABLE historique_remboursement ADD etat_remboursement BOOLEAN DEFAULT FALSE;

ALTER TABLE `clients` ADD `revenu` DECIMAL(30,3) NOT NULL AFTER `statut`;

CREATE TABLE table_pret_comparatif (
  id INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
  pret_id INT NOT NULL,
  CONSTRAINT fk_pret FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE
);

CREATE TABLE pret_a_comparer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client INT NOT NULL,
    type_pret_id INT NOT NULL,
    montant_emprunt INT NOT NULL,
    date_debut TIMESTAMP NOT NULL,
    date_fin TIMESTAMP NULL,
    id_etat_validation INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    mensualite DECIMAL(15,2) NOT NULL,
    assurance_mensuelle DECIMAL(15,2) NOT NULL,
    total_interets DECIMAL(15,2) NOT NULL,
    total_assurances DECIMAL(15,2) NOT NULL,
    montant_total_rembourse DECIMAL(15,2) NOT NULL,

    FOREIGN KEY (client) REFERENCES clients(id),
    FOREIGN KEY (type_pret_id) REFERENCES type_pret(id),
    FOREIGN KEY (id_etat_validation) REFERENCES etat_validation(id)
);


INSERT INTO type_pret (nom, taux_interet_annuel, duree_max_mois, montant_max_pres) VALUES
('Prêt Personnel', 0.0500, 60, 10000000),
('Prêt Immobilier', 0.0350, 240, 100000000),
('Prêt Étudiant', 0.0200, 48, 5000000),
('Micro-crédit', 0.0800, 12, 1000000),
('Prêt Automobile', 0.0450, 72, 30000000);

-- Table role_clients
INSERT INTO role_clients (id, nom_role) VALUES
(1, 'Client Standard'),
(2, 'Client Premium');

-- Table status_clients
INSERT INTO status_clients (id, status_role) VALUES
(1, 'Actif'),
(2, 'Inactif'),
(3, 'Suspendu');

INSERT INTO clients (id, nom, username, email, telephone, date_inscription, role, statut,revenu) VALUES
(1, 'Rakoto Jean', 'rakotoj', 'jean.rakoto@example.com', '0321234567', NOW(), 1, 1,1000000),
(2, 'Rasoanaivo Lea', 'lea.rasoa', 'lea.r@example.com', '0349876543', NOW(), 2, 1,1200000),
(3, 'Andrianina Marc', 'marc.andry', 'marc.a@example.com', '0331239876', NOW(), 1, 2,800000),
(4, 'Rabe Alice', 'alice.rabe', 'alice.r@example.com', '0321122334', NOW(), 2, 1,2000000),
(5, 'Randrianarivo Kevin', 'kevin.randria', 'kevin.r@example.com', '0344455667', NOW(), 1, 3,600000);

INSERT INTO etat_validation (id, nom_etat_validation) VALUES
(1, 'En attente'),
(2, 'Validé'),
(3, 'Rejeté');

INSERT INTO etat_validation (id, nom_etat_validation) VALUES
(4, 'Remboursé partiellement'),
(5, 'Remboursé totalement');

INSERT INTO etablissement_financier (solde_actuelle,nom) VALUES
(0, 'E-Banque');