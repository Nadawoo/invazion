<?php
class HtmlStatusBar {
    
    
    /**
     * Displays the status bar at the top of the map (wound, etc.)
     * 
     * @return string HTML
     */
    public function statusbar($action_points, $city_id, $is_wounded, $nbr_bag_items, $nbr_zone_fellows) {
        
        $status_defenses = (is_int($city_id))
            ? $this->status_image("Attaque du soir", "resources/img/copyrighted/wolf.png", null,
                                "Construisez des défenses dans votre ville &#10;pour contrer l'attaque zombie du soir !",
                                "popattack")
            : $this->status_image("Abri", "resources/img/copyrighted/home_house.png", "&#9888;&#65039;",
                                  "Abritez-vous dans une ville ou une tente &#10;avant la prochaine attaque zombie, &#10;sinon vous mourrez !",
                                  "popattack");
                
        $status_wounded = ($is_wounded >= 1)
            ? $this->status_image("Blessure", "resources/img/copyrighted/wound.png", "&#9888;&#65039;",
                                  "Vous êtes blessé ! Soignez-vous rapidement &#10;pour ne pas mourir d'infection...")
            : $this->status_empty();
        
        $status_fellows = ($nbr_zone_fellows >= 1)
            ? $this->status_image("&#128101;", null, $nbr_zone_fellows,
                                "D'autres humains se trouvent dans la même zone que vous ! L'union fait la force...")
            :$this->status_empty();
                
        $status_actionpoints = $this->status_image("&#9889;", null, $action_points,
                                                  "Vos points d'action restants. S'ils sont épuisés, &#10;vous ne pourrez plus vous déplacer dans le désert.",
                                                  "popmove");
        $status_bag = $this->status_image("&#127890;", null, $nbr_bag_items,
                                         "Votre sac à dos permet de transporter les objets trouvés pendant vos explorations.");

        return
        '<div id="statusbar">'.
            $status_actionpoints.
            $status_bag.
            $status_defenses.
            $status_fellows.
            $status_wounded.
        '</div>';
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
    private function status_image($img_text, $img_path, $amount, $title, $popup_id=null) {
        
        // If a pop-up must open when we click on the status icon
        $popup_link   = ($popup_id !== null) ? 'href="#'.$popup_id.'"' : '';
        $cursor_style = ($popup_id !== null) ? 'style="cursor:pointer"' : '';
        // The icon can be an image file or an emoji
        $html_icon    = ($img_path !== null) ? '<img src="'.$img_path.'" alt="'.$img_text.'">' : $img_text;
        // Display or not the amount for this item
        $html_amount = ($amount !== null) ? '<span class="dot_number">'.$amount.'</span>' : '';
        
        return '<a class="status" title="'.$title.'" '.$popup_link.' '.$cursor_style.'>
                    '.$html_icon . $html_amount.'
                    
                </a>';
    }
    
    
    /**
     * Displays an empty status block
     * 
     * @return string HTML
     */
    private function status_empty() {
        
        return '<div class="status empty"></div>';
    }
}
