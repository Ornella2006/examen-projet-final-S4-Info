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

