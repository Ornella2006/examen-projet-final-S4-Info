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