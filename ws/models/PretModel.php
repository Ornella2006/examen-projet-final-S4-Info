<?php
require_once __DIR__ . '/../helpers/Utils.php';

class PretModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function creerPretAvecRemboursements($idClient, $idTypePret, $idEtablissementFinancier, $montant, $dureeMois, $dateDemande, $dateAccord, $tauxAssurance) {
        try {
            $this->pdo->beginTransaction();

            // Vérifier le client
            $stmt = $this->pdo->prepare("SELECT actif FROM Client_EF WHERE idClient = ?");
            $stmt->execute([$idClient]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$client || !$client['actif']) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Client inexistant ou inactif'];
            }

            // Vérifier le type de prêt
            $stmt = $this->pdo->prepare("SELECT tauxInteret, dureeMaxMois FROM TypePret_EF WHERE idTypePret = ?");
            $stmt->execute([$idTypePret]);
            $typePret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$typePret) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Type de prêt invalide'];
            }
            if ($dureeMois > $typePret['dureeMaxMois']) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Durée dépasse la durée maximale autorisée'];
            }

            // Vérifier l'établissement
            $stmt = $this->pdo->prepare("SELECT fondTotal FROM EtablissementFinancier_EF WHERE idEtablissementFinancier = ?");
            $stmt->execute([$idEtablissementFinancier]);
            $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$etablissement) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Établissement financier inexistant'];
            }
            if ($montant > $etablissement['fondTotal']) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Montant supérieur au solde disponible'];
            }

            // Calculer les intérêts
            $tauxEffectifMensuel = ($typePret['tauxInteret'] + $tauxAssurance) / 100 / 12;
            $puissance = pow(1 + $tauxEffectifMensuel, $dureeMois);
            $annuite = $montant * $tauxEffectifMensuel * $puissance / ($puissance - 1);
            $interetsTotaux = $annuite * $dureeMois - $montant;

            // Calculer dateRetourEstimee
            $dateRetourEstimee = date('Y-m-d', strtotime($dateDemande . ' + ' . $dureeMois . ' months'));

            // Insérer le prêt
            $stmt = $this->pdo->prepare("
                INSERT INTO Pret_EF (idClient, idTypePret, idEtablissementFinancier, montant, dureeMois, 
                                    dateDemande, dateAccord, statut, interets, dateRetourEstimee, tauxAssurance)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'accorde', ?, ?, ?)
            ");
            $stmt->execute([$idClient, $idTypePret, $idEtablissementFinancier, $montant, $dureeMois, 
                           $dateDemande, $dateAccord, $interetsTotaux, $dateRetourEstimee, $tauxAssurance]);
            $idPret = $this->pdo->lastInsertId();

            // Générer et insérer les remboursements
            $tableau = Utils::genererTableauAmortissement($montant, $typePret['tauxInteret'] + $tauxAssurance, $dureeMois, $dateDemande);
            $stmtR = $this->pdo->prepare("INSERT INTO Remboursement_EF (idPret, montant, date_remboursement, interet) VALUES (?, ?, ?, ?)");
            foreach ($tableau as $ligne) {
                $stmtR->execute([$idPret, $ligne['montant'], $ligne['date_remboursement'], $ligne['interet']]);
            }

            // Mettre à jour le solde de l'établissement
            $stmt = $this->pdo->prepare("UPDATE EtablissementFinancier_EF SET fondTotal = fondTotal - ? WHERE idEtablissementFinancier = ?");
            $stmt->execute([$montant, $idEtablissementFinancier]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Prêt et remboursements créés avec succès', 'id' => $idPret];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
?>