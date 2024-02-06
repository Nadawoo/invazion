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
            <fieldset id="block_dig">
                <div style="display:flex;align-items:center">'.$this->buttons->button('dig', false, '', $citizen_can_dig).'
                    <a href="#popitems" style="margin-left:0.8rem;font-size:1.2em">[?]</a>
                </div>
                <hr>
                
                &#x270B;&#x1F3FC; <strong>Objets au sol</strong>
                    '.$html_ground_items.'
                    
                    <!--
                    <div id="items_ground">
                        <p class="greytext">
                            Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...
                        </p>
                        
                        <input type="hidden" name="api_name" value="zone">
                        <input type="hidden" name="action" value="pickup">
                        
                        <ul class="items_list" style="margin-left:1.5rem;"
                            data-coordx="" data-coordy=""></ul>
                    </div>
                    -->
            </fieldset>';
    }
    
    
    function block_citizens() {
        
        return '
            <fieldset id="block_citizens">
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
            <fieldset id="block_zombies">
                '.$html_actions_zombies.'
                <br>
                '.$html_actions_bag.'
            </fieldset>';
    }
    
    
    function block_build($coord_x, $coord_y) {
        
        return '
            <fieldset id="block_build">
                '.$this->layout->block_actions_build()
                .'<hr>'
                . $this->layout->block_edit_land($coord_x, $coord_y)
                .'<hr>'
                . $this->layout->block_zombie_powers().'
            </fieldset>';
    }
}
