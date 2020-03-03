<?php
/**
 * Filtre une liste des citoyens avec leurs caractéristiques 
 * pour ne garder que ceux d'une ville précise.
 * 
 * @param  array $citizens  Array contenant les citoyens de toute la carte,
 *                          issu de l'API map.php
 * @param  int   $city_id   L'ID de la ville dont on veut garder les citoyens
 * @return array Array ne contenant que les citoyens de la ville demandée
 */
function filter_citizens_by_city($citizens, $city_id)
{
    return  array_filter($citizens, 
                function($a) use($city_id) {
                    return $a['city_id'] === $city_id;
                }
            );
}

