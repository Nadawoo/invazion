<?php
/**
 * A partir d'un numéro de colonne ordinaire, calcule la coordonnée X conforme
 * au système des coordonnées doublées (dans lequel  les X sautent de 2 en 2, 
 * voir la doc sur le site d'Azimutant).
 * 
 * @param int $col_num  Le numéro théorique de la colonne (1, 2, 3), sans tenir compte
 *                      de la numérotation particulière des coordonnées doublées
 * @param int $coord_y  Le numéro de la coordonnée Y (= n° de la ligne de la carte)
 * @return int La valeur de la coordonnée X dans le système de coordonnées doublées
 */
function get_coordx($col_num, $coord_y)
{
    
    // On utilise le système des « coordonnées doublées » 
    // afin de simplifier les algorithmes de déplacement, distance, etc.
    // Explications détaillées sur le site d'Azimutant.
    
    // La coordonnée X augmente donc de 2 en 2...
    $coord_x = $col_num*2;
    
    // ... Et on décale le X toutes les lignes impaires, afin d'éviter 
    // qu'un même X désigne 2 colonnes différentes.
    //      Pas bon (zig-zag du 6) :    Bon :
    //      [6:2]                       [6:2]
    //        [6:3]                       [7:3]
    //      [6:4]                       [6:4]
    if ($coord_y%2 === 1) {
        $coord_x = $coord_x+1;
    }
    
    return $coord_x;
}
