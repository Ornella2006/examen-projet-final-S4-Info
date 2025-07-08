INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal)
VALUES 
    ('Banque Nationale', 1000000.00),
    ('Caisse d\'Épargne', 500000.00),
    ('Crédit Agricole', 750000.00);


    -- Insérer des données de test dans TypePret_EF
INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES
('Prêt personnel', 5.50, 60),
('Prêt immobilier', 3.20, 240),
('Prêt auto', 4.00, 84);

INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal) VALUES ('Banque EF', 1000000.00);
INSERT INTO Client_EF (nom, prenom, email, actif) VALUES 
    ('Dupont', 'Jean', 'jean.dupont@example.com', 1),
    ('Martin', 'Sophie', 'sophie.martin@example.com', 1),
    ('Sanctionné', 'Paul', 'paul.sanctionne@example.com', 0);



USE EF;

-- Vider les tables pour éviter les doublons
TRUNCATE TABLE Remboursement_EF;
TRUNCATE TABLE Pret_EF;
TRUNCATE TABLE Client_EF;
TRUNCATE TABLE TypePret_EF;
TRUNCATE TABLE AjoutFonds_EF;
TRUNCATE TABLE EtablissementFinancier_EF;

-- Insérer des clients
INSERT INTO Client_EF (nom, prenom, email, telephone, adresse, actif) VALUES
('Dupont', 'Jean', 'jean.dupont@example.com', '0123456789', '123 Rue Principale, Paris', 1),
('Martin', 'Marie', 'marie.martin@example.com', '0987654321', '456 Avenue des Champs, Lyon', 1),
('Durand', 'Pierre', 'pierre.durand@example.com', '0147258369', '789 Boulevard Central, Marseille', 0), -- Client inactif
('Lefevre', 'Sophie', 'sophie.lefevre@example.com', '0678901234', '101 Rue du Nord, Lille', 1);

-- Insérer des types de prêts
INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES
('Prêt personnel', 5.00, 60),
('Prêt immobilier', 3.50, 240),
('Prêt auto', 4.00, 48);

-- Insérer des établissements financiers
INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal, dateCreation) VALUES
('Banque Nationale', 2000000.00, '2025-01-01 10:00:00'),
('Crédit Agricole', 1000000.00, '2025-02-01 14:00:00'),
('Société Générale', 1500000.00, '2025-03-01 09:00:00');

-- Insérer des ajouts de fonds
INSERT INTO AjoutFonds_EF (idEtablissementFinancier, montant, dateAjout) VALUES
(1, 1000000.00, '2025-01-01 10:00:00'),
(1, 1000000.00, '2025-01-15 12:00:00'),
(2, 500000.00, '2025-02-01 14:00:00'),
(3, 1500000.00, '2025-03-01 09:00:00');

-- Insérer des prêts
INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, delaiPremierRemboursementMois, dateDemande, dateAccord, interets, dateRetourEstimee, statut, tauxAssurance) VALUES
(1, 1, 1, 10000.00, 12, 3, '2025-06-01', '2025-06-05', 512.96, '2026-09-01', 'accorde', 0.50), -- Prêt personnel, partiellement remboursé
(1, 2, 1, 50000.00, 60, 6, '2025-06-15', NULL, 4582.40, '2030-12-15', 'en_attente', 0.75), -- Prêt immobilier, en attente
(2, 3, 2, 15000.00, 24, 0, '2025-05-01', '2025-05-10', 1248.96, '2027-05-01', 'accorde', 0.50), -- Prêt auto, partiellement remboursé
(4, 1, 3, 5000.00, 6, 2, '2025-04-01', '2025-04-05', 125.48, '2025-12-01', 'rembourse', 0.50); -- Prêt personnel, entièrement remboursé

-- Insérer des remboursements
INSERT INTO Remboursement_EF (idPret, montantRembourse, dateRemboursement) VALUES
(1, 854.41, '2025-09-01'), -- Remboursement pour prêt 1
(1, 854.41, '2025-10-01'), -- Deuxième remboursement pour prêt 1
(3, 677.04, '2025-06-01'), -- Remboursement pour prêt 3
(3, 677.04, '2025-07-01'), -- Deuxième remboursement pour prêt 3
(4, 854.24, '2025-06-01'), -- Remboursement pour prêt 4
(4, 854.24, '2025-07-01'); -- Deuxième remboursement pour prêt 4

