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

CREATE TABLE Remboursement_EF (
    id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
    idPret INT,
    montant DECIMAL(15,2) NOT NULL,
    date_remboursement DATE NOT NULL,
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

INSERT INTO Client_EF (nom, prenom, adresse, telephone, email)
VALUES 
('Rabe', 'Jean', 'Lot 123 Ambohijanaka', '0321234567', 'jean.rabe@mail.com'),
('Randria', 'Sofia', 'Lot 456 Isoraka', '0348765432', 'sofia.randria@mail.com'),
('Rakoto', 'Hery', 'Lot 789 Ankatso', '0331122334', 'hery.rakoto@mail.com');


INSERT INTO Pret_EF (idClient, idTypePret, montant, dureeMois, dateDemande, dateAccord, statut)
VALUES 
(1, 1, 300000.00, 120, '2025-06-01', '2025-06-05', 'accorde'),
(2, 2, 150000.00, 24, '2025-06-10', NULL, 'en_attente'),
(3, 3, 100000.00, 36, '2025-06-15', '2025-06-20', 'refuse');


INSERT INTO Remboursement_EF (idPret, montant, date_remboursement, interet)
VALUES 
(1, 10000.00, '2025-07-01', 1375.00),
(1, 10000.00, '2025-08-01', 1375.00);
