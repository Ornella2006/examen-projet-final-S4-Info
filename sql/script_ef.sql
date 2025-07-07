drop database EF;
create database EF;
use EF;

CREATE TABLE EtablissementFinancier_EF (
    idEtablissementFinancier INT PRIMARY KEY AUTO_INCREMENT,
    nomEtablissementFinancier VARCHAR(100) NOT NULL,
    fondTotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE AjoutFonds_EF (
    idAjoutFonds INT PRIMARY KEY AUTO_INCREMENT,
    idEtablissementFinancier INT,
    montant DECIMAL(15,2) NOT NULL,
    dateAjout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idEtablissementFinancier) REFERENCES EtablissementFinancier_EF(idEtablissementFinancier),
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE TypePret_EF (
    idTypePret INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    tauxInteret DECIMAL(5,2) NOT NULL, 
    dureeMaxMois INT NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Client_EF (
    idClient INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE Pret_EF (
    idPret INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT,
    idTypePret INT,
    montant DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    dateDemande DATE NOT NULL,
    dateAccord DATE,                    
    statut ENUM('en_attente', 'accorde', 'refuse', 'rembourse') DEFAULT 'en_attente',
    FOREIGN KEY (idClient) REFERENCES Client_EF(idClient) ON DELETE CASCADE,
    FOREIGN KEY (idTypePret) REFERENCES TypePret_EF(idTypePret) ON DELETE CASCADE
);

-- CREATE TABLE Remboursement (
--     id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
--     idPret INT,
--     montant DECIMAL(15,2) NOT NULL,
--     date_remboursement DATE NOT NULL,
--     FOREIGN KEY (idPret) REFERENCES Pret(idPret)
-- );

INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal, dateCreation) VALUES
('Banque Nationale', 1000000.00, '2025-01-01 10:00:00'),
('Crédit Agricole', 500000.50, '2025-02-15 14:30:00'),
('Société Générale', 750000.75, '2025-03-10 09:15:00');

INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois, dateCreation) VALUES
('Prêt immobilier', 3.5, 240, '2025-01-01 12:00:00'),
('Prêt personnel', 5.0, 60, '2025-02-01 12:00:00'),
('Prêt auto', 4.0, 84, '2025-03-01 12:00:00');

INSERT INTO Client_EF (nom, prenom, adresse, telephone, email, dateCreation) VALUES
('Dupont', 'Jean', '12 Rue de Paris, 75001', '0123456789', 'jean.dupont@example.com', '2025-01-15 08:00:00'),
('Martin', 'Sophie', '45 Avenue des Champs, 75008', '0987654321', 'sophie.martin@example.com', '2025-02-20 09:00:00');

INSERT INTO Pret_EF (idClient, idTypePret, montant, dureeMois, dateDemande, dateAccord, statut) VALUES
(1, 1, 200000.00, 180, '2025-03-01', '2025-03-05', 'accorde'),
(2, 2, 10000.00, 36, '2025-04-01', NULL, 'en_attente');