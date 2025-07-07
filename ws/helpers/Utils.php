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

   
    public static function genererTableauAmortissement($montant, $tauxAnnuel, $nbMois, $dateDebut) {
        $tableau = [];
        $mensualite = self::calculMensualite($montant, $tauxAnnuel, $nbMois);
        $capitalRestant = $montant;
        $date = new DateTime($dateDebut);
        for ($i = 1; $i <= $nbMois; $i++) {
            $interet = $capitalRestant * ($tauxAnnuel / 12 / 100);
            $principal = $mensualite - $interet;
            if ($i == $nbMois) $principal = $capitalRestant; 
            $tableau[] = [
                'date_remboursement' => $date->format('Y-m-d'),
                'montant' => round($mensualite, 2),
                'interet' => round($interet, 2),
                'principal' => round($principal, 2)
            ];
            $capitalRestant -= $principal;
            $date->modify('+1 month');
        }
        return $tableau;
    }
}