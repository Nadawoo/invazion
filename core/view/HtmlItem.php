<?php

/**
 * Generates the HTML for the items in the bag, bank, ground
 * (icon, name, button to use it...)
 */
class HtmlItem {
    
    
    /**
     * HTML blank template to display an item (icon + tooltip with description, etc.)
     * The appropriate data are then fulfilled by the javascript.
     * 
     * @return string HTML
     */
    function item_template()
    {
        
        $buttons = new HtmlButtons();
        $button_drop = $buttons->drop_item(0);
        $button_pickup = $buttons->pickup_item(0);
        $button_use = $buttons->use_item('{item_alias}', 0, '{item_name}');
        
        return '
            <template id="tplItem">
                <li class="item_label z-depth-1" onclick="toggleItem(event)">
                    <var class="icon">{icon}</var>
                    <div class="details hidden">
                        <a class="close" onclick="toggleItem(event)">
                            <i class="material-icons">close</i>
                        </a>
                        <var><span class="icon">{icon}</span>&nbsp;<span class="item_name">{item_name}</span></var>
                        <p class="descr_ambiance">{descr_ambiance}</p>
                        <p class="descr_purpose z-depth-1">{descr_purpose}</p>
                        <ul>
                            <li class="hidden type_booster">
                                <i class="material-icons" style="background:orange">bolt</i>
                                Cet objet donne de l\'énergie
                            </li>
                            <li class="hidden type_resource">
                                <i class="material-icons" style="background:#7FB3D5">construction</i>
                                Cet objet est une ressource
                            </li>
                            <li class="hidden type_weapon">
                                <i class="material-icons" style="background:#EC7063">sports_kabaddi</i>
                                Cet objet est une arme
                            </li>
                            <li class="hidden preciousness">
                                <i class="material-icons" style="color:#FFD700;-webkit-text-stroke: 1px darkslategrey;">star</i>
                                Cet objet est précieux
                            </li>
                            <li class="hidden heaviness">
                                <i class="material-icons">fitness_center</i>
                                Cet objet est encombrant
                            </li>
                        </ul>
                        '.$button_use . $button_drop . $button_pickup.'
                    </div>
                </li>
            </template>';
    }
    
    
    /**
     * Gives the image (PNG, JPG...) of an item or construction, or its textual icon 
     * (HTML entity or emoji), or a default icon.
     * 
     * @param string $icon_path The path to the image icon, from /resources/img/
     *                          Ex: "copyrighted/buildings/104.png"
     * @param string $icon_html The HTML entity or emoji of the icon
     *                          Ex : "&#127751;"
     * @param int $icon_size The dimension of the image in px, ex: "32"
     *                       Only one number because height = width.
     * @return string
     */
    public function icon($icon_path, $icon_html, $icon_size=32) {
        
        $size = ($icon_size !== null) ? $icon_size : 48;
    
        if($icon_path !== null and $icon_path !== '') {
            return  '<img src="../resources/img/'.$icon_path.'" class="item_icon" '
                    . 'height="'.$size.'" width="'.$size.'" alt="">';
        }
        elseif($icon_html !== null and $icon_html !== '') {
            return '<span class="item_icon">'.$icon_html.'</span>';
        }
        else {
            // The "?" emoji
            return '<span class="item_icon">&#10067;</span>';
        }
    }
    
    
    /**
     * Generates the HTML for the items in the bag, bank, ground
     * (icon, name, button to use it...)
     * 
     * @param array $bag_items List of the items in the bag (or int the ground...),
     *                         structured like this:
     *                          [item_id=>item_amount,
     *                           item_id=>item_amount
     *                           ...]
     * @param array $items_caracs All the characteristics of the items, as returned by
     *                            the API "configs"
     * @return string
     */
    function items($bag_items, $items_caracs)
    {
        
        $htmlItem = new HtmlItem();
        $result = '';
        
        foreach ($bag_items as $item_id=>$item_amount) {
            // Handles the anormal case where an item ID is not among the list of items.
            // Possible when an item on a map is not in the items set for this map.
            $item_caracs = isset($items_caracs[$item_id]) ? $items_caracs[$item_id] : set_default_variables('item', $item_id);
            // Si le citoyen possède un objet en plusieurs exemplaires, on le fait 
            // apparaître autant de fois dans le sac.
            $result .= str_repeat($htmlItem->item($item_caracs, $item_id), $item_amount);
        }
        
        return $result;
    }
    
    
    /**
     * Generates the HTML for one item
     * 
     * @param array $item_caracs All the characteristics of the item, as returned by
     *                           the API "configs"
     * @param int $item_id
     * @return string HTML
     */
    private function item($item_caracs, $item_id) {
        
        $buttons = new HtmlButtons();
        
        $button_alias = get_item_action($item_caracs);      
        $item_image = ($item_caracs['icon_path'] !== null)
                        ? '<img src="../resources/img/'.$item_caracs['icon_path'].'" alt="'.$item_caracs['icon_symbol'].'">'
                        : $item_caracs['icon_symbol'];
        $button_drop = $buttons->drop_item($item_id);
        $button_pickup = $buttons->pickup_item($item_id);
        $button_use = $buttons->use_item($button_alias, $item_id, '');
        
        return '
            <li class="item_label z-depth-1">
                <var onclick="toggleItem(event)">
                    '.$item_image.'
                </var>
                <div class="details hidden">
                    <a class="close" onclick="toggleItem(event)">
                        <i class="material-icons">close</i>
                    </a>
                    <var>'.$item_image.'&nbsp;'.$item_caracs['name'].'</var>
                    <hr class="line">
                    <p class="descr_ambiance">'.$item_caracs['descr_ambiance'].'</p>
                    <p class="descr_purpose">'.$item_caracs['descr_purpose'].'</p>
                    '.$button_use . $button_drop . $button_pickup.'
                </div>
            </li>';
    }
    
    
    /**
     * Generates an empty slot for an item.
     * Useful when the citizen's bag is not filled.
     * 
     * @param int $nbr_empty_slots Number of free slots to generate
     * @return string
     */
    function empty_slots($nbr_empty_slots)
    {
        
        $result = '';
        
        for ($i=0; $i<$nbr_empty_slots; $i++) {            
            $result.= "\n<li class=\"item_label empty_slot\"></li>\n";
        }
        
        return $result;
    }
}

