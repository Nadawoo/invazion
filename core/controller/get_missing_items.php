<?php

/**
* Gives the list of the missing items required to do something.
* Useful to know how many resources we need to add in the city storage
* to build a construction.
* 
* @param array $items_needed The list of items required to do the action
*                            [item ID => required amount, item ID => required amount, ...]
* @param array $items_available The list of items available to do the action
*                            [item ID => available amount, item ID => available amount, ...]
* @return array A list giving the missing amount for each item
*               [item ID => missing amount, item ID => missing amount, ...]
*               The amount is 0 when all the required amount is available.
*/
function get_missing_items($items_needed, $items_available) {

   $items_missing = [];

   // TODO: this naive foreach could be avoided by using array_map(), but 
   // array_map() doesn't preserves the keys (item ID). Try to improve this:
   //      $items_missing = array_map(function($nbr_items_needed, $nbr_items_available) {
   //                  return max(0, $nbr_items_needed-$nbr_items_available);
   //              },
   //              $items_needed, $items_available);
   foreach($items_needed as $item_id=>$nbr_item_needed) {

       $items_missing[$item_id] = (isset($items_available[$item_id])) 
           ? $items_missing[$item_id] = max(0, $nbr_item_needed-$items_available[$item_id])
           : $nbr_item_needed;
   }

   return $items_missing;
}
