<?php
/**
 * Determines if the item is usable (can be eaten, can heal a wound...)
 * 
 * @param array $item_caracs All the charactéristics of the item, as returned by
 *                           the API "configs"
 * @return string The name of the action (if one is found). Must have been defined
 *                in the class HtmlButtons.
 */
function get_item_action($item_caracs)
{

    $button_alias = null;
    
    if ($item_caracs['ap_gain'] > 0) {
        $button_alias = 'eat';
    }
    elseif ($item_caracs['killing_rate'] > 0) {
        $button_alias = 'fight';
    }
    elseif ($item_caracs['healing_wound'] > 0) {
        $button_alias = 'heal';
    }
    
    return $button_alias;
}
