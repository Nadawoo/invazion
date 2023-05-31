<?php

/**
 * Generates the HTML for the items in the bag, bank, ground
 * (icon, name, button to use it...)
 */
class HtmlItem {
    
    
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
            
            // Si le citoyen possède un objet en plusieurs exemplaires, on le fait 
            // apparaître autant de fois dans le sac.
            while ($item_amount > 0) {                
                $result .= $htmlItem->item($items_caracs[$item_id], $item_id);
                $item_amount--;
            }
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
        $item_image = '<img src="../resources/img/'.$item_caracs['icon_path'].'" alt="'.$item_caracs['icon_symbol'].'">';
        $button_drop = $buttons->drop_item($item_id);
        $button_pickup = $buttons->pickup_item($item_id);
        $button_use = $buttons->use_item($button_alias, $item_id, $item_caracs['name']);
        
        return '
            <li class="item_label">
                <var id="iconItem'.$item_id.'"
                    style="height:3em;width:3em;justify-content:center;"
                     onclick="display(\'detailsItem'.$item_id.'\')">
                    '.$item_image.'
                </var>
                <div id="detailsItem'.$item_id.'" class="details">
                    <span class="close" onclick="hide(\'detailsItem'.$item_id.'\')">&#x274C;</span>
                    <var style="font-weight:bold">'.$item_image.'&nbsp;'.$item_caracs['name'].'</var>
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
            $result.= "\n<li class=\"empty_slot\"></li>\n";
        }
        
        return $result;
    }
}

