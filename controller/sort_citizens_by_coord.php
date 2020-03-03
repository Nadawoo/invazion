<?php
/**
 * Trie les citoyens par leur emplacement sur la carte au lieu de leur id
 * 
 * @param array $citizens   Les données des citoyens telles que retournées 
 *                          par l'API citizens
 * 
 * @param array Les données des citoyens, indexées par coordonnées
 *              [0_2] => [
 *                  [0] => [données du citoyen],
 *                  [1] => [données du citoyen],
 *                  ...
 *                  ],
 *              [0_3] => ...
 */
function sort_citizens_by_coord($citizens)
{
    
    $citizens_by_coord = [];

    foreach ($citizens as $val) {
        
        $coords = $val['coord_x'].'_'.$val['coord_y'];
        $citizens_by_coord[$coords][] = $val;
    }
    
    return $citizens_by_coord;
}
