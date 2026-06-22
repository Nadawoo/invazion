<?php
/**
 * Determines if the item is usable (can be eaten, can heal a wound...)
 * 
 * @param array $item_caracs All the characteristics of the item, as returned by
 *                           the API "configs"
 * @return string The name of the action (if one is found). Must have been defined
 *                in the class HtmlButtons.
 */
function get_item_action($item_caracs)
{

    $button_alias = null;
    
    if (isset($item_caracs['triggers']) and in_array('eat', $item_caracs['triggers'])) {
        $button_alias = 'eat';
    }
    elseif ($item_caracs['item_type'] === "weapon") {
        $button_alias = 'fight';
    }
    elseif ($item_caracs['healing_wound'] > 0) {
        $button_alias = 'heal';
    }
    elseif ($item_caracs['items_inside_min'] > 0) {
        $button_alias = 'open';
    }
    
    return $button_alias;
}
