<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function create($data) {
        $db = getDB();
        
        $stmt = $db->prepare("SELECT actif FROM Client_EF WHERE idClient = ?");
        $stmt->execute([$data->idClient]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$client) {
            throw new Exception("Client inexistant.");
        }
        if (!$client['actif']) {
            throw new Exception("Client sanctionné ou inactif.");
        }

        $stmt = $db->prepare("SELECT tauxInteret, dureeMaxMois FROM TypePret_EF WHERE idTypePret = ?");
        $stmt->execute([$data->idTypePret]);
        $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$typePret) {
            throw new Exception("Type de prêt invalide.");
        }

        if ($data->dureeMois > $typePret['dureeMaxMois']) {
            throw new Exception("La durée dépasse la durée maximale autorisée.");
        }

        $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$data->idEtablissementFinancier]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$etablissement) {
            throw new Exception("Établissement financier inexistant.");
        }
        if ($data->montant > $etablissement['fondTotal']) {
            throw new Exception("Montant supérieur au solde disponible.");
        }

        $interets = $data->montant * ($typePret['tauxInteret'] / 100);

        $dateRetourEstimee = date('Y-m-d', strtotime($data->dateDemande . ' + ' . $data->dureeMois . ' months'));

        try {
            $stmt = $db->prepare("
                INSERT INTO Pret_EF (
                    idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, 
                    dateDemande, interets, dateRetourEstimee, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([
                $data->idClient,
                $data->idTypePret,
                $data->idEtablissementFinancier,
                $data->montant,
                $data->dureeMois,
                $data->dateDemande,
                $interets,
                $dateRetourEstimee
            ]);
            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur SQL dans create: " . $e->getMessage());
            throw new Exception("Erreur lors de la création du prêt.");
        }
    }

    public static function valider($id) {
        $db = getDB();
        
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

        $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$pret['idEtablissementFinancier']]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($pret['montant'] > $etablissement['fondTotal']) {
            throw new Exception("Solde insuffisant pour valider le prêt.");
        }

        try {
            $db->beginTransaction();


            $stmt = $db->prepare("
                UPDATE Pret_EF 
                SET statut = 'accorde', dateAccord = CURDATE() 
                WHERE idPret = ?
            ");
            $stmt->execute([$id]);

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
            SELECT 
                p.idPret,
                p.idClient,
                p.idTypePret,
                p.idEtablissementFinancier,
                p.montant,
                p.dureeMois,
                p.dateDemande,
                p.dateRetourEstimee,
                p.interets,
                p.statut,
                t.libelle,
                t.tauxInteret,
                c.nom,
                c.prenom
            FROM 
                Pret_EF p
            JOIN 
                TypePret_EF t ON p.idTypePret = t.idTypePret
            JOIN 
                Client_EF c ON p.idClient = c.idClient
        ");
        $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prets as &$pret) {
            $tauxMensuel = $pret['tauxInteret'] / 100 / 12;
            $n = $pret['dureeMois'];
            $montant = $pret['montant'];

            if ($tauxMensuel > 0) {
                $annuiteMensuelle = $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $n)) / (pow(1 + $tauxMensuel, $n) - 1);
            } else {
                $annuiteMensuelle = $montant / $n;
            }
            $pret['annuiteMensuelle'] = round($annuiteMensuelle, 2);
            $pret['tauxInteretAnnuel'] = $pret['tauxInteret'];
            $pret['sommeTotaleRembourser'] = round($annuiteMensuelle * $n, 2);

            unset($pret['tauxInteret']);
        }

        return $prets;
    }
}