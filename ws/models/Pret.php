<?php
require_once __DIR__ . '/../db.php';

class Pret {
   public static function create($data) {
    $db = getDB();
    
    // Vérifier si le client existe et est actif
    $stmt = $db->prepare("SELECT actif FROM Client_EF WHERE idClient = ?");
    $stmt->execute([$data->idClient]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$client) {
        throw new Exception("Client inexistant.");
    }
    if (!$client['actif']) {
        throw new Exception("Client sanctionné ou inactif.");
    }

    // Vérifier si le type de prêt existe
    $stmt = $db->prepare("SELECT tauxInteret, dureeMaxMois FROM TypePret_EF WHERE idTypePret = ?");
    $stmt->execute([$data->idTypePret]);
    $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$typePret) {
        throw new Exception("Type de prêt invalide.");
    }

    // Vérifier la durée
    if ($data->dureeMois > $typePret['dureeMaxMois']) {
        throw new Exception("La durée dépasse la durée maximale autorisée.");
    }

    // Vérifier le solde de l'établissement
    $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
    $stmt->execute([$data->idEtablissementFinancier]);
    $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$etablissement) {
        throw new Exception("Établissement financier inexistant.");
    }
    if ($data->montant > $etablissement['fondTotal']) {
        throw new Exception("Montant supérieur au solde disponible.");
    }

    // Calculer les intérêts
    $interets = $data->montant * ($typePret['tauxInteret'] / 100);

    // Calculer la date de retour estimée
    $dateRetourEstimee = date('Y-m-d', strtotime($data->dateDemande . ' + ' . $data->dureeMois . ' months'));

    // Ajouter tauxAssurance à l'insertion
    try {
        $stmt = $db->prepare("
            INSERT INTO Pret_EF (
                idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, 
                dateDemande, interets, dateRetourEstimee, statut, tauxAssurance
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', ?)
        ");
        $stmt->execute([
            $data->idClient,
            $data->idTypePret,
            $data->idEtablissementFinancier,
            $data->montant,
            $data->dureeMois,
            $data->dateDemande,
            $interets,
            $dateRetourEstimee,
            $data->tauxAssurance // Ajout de la valeur
        ]);
        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur SQL dans create: " . $e->getMessage());
        throw new Exception("Erreur lors de la création du prêt : " . $e->getMessage());
    }
}

    public static function valider($id) {
        $db = getDB();
        
        // Récupérer les informations du prêt
        $stmt = $db->prepare("
            SELECT p.montant, p.idEtablissementFinancier, p.statut 
            FROM Pret_EF p 
            WHERE p.idPret = ?
        ");
        $stmt->execute([$id]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pret) {
            throw new Exception("Prêt non trouvé.");
        }
        if ($pret['statut'] !== 'en_attente') {
            throw new Exception("Le prêt n'est pas en attente de validation.");
        }

        // Vérifier le solde de l'établissement
        $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$pret['idEtablissementFinancier']]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($pret['montant'] > $etablissement['fondTotal']) {
            throw new Exception("Solde insuffisant pour valider le prêt.");
        }

        try {
            $db->beginTransaction();

            // Mettre à jour le prêt
            $stmt = $db->prepare("
                UPDATE Pret_EF 
                SET statut = 'accorde', dateAccord = CURDATE() 
                WHERE idPret = ?
            ");
            $stmt->execute([$id]);

            // Mettre à jour le solde de l'établissement
            $stmt = $db->prepare("
                UPDATE EtablissementFinancier_EF 
                SET fondTotal = fondTotal - ? 
                WHERE idEtablissementFinancier = ?
            ");
            $stmt->execute([$pret['montant'], $pret['idEtablissementFinancier']]);

            $db->commit();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erreur SQL dans valider: " . $e->getMessage());
            throw new Exception("Erreur lors de la validation du prêt.");
        }
    }

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*, t.libelle, c.nom, c.prenom 
            FROM Pret_EF p 
            JOIN TypePret_EF t ON p.idTypePret = t.idTypePret 
            JOIN Client_EF c ON p.idClient = c.idClient
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}