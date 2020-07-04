<?php
/**
 * Initializes default values in place of the API values, in case the player 
 * is not connected.
 *  
 * @param string $variable  The type of data you want to initialize. Chose one 
 *                          of the values set in the $default array.
 * @return array
 */
function set_default_variables($variable) {
    
    $default['citizen'] = [
        'action_points'     => 0,
        'bag_items'         => [],
        'bag_size'          => 4,
        'citizen_id'        => 0,
        'coord_x'           => null,
        'coord_y'           => null,
        'citizen_pseudo'    => null,
        'is_inside_city'    => false,
        'is_wounded'        => false,
        // If player not connected, by default display the map #1
        'map_id'            => 1,
        ];
    
    $default['zone'] = [
        'building'                  => null,
        'city_size'                 => 0,
        'controlpoints_citizens'    => 0,
        'controlpoints_zombies'     => 0,
        'items'                     => [],
        'zombies'                   => 0,
        ];
    
    return $default[$variable];
}
