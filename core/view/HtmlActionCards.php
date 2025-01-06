<?php

/**
 * The interactive cards inside the "move" round action button to drive the player 
 * to the available actions ("You can dig", "Building in the zone"...)
 */
class HtmlActionCards {
    
    
    function __construct() {
        
        $this->buttons  = new HtmlButtons();
        $this->popup    = new HtmlPopup();
    }
    
    
    function card_citizens() {
        
        return '
            <a id="card_citizens" class="card animate__animated animate__slideInLeft"
                onclick="toggleActionBlock(\'citizens\'); updateBlockAction(\'citizens\')">
                <img src="/resources/img/copyrighted/city_fellows.png" alt="&#x1F465;" height="48">
                Humains dans la zone !
                <i class="material-icons">chevron_right</i>
            </a>';
    }
    
    
    function card_building() {
        
        return '
            <div id="card_building" class="card animate__animated animate__slideInDown">
                <img src="resources/img/copyrighted/tiles/desert/10.png" height="96" width="73" alt="Bâtiment">
                <div style="margin-left:5.5rem">
                    <strong>Bâtiment découvert :<br><span class="building_name"></span></strong>
                    <br>'
                    .$this->buttons->button('enter_city')
                    .$this->buttons->button('destroy_city', 'no_icon')
                    .$this->popup->link('popsuccess', 'Explorer', 'button_explore', '&#x1F50E;')
                    .$this->popup->link('popvault', 'Pouvoir cryptique', 'button_crypt').
                    '<button class="ignore_button bluebutton" onclick="moveBuildingBlockBelowPaddle()">Ignorer</button>
                </div>
            </div>';
    }
    
    
    function card_dig() {
        
        return '
            <a id="card_dig" class="card animate__animated animate__slideInLeft"
                onclick="toggleActionBlock(\'dig\'); updateBlockAction(\'dig\')">
                <img src="/resources/img/copyrighted/mining_48px.png" alt="&#9935;&#65039;" height="48">
                La zone peut être fouillée.
                <i class="material-icons">chevron_right</i>
            </a>';
    }
    
    
    function card_ap_cost() {
        
        return '
            <a href="#popmove" id="card_ap_cost" class="card animate__animated animate__slideInLeft">
                <span>Quitter la zone vous coûtera <strong>1</strong>&#9889;</span>
                <span class="actionspoints_decrease"></span>
             </a>';
    }
}
