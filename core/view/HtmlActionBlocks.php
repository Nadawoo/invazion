<?php

/**
 * Frames displayed by the round action buttons (move, dig, zombies, humans, build)
 * Each method contains a frame triggered by the round action buttons (<fieldset id="block_...">),
 * dont't add methods for sub-elements here.
 * Name each method according to the name of the frame
 * (ex: the method "block_dig() contains the <fieldset id="block_dig">)
 */
class HtmlActionBlocks {
    
    
    function __construct() {
        
        $this->buttons = new HtmlButtons();
        $this->layout  = new HtmlLayout();
    }
    
    
    function block_dig($html_ground_items, $citizen_can_dig) {
        
        return '
            <fieldset role="tabpanel" aria-labelledby="block_dig" id="block_dig" class="z-depth-2 hidden">
                <div class="center">
                    '.$this->buttons->button('dig', true, 'big', $citizen_can_dig).'
                    <a href="#popitems" style="margin-left:0.8rem;font-size:1.2em">[?]</a>
                </div>
                <br>
                
                &#x270B;&#x1F3FC; <strong>Objets au sol</strong>
                    '.$html_ground_items.'
            </fieldset>';
    }
    
    
    function block_citizens() {
        
        return '
            <fieldset role="tabpanel" aria-labelledby="block_citizens" id="block_citizens" class="z-depth-2 hidden">
                <strong>Humains dans ma zone</strong>
                <p class="greytext"><br>Personne à proximité. Vous êtes seul au milieu 
                    de cette zone désertique...</p>
                <ol id="citizensInMyZone" class="citizens" data-coordx="" data-coordy=""></ol>
                <hr>
                <strong>Autres humains sur la carte</strong>
                <ol id="citizensInOtherZones" class="citizens" data-coordx="" data-coordy=""></ol>
            </fieldset>';
    }
    
    
    function block_zombies($zone_zombies, $citizen_bag_items, $config_items, $killing_zombie_cost) {
        
        $html_actions_zombies = $this->layout->block_actions_zombies($zone_zombies, $killing_zombie_cost);
        $html_actions_bag     = $this->layout->block_actions_bag($config_items, $citizen_bag_items);
        
        return '
            <fieldset role="tabpanel" aria-labelledby="block_zombies" id="block_zombies" class="z-depth-2 hidden">
                '.$html_actions_zombies.'
                <br>
                '.$html_actions_bag.'
            </fieldset>';
    }
    
    
    function block_build($coord_x, $coord_y) {
        
        return '
            <fieldset role="tabpanel" aria-labelledby="block_build" id="block_build" class="z-depth-2 hidden">
                <ul id="builder" class="collapsible">
                    <li>
                        <div class="collapsible-header"><strong>&#x1F6E0;&#xFE0F; Construire (tous)</strong> <strong>&gt;</strong></div>
                        <div class="collapsible-body">
                            <ul class="items_list"></ul>
                        </div>
                    <li>
                </ul>
                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header"><strong>&#x1F6E0;&#xFE0F; Construire (spéciaux)</strong> <strong>&gt;</strong></div>
                        <div class="collapsible-body">
                            '.$this->layout->block_actions_build().'
                        </div>
                    <li>
                </ul>
                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header"><strong>&#x26F0;&#xFE0F; Modifier le terrain</strong> <strong>&gt;</strong></div>
                        <div class="collapsible-body">
                            '.$this->layout->block_edit_land($coord_x, $coord_y).'
                        </div>
                    <li>
                </ul>
                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header"><strong>&#x1F9DF; Pouvoirs (joueur zombie)</strong> <strong>&gt;</strong></div>
                        <div class="collapsible-body">
                            '.$this->layout->block_zombie_powers().'
                        </div>
                    <li>
                </ul>
            </fieldset>';
    }
}
