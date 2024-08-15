<?php
class HtmlStatusBar {
    
    
    /**
     * Displays the status bar at the top of the map (wound, etc.)
     * 
     * @param array $bag_items The list of items in the citizen's bag,
     *                         as returned by the Invazion's API
     * @param int $city_id
     * @param int $is_wounded Value "1" if the citizen is wounded
     * @param int $nbr_zone_fellows The number of humans in the citizen's zone
     * @return string HTML
     */
    public function statusbar($bag_items, $city_id, $is_wounded, $nbr_zone_fellows) {
        
        // #23 = ID of the item "action points" in the database
        $ap_item_id = 23;
        $action_points = isset($bag_items[$ap_item_id]) ? $bag_items[$ap_item_id] : 0;
        
        $status_my_caracs = $this->status_image("&#x1F464;", null, null,
                                "Mes caractéristiques de base",
                                "popmycaracs");
        $status_defenses = (is_int($city_id))
            ? $this->status_image("Attaque du soir", "resources/img/copyrighted/wolf.png", null,
                                "Construisez des défenses dans votre ville &#10;pour contrer l'attaque zombie du soir !",
                                "popattack")
            : $this->status_image("Abri", "resources/img/copyrighted/home_house.png", null,
                                  "Abritez-vous dans une ville ou une tente &#10;avant la prochaine attaque zombie, &#10;sinon vous mourrez !",
                                  "popattack");
                
        $status_wounded = ($is_wounded >= 1)
            ? $this->status_image("Blessure", "resources/img/copyrighted/wound.png", null,
                                  "Vous êtes blessé ! Soignez-vous rapidement &#10;pour ne pas mourir d'infection...",
                                  "popwounded")
            : $this->status_empty();
        
        $status_fellows = ($nbr_zone_fellows >= 1)
            ? $this->status_image("&#128101;", null, $nbr_zone_fellows,
                                "D'autres humains se trouvent dans la même zone que vous ! L'union fait la force...")
            :$this->status_empty();
                
        $status_actionpoints = $this->status_image($action_points,
                                                  null,
                                                  "&#9889;",
                                                  "Vos points d'action restants. S'ils sont épuisés, &#10;vous ne pourrez plus vous déplacer dans le désert.",
                                                  "popmove",
                                                  "actionpoints");

        return
        '<fieldset id="statusbar" class="z-depth-1">
            <div class="block_icon">
                <div class="icon">&#9877;&#65039;</div>
                <div class="name">États</div>
            </div>
            <ul class="items_list">'.
                $status_my_caracs.
                $status_defenses.
                $status_wounded.
                $this->status_empty().
                $this->status_empty().
                $status_fellows.
                $status_actionpoints.'
            </ul>
        </fieldset>';
    }
    
    
    /**
     * Displays a status block with an image as icon
     * 
     * @param string $img_text The emoji or text representing the status.
     * @param string $img_path The path to the image file representing the status.
     *                         Set to NULL if there is no image file. The $img_text
     *                         will be displayed instead.
     * @param int $amount The number for this item (ex: "3" action points).
     *                    If set to 0, a "0" will be displayed.
     *                    If set to NULL, the number won't be displayed at all.
     * @param string $title
     * @param string popup_id The HTML ID of the pop-up to display when clicking on the status
     * @return string HTML
     */
    private function status_image($img_text, $img_path, $amount, $title, $popup_id=null, $class=null) {
        
        // If a pop-up must open when we click on the status icon
        $popup_link   = ($popup_id !== null) ? 'href="#'.$popup_id.'"' : '';
        $cursor_style = ($popup_id !== null) ? 'style="cursor:pointer"' : '';
        // The icon can be an image file or an emoji
        $html_icon    = ($img_path !== null) ? '<img src="'.$img_path.'" alt="'.$img_text.'">' : $img_text;
        // Display or not the amount for this item
        $html_amount = ($amount !== null) ? '<span class="dot_number">'.$amount.'</span>' : '';
        
        return '<li class="item_label z-depth-1 '.$class.'">
                <a title="'.$title.'" '.$popup_link.' '.$cursor_style.'>
                    <span class="icon animate__animated">'.$html_icon.'</span>'
                    .$html_amount.'
                </a>
            </li>';
    }
    
    
    /**
     * Displays an empty status block
     * 
     * @return string HTML
     */
    private function status_empty() {
        
        return '<li class="item_label empty_slot"></li>';
    }
}
