create database EF;
use EF;

CREATE TABLE EtablissementFinancier (
    idEtablissementFinancier INT PRIMARY KEY AUTO_INCREMENT,
    nomEtablissementFinancier VARCHAR(100) NOT NULL,
    fondTotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE AjoutFonds (
    idAjoutFonds INT PRIMARY KEY AUTO_INCREMENT,
    idEtablissementFinancier INT,
    montant DECIMAL(15,2) NOT NULL,
    dateAjout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idEtablissementFinancier) REFERENCES EtablissementFinancier(idEtablissementFinancier),
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE TypePret (
    idTypePret INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    tauxInteret DECIMAL(5,2) NOT NULL, 
    dureeMaxMois INT NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Client (
    idClient INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE Pret (
    idPret INT PRIMARY KEY AUTO_INCREMENT,
    idClient INT,
    idTypePret INT,
    montant DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    dateDemande DATE NOT NULL,
    dateAccord DATE,                    
    statut ENUM('en_attente', 'accorde', 'refuse', 'rembourse') DEFAULT 'en_attente',
    FOREIGN KEY (idClient) REFERENCES Client(idClient) ON DELETE CASCADE,
    FOREIGN KEY (idTypePret) REFERENCES TypePret(idTypePret) ON DELETE CASCADE
);

-- CREATE TABLE Remboursement (
--     id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
--     idPret INT,
--     montant DECIMAL(15,2) NOT NULL,
--     date_remboursement DATE NOT NULL,
--     FOREIGN KEY (idPret) REFERENCES Pret(idPret)
-- );