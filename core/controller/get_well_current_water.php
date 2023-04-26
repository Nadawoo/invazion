<?php
/**
 * Gets the amount of water rations in the well
 * 
 * @param array $items_inside_constructions The items stored in the constructions 
 *                                          of the city, as returned by the API "items"
 * @param type $well_construction_id The ID of the well built in this city 
 *                                  (not the ID of the building type)
 * @return type
 */
function get_well_current_water($items_inside_constructions, $well_construction_id)
{
    
    // The ID of the "water ration" item in the game (see the "configs" API to get the list)
    $item_water_id = 9;
    $well_current_water = 0;
    
    if(isset($items_inside_constructions[$well_construction_id]) 
        and isset($items_inside_constructions[$well_construction_id][$item_water_id])
        ) {
        $well_current_water = $items_inside_constructions[$well_construction_id][$item_water_id];
    }
    
    return $well_current_water;
}
        

