DROP DATABASE IF EXISTS EF;
CREATE DATABASE EF;
USE EF;

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
    actif BOOLEAN NOT NULL DEFAULT 1,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Pret_EF (
    idPret INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT,
    idTypePret INT,
    idEtablissementFinancier INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    delaiPremierRemboursementMois INT NOT NULL DEFAULT 0,
    dateDemande DATE NOT NULL,
    dateAccord DATE,                
    interets DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    dateRetourEstimee DATE NOT NULL,
    tauxAssurance DECIMAL(5,2) NULL, 
    statut ENUM('en_attente', 'accorde', 'refuse', 'rembourse') DEFAULT 'en_attente',
    FOREIGN KEY (idClient) REFERENCES Client_EF(idClient) ON DELETE CASCADE,
    FOREIGN KEY (idTypePret) REFERENCES TypePret_EF(idTypePret) ON DELETE CASCADE,
    FOREIGN KEY (idEtablissementFinancier) REFERENCES EtablissementFinancier_EF(idEtablissementFinancier)
);

CREATE TABLE Remboursement_EF (
    idRemboursement INT PRIMARY KEY AUTO_INCREMENT,
    idPret INT,
    montantRembourse DECIMAL(15,2) NOT NULL,
    dateRemboursement DATE NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idPret) REFERENCES Pret_EF(idPret) ON DELETE CASCADE
);



CREATE TABLE SimulationPret_EF (
    idSimulation INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT,
    idTypePret INT,
    idEtablissementFinancier INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    delaiPremierRemboursementMois INT NOT NULL DEFAULT 0,
    interets DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    dateSimulation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tauxAssurance DECIMAL(5,2) NULL,
    FOREIGN KEY (idClient) REFERENCES Client_EF(idClient) ON DELETE CASCADE,
    FOREIGN KEY (idTypePret) REFERENCES TypePret_EF(idTypePret) ON DELETE CASCADE,
    FOREIGN KEY (idEtablissementFinancier) REFERENCES EtablissementFinancier_EF(idEtablissementFinancier)
);

INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal) VALUES
('Banque Nationale', 5000000.00);

INSERT INTO AjoutFonds_EF (idEtablissementFinancier, montant, dateAjout) VALUES
(1, 1000000.00, '2023-01-15 10:00:00'),
(1, 500000.00, '2023-02-20 11:30:00'),
(1, 750000.00, '2023-03-10 09:15:00'),
(1, 200000.00, '2023-04-05 14:20:00'),
(1, 1500000.00, '2023-05-12 16:45:00');

INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES
('Prêt personnel', 4.50, 60),
('Prêt immobilier', 2.75, 240),
('Prêt automobile', 3.25, 84),
('Crédit renouvelable', 6.00, 36),
('Prêt étudiant', 1.50, 120);

INSERT INTO Client_EF (nom, prenom, adresse, telephone, email) VALUES
('Dupont', 'Jean', '12 rue de la Paix, Paris', '0612345678', 'jean.dupont@email.com'),
('Martin', 'Sophie', '25 avenue des Champs, Lyon', '0698765432', 'sophie.martin@email.com'),
('Bernard', 'Pierre', '8 boulevard Voltaire, Marseille', '0678912345', 'pierre.bernard@email.com'),
('Petit', 'Marie', '3 rue du Commerce, Lille', '0632145698', 'marie.petit@email.com'),
('Moreau', 'Luc', '15 place de la République, Toulouse', '0687654321', 'luc.moreau@email.com');

INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, delaiPremierRemboursementMois, dateDemande, dateAccord, interets, dateRetourEstimee, tauxAssurance, statut) VALUES
(1, 1, 1, 10000.00, 36, 0, '2023-01-10', '2023-01-12', 450.00, '2026-01-10', 1.20, 'accorde'),
(2, 2, 2, 200000.00, 180, 3, '2023-02-15', '2023-02-20', 49500.00, '2038-02-15', 0.80, 'accorde'),
(3, 3, 3, 25000.00, 60, 0, '2023-03-05', NULL, 0.00, '2028-03-05', 1.50, 'en_attente'),
(4, 1, 4, 15000.00, 48, 0, '2023-04-12', '2023-04-15', 720.00, '2027-04-12', 1.00, 'rembourse'),
(5, 4, 5, 5000.00, 24, 0, '2023-05-20', '2023-05-22', 300.00, '2025-05-20', 2.00, 'refuse');

INSERT INTO Remboursement_EF (idPret, montantRembourse, dateRemboursement) VALUES
(1, 291.67, '2023-02-10'),
(1, 291.67, '2023-03-10'),
(4, 320.00, '2023-05-12'),
(4, 320.00, '2023-06-12'),
(4, 320.00, '2023-07-12');