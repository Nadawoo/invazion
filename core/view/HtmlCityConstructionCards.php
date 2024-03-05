<?php
safely_require('/core/controller/ItemsController.php');


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
        
        $htmlItem = new HtmlItem();
        $itemsController = new ItemsController();
        $result_resources = '';
        $result_buildable = '';
        
        $sorted_buildings_caracs = $itemsController->sort_buildings_by_missing_components(
                                                        $city_buildings_caracs,
                                                        $city_buildings_components,
                                                        $items_in_storage);
    
        // For each possible building...
        foreach($sorted_buildings_caracs as $building_id=>$building_caracs) {
            // ... if not already built
            // TODO: we could avoid this naive condition by removing first
            //  the useless constructions from $building_caracs
            if(!in_array($building_id, $completed_buildings_ids)) {
                //... display a card
                $AP_invested_in_construction = isset($city_constructions[$building_id]) ? $city_constructions[$building_id]['AP_invested'] : 0;
                $building_components = (isset($city_buildings_components[$building_id])) ? $city_buildings_components[$building_id] : [];
                
                // Keep only the "real" resources, excluding action points (wood, metal...)
                $building_components_resources = $itemsController->filter($building_components, 'resources');
                $items_missing = $itemsController->get_missing_items($building_components_resources, $items_in_storage, false);
                
                if(array_sum($items_missing) === 0) {
                    $result_buildable   .= $this->card_buildable($building_caracs, $building_components,
                                           $building_id, $AP_invested_in_construction)
                                        . '<hr>';
                }
                else {
                    $result_resources   .= $this->card_resources($items_caracs, 
                                                $building_caracs, $items_missing)
                                        . '<hr>';
                }
            }
        }
        
        $card_icon = $htmlItem->icon(null, "&#x1F4A1;", 48);
        
        return '
            <a id="to_constructions" class="redbutton" onclick="toggle(\'#constructions_block\');hide(\'to_constructions\');hide(\'tip_buildable\');hide(\'tip_resources\')">&lt;&lt;</a>
                
            <div id="tip_buildable" class="city_block construction_card">
                <h2>'.$card_icon.'&nbsp;Chantiers constructibles</h2>
                <p class="descr">Vous pouvez construire ces chantiers car 
                    les ressources requises sont réunies 
                    <a href="#" onclick="switchCitySubmenu(\'city_storage\')">au dépôt</a> !
                </p>
                <div class="contents">'.$result_buildable.'</div>
            </div>
            <div id="tip_resources" class="city_block construction_card">
                <h2>'.$card_icon.'&nbsp;Ressources à compléter</h2>
                <p class="descr">Rapportez ces objets lors de 
                    <a href="#" onclick="switchCitySubmenu(\'explore\')">vos explorations</a>
                    afin de construire de nouveaux chantiers.
                </p>
                <div class="contents">'.$result_resources.'</div>
            </div>';
    }
    
    
    /**
     * HTML to list one suggestion of resources to bring back from the desert
     * ("item 1 + item 2 + item 3... will unlock the construction X")
     * 
     * @param array $items_caracs The characteristics of the items 
     *                  existing in the game (name, icon...), as returned 
     *                  by the game's API "configs[items]"
     * @param array $construction_caracs The characteristics of the concerned construction,
     *                  as returned by the game's API "configs[constructions][id]"
     * @param array $items_missing List of the items not available (ex: not in the city storage)
     *                  to build the construction
     * @return string HTML
     */
    private function card_resources($items_caracs, $construction_caracs, $items_missing) {
        
        $construction_name = $construction_caracs['name'];
        $missing_resources = $this->missing_resources($items_missing, $items_caracs);
        
        return $missing_resources.'
            <p>&#x27A1;&#xFE0F; Débloquera le chantier <strong>'.$construction_name.'</strong></p>';
    }
    
    
    /**
     * HTML to list one buildable construction (= all the components are gathered
     * in the city storage, only action points miss)
     * 
     * @param array $construction_caracs The characteristics of the concerned construction,
     *                  as returned by the game's API "configs[constructions][id]"
     * @param array $construction_components All the items required to build the building,
     *                  including the action points. Structured as a list of pairs 
     *                  [item_id => item_amount]
     * @param int $building_id
     * @param int $AP_invested_in_construction
     * @return string HTML
     * 
     */
    private function card_buildable($construction_caracs, $construction_components,
                                    $building_id, $AP_invested_in_construction) {
        
        $htmlItem = new HtmlItem();
        $itemsController = new ItemsController();
        $building_image = $htmlItem->icon($construction_caracs['icon_path'], $construction_caracs['icon_html'], 48);
        
        // Missing action points
        $action_points_needed = $itemsController->filter($construction_components, 'action_points');
        $total_AP_missing = $action_points_needed - $AP_invested_in_construction;
        $card_contents = $this->missing_actionpoints($total_AP_missing, $building_id);
        
        return '<h3>'.$building_image.'&nbsp; '.$construction_caracs['name'].'</h3>
                    '.$card_contents;
    }
    
    
    /**
     * HTML for the items missing to build a defense building
     * 
     * @param array $items_missing
     * @param array $items_caracs
     * @return string HTML
     */
    private function missing_resources($items_missing, $items_caracs) {
        
        $htmlItem = new HtmlItem();
        $missing_items_icons = '';
        
        foreach($items_missing as $item_id=>$missing_amount) {            
            // Handles the anormal case where the resource needed is not 
            // in the list of items set for the current game 
            $item_caracs = isset($items_caracs[$item_id]) ? $items_caracs[$item_id] : set_default_variables('item');
            $item_icon = $htmlItem->icon($item_caracs['icon_path'], $item_caracs['icon_symbol'], 32);
            
            $missing_items_icons .= '
                <li class="item_label" style="height:2.5em;width:2.5em" title="'.$item_caracs['name'].'">
                    <span class="item_icon">'.$item_icon.'</span>
                    <span class="dot_number">'.$missing_amount.'</span>
                </li>';
        }
        
        return '<ul class="items_list components" style="justify-content:center">
                '.$missing_items_icons.'
            </ul>';
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
            <p>Il manque seulement<br>&#9889;<strong>'.plural($total_AP_missing, 'point').' d\'action</strong>
                <br>pour finir ce chantier !
                '.$buttons->construct($building_id, 'no_notif', 'Participer [1&#9889;]').'
            </p>';
    }
}
