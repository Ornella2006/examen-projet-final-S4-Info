create database ES;
use ES;

CREATE TABLE EtablissementFinancier (
    id_ef INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    fonds_total DECIMAL(15,2) NOT NULL DEFAULT 0.00
);

CREATE TABLE AjoutFonds (
    id_ajout INT PRIMARY KEY AUTO_INCREMENT,
    id_ef INT,
    montant DECIMAL(15,2) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ef) REFERENCES EtablissementFinancier(id_ef)
);

CREATE TABLE TypePret (
    id_type_pret INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    taux_interet DECIMAL(5,2) NOT NULL, 
    duree_max_mois INT NOT NULL         
);

CREATE TABLE Client (
    id_client INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE Pret (
    id_pret INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT,
    id_type_pret INT,
    montant DECIMAL(15,2) NOT NULL,
    duree_mois INT NOT NULL,
    date_demande DATE NOT NULL,
    date_accord DATE,                    
    statut ENUM('en_attente', 'accorde', 'refuse', 'rembourse') DEFAULT 'en_attente',
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_type_pret) REFERENCES TypePret(id_type_pret)
);

CREATE TABLE Remboursement (
    id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
    id_pret INT,
    montant DECIMAL(15,2) NOT NULL,
    date_remboursement DATE NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES Pret(id_pret)
);