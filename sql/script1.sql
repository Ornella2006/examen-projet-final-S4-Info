CREATE DATABASE finance CHARACTER SET utf8mb4;
USE finance;

-- Table pour l'établissement financier (fonds disponibles)
CREATE TABLE financial_institution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00, -- Solde de l'établissement
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les types de prêts (ex. prêt immobilier, prêt auto)
CREATE TABLE loan_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Nom du type de prêt (ex. "Prêt immobilier")
    interest_rate DECIMAL(5, 2) NOT NULL, -- Taux d'intérêt (ex. 5.50 pour 5,5%)
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les clients
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les prêts
CREATE TABLE loan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    loan_type_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL, -- Montant du prêt
    status ENUM('pending', 'approved', 'rejected', 'repaid') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (loan_type_id) REFERENCES loan_type(id) ON DELETE CASCADE
);