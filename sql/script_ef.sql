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
    actif TINYINT(1) DEFAULT 1,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Pret_EF (
    idPret INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT,
    idTypePret INT,
    idEtablissementFinancier INT,
    montant DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    dateDemande DATE NOT NULL,
    dateAccord DATE,
    statut ENUM('en_attente', 'accorde', 'refuse', 'rembourse') DEFAULT 'en_attente',
    interets DECIMAL(15,2) NOT NULL,
    dateRetourEstimee DATE,
    tauxAssurance DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (idClient) REFERENCES Client_EF(idClient) ON DELETE CASCADE,
    FOREIGN KEY (idTypePret) REFERENCES TypePret_EF(idTypePret) ON DELETE CASCADE,
    FOREIGN KEY (idEtablissementFinancier) REFERENCES EtablissementFinancier_EF(idEtablissementFinancier)
);

CREATE TABLE Remboursement_EF (
    id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
    idPret INT,
    montant DECIMAL(15,2) NOT NULL,
    date_remboursement DATE NOT NULL,
    interet DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (idPret) REFERENCES Pret_EF(idPret)
);

INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal)
VALUES 
('Banque BOA', 5000000.00);

INSERT INTO AjoutFonds_EF (idEtablissementFinancier, montant)
VALUES 
(1, 1000000.00),
(1, 500000.00),
(1, 300000.00);

INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois)
VALUES 
('Crédit Immobilier', 5.50, 240),
('Crédit Consommation', 7.25, 60),
('Crédit Étudiant', 3.75, 84);

INSERT INTO Client_EF (nom, prenom, adresse, telephone, email, actif)
VALUES 
('Rabe', 'Jean', 'Lot 123 Ambohijanaka', '0321234567', 'jean.rabe@mail.com', 1),
('Randria', 'Sofia', 'Lot 456 Isoraka', '0348765432', 'sofia.randria@mail.com', 1),
('Rakoto', 'Hery', 'Lot 789 Ankatso', '0331122334', 'hery.rakoto@mail.com', 1);

-- Calcul de l'annuité pour le prêt 1 (300000.00, 5.5%, 120 mois)
-- Taux mensuel = 5.5 / 100 / 12 = 0.00458333
-- Annuité = 300000 * (0.00458333 * (1 + 0.00458333)^120) / ((1 + 0.00458333)^120 - 1) ≈ 3952.76
-- Intérêts première échéance = 300000 * 0.00458333 ≈ 1375.00
-- Capital remboursé = 3952.76 - 1375.00 ≈ 2577.76
INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, dateDemande, dateAccord, statut, interets, dateRetourEstimee, tauxAssurance)
VALUES 
(1, 1, 1, 300000.00, 120, '2025-06-01', '2025-06-05', 'accorde', 174331.20, '2035-06-01', 0.00),
(2, 2, 1, 150000.00, 24, '2025-06-10', NULL, 'en_attente', 0.00, '2027-06-10', 0.00),
(3, 3, 1, 100000.00, 36, '2025-06-15', '2025-06-20', 'refuse', 0.00, '2028-06-15', 0.00);

INSERT INTO Remboursement_EF (idPret, montant, date_remboursement, interet)
VALUES 
(1, 2577.76, '2025-07-01', 1375.00),
(1, 2589.57, '2025-08-01', 1363.19);