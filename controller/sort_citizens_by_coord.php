<?php
/**
 * Sorts citizens by their location on the map instead of their id
 * 
 * @param array $citizens   The citizens data, as returned by the "citizens" API
 * 
 * @return array The citizens data index by coordinates
 *              [0_2] => [
 *                  [0] => [data of a citizen],
 *                  [1] => [data of another citizen],
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
