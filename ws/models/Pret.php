<?php
require_once __DIR__ . '/../db.php';

class Pret {
    public static function create($data) {
        $db = getDB();
        
        // Log des données reçues
        error_log("Données reçues dans Pret::create: " . print_r($data, true));

        // Vérifier si le client existe et est actif
        error_log("Vérification du client idClient={$data->idClient}");
        $stmt = $db->prepare("SELECT actif FROM Client_EF WHERE idClient = ?");
        $stmt->execute([$data->idClient]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$client) {
            error_log("Erreur: Client inexistant pour idClient={$data->idClient}");
            throw new Exception("Client inexistant.");
        }
        if (!$client['actif']) {
            error_log("Erreur: Client inactif pour idClient={$data->idClient}");
            throw new Exception("Client sanctionné ou inactif.");
        }

        // Vérifier si le type de prêt existe
        error_log("Vérification du type de prêt idTypePret={$data->idTypePret}");
        $stmt = $db->prepare("SELECT tauxInteret, dureeMaxMois FROM TypePret_EF WHERE idTypePret = ?");
        $stmt->execute([$data->idTypePret]);
        $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$typePret) {
            error_log("Erreur: Type de prêt invalide pour idTypePret={$data->idTypePret}");
            throw new Exception("Type de prêt invalide.");
        }

        // Vérifier la durée
        error_log("Vérification de la durée: dureeMois={$data->dureeMois}, dureeMaxMois={$typePret['dureeMaxMois']}");
        if ($data->dureeMois > $typePret['dureeMaxMois']) {
            error_log("Erreur: Durée excessive");
            throw new Exception("La durée dépasse la durée maximale autorisée.");
        }

