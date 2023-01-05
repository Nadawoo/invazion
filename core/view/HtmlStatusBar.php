<?php
class HtmlStatusBar {
    
    
    /**
     * Displays the status bar at the top of the map (wound, etc.)
     * 
     * @return string HTML
     */
    public function statusbar($action_points, $city_id, $is_wounded, $nbr_bag_items, $nbr_zone_fellows) {
        
        $status_defenses = (is_int($city_id))
            ? $this->status_image("Attaque du soir", "resources/img/copyrighted/wolf.png", "&#9888;&#65039;",
                                "Construisez des défenses dans votre ville &#10;pour contrer l'attaque zombie du soir !")
            : $this->status_image("Abri", "resources/img/copyrighted/home_house.png", "&#9888;&#65039;",
                                  "Abritez-vous dans une ville ou une tente &#10;avant la prochaine attaque zombie, &#10;sinon vous mourrez !");
                
        $status_wounded = ($is_wounded >= 1)
            ? $this->status_image("Blessure", "resources/img/copyrighted/wound.png", "&#9888;&#65039;",
                                  "Vous êtes blessé ! Soignez-vous rapidement &#10;pour ne pas mourir d'infection...")
            : $this->status_empty();
        
        $status_fellows = ($nbr_zone_fellows >= 1)
            ? $this->status_text("&#128101;", $nbr_zone_fellows,
                                "D'autres humains se trouvent dans la même zone que vous ! L'union fait la force...")
            :$this->status_empty();
                
        $status_actionpoints = $this->status_text("&#9889;", $action_points,
                                                  "Vos points d'action restants. S'ils sont épuisés, &#10;vous ne pourrez plus vous déplacer dans le désert.");
        $status_bag = $this->status_text("&#127890;", $nbr_bag_items,
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
     * @param string $img_alt
     * @param string $img_path
     * @param int $amount
     * @param string $title
     * @return string HTML
     */
    private function status_image($img_alt, $img_path, $amount, $title) {
        
        return '<div class="status" title="'.$title.'">
                    <img src="'.$img_path.'" alt="'.$img_alt.'">
                    <span class="dot_number">'.$amount.'</span>
                </div>';
    }
    
    
    /**
     * Displays a status block with an emoji as icon
     * 
     * @param int $amount
     * @param string $title
     * @return string HTML
     */
    private function status_text($text, $amount, $title) {
        
        return '<div class="status" title="'.$title.'">
                    '.$text.'
                    <span class="dot_number" style="background:#28b463">'.$amount.'</span>
                </div>';
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
