<?php

class Utils {
    public static function formatDate($date) {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }

   
    public static function calculMensualite($montant, $tauxAnnuel, $nbMois) {
        $tauxMensuel = $tauxAnnuel / 12 / 100;
        if ($tauxMensuel == 0) return $montant / $nbMois;
        return ($montant * $tauxMensuel) / (1 - pow(1 + $tauxMensuel, -$nbMois));
    }

   
   public static function genererTableauAmortissement($montant, $tauxInteret, $dureeMois, $datePremiereEcheance) {
        $tableau = [];
        $tauxMensuel = $tauxInteret / 100 / 12;
        $puissance = pow(1 + $tauxMensuel, $dureeMois);
        $annuite = $montant * $tauxMensuel * $puissance / ($puissance - 1);
        
        $capitalRestant = $montant;
        $date = new DateTime($datePremiereEcheance);
        
        for ($i = 0; $i < $dureeMois; $i++) {
            $interet = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $annuite - $interet;
            $capitalRestant -= $capitalRembourse;
            
            $tableau[] = [
                'montant' => round($capitalRembourse, 2),
                'interet' => round($interet, 2),
                'date_remboursement' => $date->format('Y-m-d')
            ];
            
            $date->modify('+1 month');
        }
        
        return $tableau;
    }
}