<?php
/**
 * 
 */
class ItemsController {
    
    // ID #23 = the ID of the action points (treated as an ordinary resource)
    private $ap_item_id = 23;
    
    
    /**
     * Filter a list of components (ex: components to build a construction in the city)
     * to keep only the real resources (wood, metal...) or only the action points.
     * 
     * @param array $components [itemID => itemAmount, itemID => itemAmount, ...]
     * @param string $item_type_wanted :
     *      - "resources" to keep only the real resources (wood, metal...), 
     *         excluding the action points
     *      - "action_points" to keep only the action points needed, exluding the resources
     */
    function filter($components, $item_type_wanted) {
        
        if($item_type_wanted === 'resources') {
            unset($components[$this->ap_item_id]);
        }
        elseif($item_type_wanted === 'action_points') {
            $components = isset($components[$this->ap_item_id]) ? $components[$this->ap_item_id] : 0;
        }
        else {
            throw new Exception('Filter "'.$item_type_wanted.'" is not allowed in ItemsController->filter()');
        }
        
        return $components;
    }
    
    
    /**
     * Sorts a list of constructions by increasing number of missing components.
     * Useful to suggest which resources should be added in priority to complete 
     * the easiest constructions.
     * 
     * @param array $buildings_caracs The characterictics of the building, 
     *                  as returned by the "configs[buildings]" API.
     * @param array $buildings_components The items required to build a building, 
     *                  as returned by the "configs[buildings_components]" API.
     * @param array $resources_available The items available (ex: in the city storage)
     * @return array
     */
    function sort_buildings_by_missing_components($buildings_caracs, 
                                                  $buildings_components, 
                                                  $resources_available) {
        
        // TODO: sort the resources by:
        // 1) Number of *types* of missing resources => OK done
        // .. 2) Number of total *amount* of missing resources => TODO
        $suggested_resources = [];
        foreach($buildings_caracs as $building_id=>$building_caracs) {
            // Keep only the "real" resources, excluding action points (wood, metal...)
            $resources_needed = $this->filter($buildings_components[$building_id], 'resources');
            $resources_missing = get_missing_items($resources_needed, $resources_available, false);
            $nbr_types_resources_missing = count($resources_missing);
//            $nbr_resources_missing = array_sum($resources_missing);
            
            // Indexing the buildings by number of missing type resources will 
            // allow the sorting
            $suggested_resources[$nbr_types_resources_missing][] = $building_id;
        }
        
        // Place first the constructions that need the few *types* of resources
        // (but don't care about the needed *amount* of these resources).
        ksort($suggested_resources);        
        // Flattens the subarray to keep only the IDs of the buildings
        // (and preserve the previous sortings)
        $constructions_sorted_by_resources = array_merge(...$suggested_resources);
        // Add back the characteristics of the buildings to the sorted array.
        // Now we have the original array with all its data *and* sorted.
        return array_replace(array_flip($constructions_sorted_by_resources), $buildings_caracs);
    }
}
