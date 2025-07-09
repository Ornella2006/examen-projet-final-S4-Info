
INSERT INTO EtablissementFinancier_EF (nomEtablissementFinancier, fondTotal) VALUES
('Banque Nationale', 1500000.00),
('Crédit Populaire', 800000.00);


INSERT INTO AjoutFonds_EF (idEtablissementFinancier, montant, dateAjout) VALUES
(1, 500000.00, '2025-01-15 09:00:00'),
(1, 300000.00, '2025-06-01 14:30:00'),
(2, 200000.00, '2025-03-10 10:15:00');


INSERT INTO TypePret_EF (libelle, tauxInteret, dureeMaxMois) VALUES
('Prêt personnel', 5.50, 120),
('Prêt auto', 4.00, 60),
('Prêt immobilier', 3.20, 240),
('Prêt étudiant', 2.50, 36);

INSERT INTO Client_EF (nom, prenom, adresse, telephone, email) VALUES
('Dupont', 'Jean', '12 Rue de la Paix, Paris', '0123456789', 'jean.dupont@email.com'),
('Martin', 'Sophie', '45 Avenue des Champs, Lyon', '0987654321', 'sophie.martin@email.com'),
('Lefèvre', 'Pierre', '78 Boulevard Saint-Michel, Marseille', '0632547896', 'pierre.lefevre@email.com'),
('Garcia', 'Marie', '9 Rue Victor Hugo, Toulouse', '0612345678', 'marie.garcia@email.com');

INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, delaiPremierRemboursementMois, dateDemande, dateAccord, interets, dateRetourEstimee, tauxAssurance, statut) VALUES
(1, 1, 1, 50000.00, 24, 0, '2025-06-01', NULL, 0.00, '2027-06-01', 0.50, 'en_attente'),
(1, 1, 1, 100000.00, 36, 2, '2025-05-15', '2025-06-10', 16500.00, '2028-05-15', 0.75, 'accorde'),
(2, 3, 2, 200000.00, 120, 0, '2025-04-20', '2025-05-01', 38400.00, '2035-04-20', 0.40, 'accorde'),
(3, 2, 1, 75000.00, 48, 1, '2025-03-10', '2025-03-15', 12000.00, '2029-03-10', 0.30, 'rembourse'),
(4, 4, 2, 30000.00, 24, 0, '2025-07-01', NULL, 0.00, '2027-07-01', 0.20, 'en_attente');

INSERT INTO Remboursement_EF (idPret, montantRembourse, dateRemboursement) VALUES
(4, 2000.00, '2025-04-01'),
(4, 2000.00, '2025-05-01'),
(4, 2000.00, '2025-06-01'),
(4, 55000.00, '2025-07-01'); -- Montant final pour clôturer le prêt

INSERT INTO SimulationPret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, delaiPremierRemboursementMois, interets, tauxAssurance) VALUES
(1, 1, 1, 500000.00, 120, 0, 65000.00, 0.50), -- Simulation 1
(1, 2, 1, 200000.00, 24, 0, 24000.00, 1.00),  -- Simulation 2
(2, 3, 2, 1000000.00, 180, 0, 135000.00, 0.75), -- Simulation 3
(3, 4, 1, 150000.00, 36, 0, 9000.00, 0.00);   -- Simulation 4

INSERT INTO Admin (nom, motDePasse) VALUES ('admin', 'admin123');