        // Vérifier le solde de l'établissement
        error_log("Vérification de l'établissement idEtablissementFinancier={$data->idEtablissementFinancier}");
        $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$data->idEtablissementFinancier]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$etablissement) {
            error_log("Erreur: Établissement inexistant pour idEtablissementFinancier={$data->idEtablissementFinancier}");
            throw new Exception("Établissement financier inexistant.");
        }
        error_log("Vérification du solde: montant={$data->montant}, fondTotal={$etablissement['fondTotal']}");
        if ($data->montant > $etablissement['fondTotal']) {
            error_log("Erreur: Montant supérieur au solde disponible");
            throw new Exception("Montant supérieur au solde disponible.");
        }

        // Vérifier le taux d'assurance
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        error_log("Taux d'assurance: {$tauxAssurance}%");
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            error_log("Erreur: Taux d'assurance invalide");
            throw new Exception("Le taux d'assurance doit être compris entre 0 et 5%.");
        }

        // Calculer les intérêts (incluant l'assurance)
        $tauxEffectif = $typePret['tauxInteret'] + $tauxAssurance;
        $interets = $data->montant * ($tauxEffectif / 100);
        error_log("Intérêts calculés (avec assurance): {$interets}");

        // Calculer la date de retour estimée
        $dateRetourEstimee = date('Y-m-d', strtotime($data->dateDemande . ' + ' . $data->dureeMois . ' months'));
        error_log("Date de retour estimée: {$dateRetourEstimee}");

        try {
            error_log("Tentative d'insertion dans Pret_EF");
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
                $tauxAssurance
            ]);
            $id = $db->lastInsertId();
            error_log("Prêt créé avec idPret={$id}");
            return $id;
        } catch (PDOException $e) {
            error_log("Erreur SQL dans create: " . $e->getMessage());
            throw new Exception("Erreur lors de la création du prêt: " . $e->getMessage());
        }
    }

    public static function valider($id) {
        $db = getDB();
        
        error_log("Validation du prêt idPret={$id}");
        // Récupérer les informations du prêt
        $stmt = $db->prepare("
            SELECT p.montant, p.idEtablissementFinancier, p.statut 
            FROM Pret_EF p 
            WHERE p.idPret = ?
        ");
        $stmt->execute([$id]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pret) {
            error_log("Erreur: Prêt non trouvé pour idPret={$id}");
            throw new Exception("Prêt non trouvé.");
        }
        if ($pret['statut'] !== 'en_attente') {
            error_log("Erreur: Le prêt n'est pas en attente pour idPret={$id}");
            throw new Exception("Le prêt n'est pas en attente de validation.");
        }

        // Vérifier le solde de l'établissement
        error_log("Vérification du solde pour idEtablissementFinancier={$pret['idEtablissementFinancier']}");
        $stmt = $db->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
        $stmt->execute([$pret['idEtablissementFinancier']]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($pret['montant'] > $etablissement['fondTotal']) {
            error_log("Erreur: Solde insuffisant pour idEtablissementFinancier={$pret['idEtablissementFinancier']}");
            throw new Exception("Solde insuffisant pour valider le prêt.");
        }

        try {
            $db->beginTransaction();

            // Mettre à jour le prêt
            error_log("Mise à jour du prêt idPret={$id} à statut=accorde");
            $stmt = $db->prepare("
                UPDATE Pret_EF 
                SET statut = 'accorde', dateAccord = CURDATE() 
                WHERE idPret = ?
            ");
            $stmt->execute([$id]);

            // Mettre à jour le solde de l'établissement
            error_log("Mise à jour du solde pour idEtablissementFinancier={$pret['idEtablissementFinancier']}");
            $stmt = $db->prepare("
                UPDATE EtablissementFinancier_EF 
                SET fondTotal = fondTotal - ? 
                WHERE idEtablissementFinancier = ?
            ");
            $stmt->execute([$pret['montant'], $pret['idEtablissementFinancier']]);

            $db->commit();
            error_log("Prêt validé avec succès pour idPret={$id}");
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erreur SQL dans valider: " . $e->getMessage());
            throw new Exception("Erreur lors de la validation du prêt: " . $e->getMessage());
        }
    }

    public static function getAll() {
        $db = getDB();
        error_log("Récupération de tous les prêts");
        $stmt = $db->query("
            SELECT p.*, t.libelle, c.nom, c.prenom 
            FROM Pret_EF p 
            JOIN TypePret_EF t ON p.idTypePret = t.idTypePret 
            JOIN Client_EF c ON p.idClient = c.idClient
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function simuler($data) {
        $db = getDB();
        
        error_log("Simulation du prêt: " . print_r($data, true));

        // Vérifier si le type de prêt existe
        $stmt = $db->prepare("SELECT tauxInteret, dureeMaxMois FROM TypePret_EF WHERE idTypePret = ?");
        $stmt->execute([$data->idTypePret]);
        $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$typePret) {
            error_log("Erreur: Type de prêt invalide pour idTypePret={$data->idTypePret}");
            throw new Exception("Type de prêt invalide.");
        }

        // Vérifier la durée
        if ($data->dureeMois > $typePret['dureeMaxMois']) {
            error_log("Erreur: Durée excessive");
            throw new Exception("La durée dépasse la durée maximale autorisée.");
        }

        // Vérifier le taux d'assurance
        $tauxAssurance = isset($data->tauxAssurance) ? floatval($data->tauxAssurance) : 0.00;
        if ($tauxAssurance < 0 || $tauxAssurance > 5) {
            error_log("Erreur: Taux d'assurance invalide");
            throw new Exception("Le taux d'assurance doit être compris entre 0 et 5%.");
        }

        // Calculer le taux effectif mensuel
        $tauxEffectifMensuel = ($typePret['tauxInteret'] + $tauxAssurance) / 100 / 12;
        $montant = floatval($data->montant);
        $dureeMois = intval($data->dureeMois);

        // Calculer l'annuité sans arrondi intermédiaire
        $puissance = pow(1 + $tauxEffectifMensuel, $dureeMois);
        $annuite = $montant * $tauxEffectifMensuel * $puissance / ($puissance - 1);
        $coutTotal = $annuite * $dureeMois;
        $interetsTotaux = $coutTotal - $montant;

        error_log("Résultat de la simulation: annuité=" . round($annuite, 2) . ", intérêts totaux=" . round($interetsTotaux, 2) . ", coût total=" . round($coutTotal, 2));

        return [
            'annuite' => round($annuite, 2),
            'interetsTotaux' => round($interetsTotaux, 2),
            'coutTotal' => round($coutTotal, 2),
            'montant' => $montant,
            'dureeMois' => $dureeMois,
            'tauxInteret' => $typePret['tauxInteret'],
            'tauxAssurance' => $tauxAssurance
        ];
    }
}
?>