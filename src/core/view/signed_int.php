<?php
/**
 * Ajoute le signe "+" devant un nombre positif (ex : "+15").
 * Permet un affichage plus explicite pour l'utilisateur.
 * 
 * @param  int $int
 * @return string
 */
function signed_int($int)
{
    
    if ($int > 0) {
        
        return '+'.$int;
    }
    elseif ($int <= 0) {
        
        return (string)$int;
    }
}

