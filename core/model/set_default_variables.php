<?php
/**
 * Initializes default values in place of the API values, in case the player 
 * is not connected.
 *  
 * @param string $variable  The type of data you want to initialize. Chose one 
 *                          of the values set in the $default array.
 * @return array
 */
function set_default_variables($variable, $item_id=null) {
    
    $default['citizen'] = [
        'speciality'        => 'weak',
        'action_points'     => 0,
        'bag_items'         => [],
        'bag_size'          => 4,
        'vision'            => 0,
        'camouflage'        => 0,
        'user_id'           => null,
        'citizen_id'        => null,
        'coord_x'           => null,
        'coord_y'           => null,
        'citizen_pseudo'    => null,
        'city_id'           => null,
        'inside_city_id'    => null,
        'last_specialization_cycle' => null,
        'can_dig'               => true,
        'is_inside_city'        => false,
        'is_wounded'            => false,
        'last_death'            => null,
        'zones_visited_today'   => [],
        // If player not connected, by default display the map #1
        'map_id'            => 1,
        ];
    
    $default['zone'] = [
        'building_id'               => null,
        'city_size'                 => 0,
        'controlpoints_citizens'    => 0,
        'controlpoints_zombies'     => 0,
        'items'                     => [],
        'zombies'                   => 0,
        ];
    
    $default['item'] = [
            'icon_symbol'       => '&#x2753;',
            'icon_path'         => null,
            'name'              => '{Objet inconnu}',
            'descr_ambiance'    => '',
            'descr_purpose'     => "[Bug] L'objet #".$item_id." est inconnu sur cette carte. "
                                 . "Signalez-le à l'administrateur.",
            'is_weapon'         => '',
            'healing_wound'     => '',
            'items_inside_min'  => '',
        ];
    
    return $default[$variable];
}
