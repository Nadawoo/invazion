<?php
safely_require('/core/controller/get_missing_items.php');


/**
 * Generates HTML to displays the city constructions in the form of cards
 * (one card = one construction)
 */
class HtmlCityConstructionCards
{
    
    
    /**
     * Generates the HTML for all the construction cards
     * 
     * @param array $items_caracs The characteristics of the items 
     *                  existing in the game (name, icon...), as returned 
     *                  by the game's API "configs[items]"
     * @param array $items_in_storage The items stored in the city storage, as returned 
     *                                by the game's API "city"
     * @param array $city_buildings_caracs The characteristics of the constructions 
     *                  existing in the game (name, icon, defenses...), as returned 
     *                  by the game's API "configs[constructions]"
     * @param array $city_buildings_components All the items required to build the buildings,
     *                  as returned by the game's API "configs[buildings_components]"
     * @param array $city_constructions The constructions built in the current city,
     *                  as returned by the game's API "city"
     * @return array HTML displaying all the contruction cards
     */
    function all_cards($items_caracs, $items_in_storage,
                       $city_buildings_caracs, $city_buildings_components, 
                       $city_constructions, $completed_buildings_ids) {
        
        $result = '';
        
        // For each possible building...
        foreach($city_buildings_caracs as $building_id=>$building_caracs) {
            // ... if not already built
            // TODO: we could avoid this naive condition by removing first
            //  the useless constructions from $building_caracs
            if(!in_array($building_id, $completed_buildings_ids)) {
                //... display a card
                $AP_invested_in_construction = isset($city_constructions[$building_id]) ? $city_constructions[$building_id]['AP_invested'] : 0;
                $result .= $this->card($items_caracs, $items_in_storage, 
                                       $building_caracs, $city_buildings_components[$building_id],
                                       $building_id, $AP_invested_in_construction);
            }
        }
        
        return $result;
    }
    
    
    /**
     * HTML for a single construction card
     * 
     * @param array $items_caracs The characteristics of the items 
     *                  existing in the game (name, icon...), as returned 
     *                  by the game's API "configs[items]"
     * @param array $items_in_storage The items stored in the city storage, as returned 
     *                                by the game's API "city"
     * @param array $construction_caracs The characteristics of the concerned construction,
     *                  as returned by the game's API "configs[constructions][id]"
     * @param array $construction_components All the items required to build the building,
     *                  as a list of pairs [item_id => item_amount]
     * @param int $building_id The ID of the concerned construction
     * @param int $AP_invested_in_construction The amount of action points already invested 
     *                                         for building the construction
     * @return string HTML
     */
    private function card($items_caracs, $items_in_storage,
                          $construction_caracs, $construction_components,
                          $building_id, $AP_invested_in_construction) {
        
        $construction_name = $construction_caracs['name'];
        // ID #23 = the ID of the action points (treated as an ordinary resource)
        $action_points_needed = $construction_components[23];
        // Keep only the "real" resources, excluding action points (wood, metal...)
        unset($construction_components[23]);
        
        // From all the resources available in the city storage, keep only the ones 
        // useful for the construction
        $items_available = array_intersect_key($items_in_storage, $construction_components);
        $items_missing = get_missing_items($construction_components, $items_available);
        
        // Total amount of missing items
        $total_items_missing = array_sum($items_missing);
        $total_AP_missing = $action_points_needed - $AP_invested_in_construction;
        
        $card_contents = ($total_items_missing === 0)
            ? $this->missing_actionpoints($total_AP_missing, $building_id)
            : $card_contents = $this->missing_resources($items_missing, $total_items_missing, $items_caracs);
        
        return '
            <div class="city_block">
                <h2>Chantier</h2>
                <div class="contents">
                    <h3 style="height:2.2em;color:black;text-align:center;font-size:1.3em;letter-spacing:normal;">
                        <img src="resources/img/copyrighted/buildings/'.$building_id.'.png"
                             height="32" width="32" alt="icon">
                        '.$construction_name.'
                    </h3>
                    '.$card_contents.'
                </div>
            </div>';
    }
    
    
    private function missing_resources($items_missing, $total_items_missing, $items_caracs) {
        
        // HTML for the icons of the missing items
        $missing_items_icons = '';
        foreach($items_missing as $item_id=>$missing_amount) {
            $missing_items_icons .= '
                <span title="'.$items_caracs[$item_id]['name'].'">
                    <img src="resources/img/copyrighted/items/'.$item_id.'.png"
                         width="32" height="32" alt="icon">x'. $missing_amount.'
                <span> ';
        }   
        
        return '
            <p>&#10060; Pour construire ce chantier, ajoutez 
                <strong>'.$total_items_missing.' objets</strong> 
                <a href="#" onclick="switchCitySubmenu(\'city_storage\')">au dépôt</a> :</p>
            <p style="font-weight:bold;color:red">'.$missing_items_icons.'</p>';
    }
    
    
    private function resources_completed() {
        
        return '
            <p>&#x2714;&#xFE0F; Les matériaux requis sont présents 
                <a href="#" onclick="switchCitySubmenu(\'city_storage\')">au dépôt</a>.
            </p>';
    }
    
    
    /**
     * Text to invite the player to put action points to build the construction
     * 
     * @param int $total_AP_missing the number of action points required to achieve 
     *                              the construction
     * @param int $building_id
     * @return string HTML
     */
    private function missing_actionpoints($total_AP_missing, $building_id) {
        
        $buttons = new HtmlButtons();
        
        return '
            <p>&#128296; Il manque <strong>'.$total_AP_missing.' points d\'action</strong>
                pour terminer le chantier :
            '.$buttons->construct($building_id, 'notify', 'Participer [1 PA]').'
            </p>';
    }
}
