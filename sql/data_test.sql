-- Insérer des établissements financiers
INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal)
VALUES 
    ('Banque Nationale', 1000000.00),
    ('Caisse d\'Épargne', 500000.00),
    ('Crédit Agricole', 750000.00);

-- Insérer des types de prêts
INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) 
VALUES
    ('Prêt personnel', 5.50, 60),
    ('Prêt immobilier', 3.20, 240),
    ('Prêt auto', 4.00, 84);

-- Insérer des clients
INSERT INTO Client_EF (nom, prenom, email, actif) 
VALUES 
    ('Dupont', 'Jean', 'jean.dupont@example.com', 1),
    ('Martin', 'Sophie', 'sophie.martin@example.com', 1),
    ('Sanctionné', 'Paul', 'paul.sanctionne@example.com', 0);

-- Insérer des prêts
INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, delaiPremierRemboursementMois, dateDemande, dateAccord, interets, dateRetourEstimee, statut) 
VALUES
    (1, 1, 1, 10000.00, 12, 1, '2025-01-01', '2025-01-02', 550.00, '2026-01-01', 'accorde'), -- Intérêts = 10000 * 5.5% = 550
    (2, 2, 1, 200000.00, 240, 1, '2025-02-01', '2025-02-02', 6400.00, '2045-02-01', 'accorde'); -- Intérêts = 200000 * 3.2% * 20 ans = 6400

-- Insérer des remboursements
INSERT INTO Remboursement_EF (idPret, montantRembourse, dateRemboursement) 
VALUES
    (1, 879.17, '2025-02-01'), -- Annuité = 10000 * (0.004583 * (1 + 0.004583)^12) / ((1 + 0.004583)^12 - 1) ≈ 879.17
    (1, 879.17, '2025-03-01'),
    (2, 1055.56, '2025-03-01'), -- Annuité = 200000 * (0.002667 * (1 + 0.002667)^240) / ((1 + 0.002667)^240 - 1) ≈ 1055.56
    (2, 1055.56, '2025-04-01');