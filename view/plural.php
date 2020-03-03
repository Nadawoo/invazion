<?php
/**
 * Met automatiquement le mot au pluriel s'il y en a 2 ou plus.
 * Utile accorder les mots dont on ne connaît pas la quantité à l'avance
 * sans ajouter des "if" partout.
 * La fonction ne gère que les pluriels en "S" pour l'instant (pas ceux en "-aux").
 * 
 * @param int    $amount        La quantité de l'objet
 * @param string $singular_word Le mot, au singulier
 * 
 * @return string Exemples : "1 zombie"
 *                           "5 zombies"
 */
function plural($amount, $singular_word)
{

    $s = ($amount <= 1) ? '' : 's';

    return $amount.' '.$singular_word. $s;
}

