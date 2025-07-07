<?php
require_once __DIR__ . '/../helpers/Utils.php';

class PretModel {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

   
    public function creerPretAvecRemboursements($idClient, $idTypePret, $montant, $dureeMois, $dateDemande, $dateAccord, $tauxInteret, $datePremiereEcheance) {
        try {
            $this->pdo->beginTransaction();
         
            $stmt = $this->pdo->prepare("INSERT INTO Pret_EF (idClient, idTypePret, montant, dureeMois, dateDemande, dateAccord, statut) VALUES (?, ?, ?, ?, ?, ?, 'accorde')");
            $stmt->execute([$idClient, $idTypePret, $montant, $dureeMois, $dateDemande, $dateAccord]);
            $idPret = $this->pdo->lastInsertId();

            
            $tableau = Utils::genererTableauAmortissement($montant, $tauxInteret, $dureeMois, $datePremiereEcheance);
            $stmtR = $this->pdo->prepare("INSERT INTO Remboursement_EF (idPret, montant, date_remboursement, interet) VALUES (?, ?, ?, ?)");
            foreach ($tableau as $ligne) {
                $stmtR->execute([$idPret, $ligne['montant'], $ligne['date_remboursement'], $ligne['interet']]);
            }
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Prêt et remboursements créés avec succès.'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
