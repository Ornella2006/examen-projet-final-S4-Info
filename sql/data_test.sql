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