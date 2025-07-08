CREATE DATABASE pret_bancaire CHARACTER SET utf8mb4;

USE pret_bancaire;

CREATE TABLE IF NOT EXISTS employes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

INSERT INTO employes (pseudo, mot_de_passe) VALUES 
('admin', 'admin123');