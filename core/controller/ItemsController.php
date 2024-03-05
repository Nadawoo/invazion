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
     * Gives the list of the missing items required to do something.
     * Useful to know how many resources we need to add in the city storage
     * to build a construction.
     * 
     * @param array $items_needed The list of items required to do the action
     *                            [item ID => required amount, item ID => required amount, ...]
     * @param array $items_available The list of items available to do the action
     *                            [item ID => available amount, item ID => available amount, ...]
     * @param bool $keep_zero_amount_items If set to FALSE, the components already gathered 
     *                  in the storage will be totally removed. If set to TRUE, those components
     *                  will be kept in the array and just marked as "0" resource missing.
     *
     * @return array A list giving the missing amount for each item
     *               [item ID => missing amount, item ID => missing amount, ...]
     *               The amount is 0 when all the required amount is available.
     */
    function get_missing_items($items_needed, $items_available, $keep_zero_amount_items=true) {

        // From all the resources available in the zone, keep only the ones 
        // useful for the construction
        $items_available = array_intersect_key($items_available, $items_needed);
        $items_missing = [];

       // TODO: this naive foreach could be avoided by using array_map(), but 
       // array_map() doesn't preserves the keys (item ID). Try to improve this:
       //      $items_missing = array_map(function($nbr_items_needed, $nbr_items_available) {
       //                  return max(0, $nbr_items_needed-$nbr_items_available);
       //              },
       //              $items_needed, $items_available);
       foreach($items_needed as $item_id=>$nbr_item_needed) {

            $missing_amount = isset($items_available[$item_id])
                              ? max(0, $nbr_item_needed-$items_available[$item_id])
                              : $nbr_item_needed;

            if($keep_zero_amount_items === true) {
                $items_missing[$item_id] = $missing_amount;
            }
            elseif($keep_zero_amount_items === false and $missing_amount > 0) {
                $items_missing[$item_id] = $missing_amount;
            }
       }

       return $items_missing;
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
            $resources_missing = $this->get_missing_items($resources_needed, $resources_available, false);
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
