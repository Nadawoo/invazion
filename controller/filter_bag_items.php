<?php
/**
 * Filtre la liste des objets du sac pour ne garder que ceux ayant une caractéristique donnée.
 * 
 * @param array $carac_filter   Nom de la caractéristique que l'objet doit avoir
 *                              Ex : healing_wound
 * @param array $game_items     Caractéristiques de tous les objets du jeu, telles que
 *                              fournies par l'API 'configs'
 * @param array $bag_items  Les objets que le joueur a dans son sac, 
 *                          sous la forme [id_objet => quantité]
 * @return array Liste ne contenant que les objets ayant la caractéristique demandée.
 */
function filter_bag_items($carac_filter, $game_items, $bag_items)
{
    
    $filtered_items = [];
    
    foreach ($bag_items as $item_id=>$amount) {
    
        if ($game_items[$item_id][$carac_filter] !== 0) {
            $filtered_items[$item_id] = $game_items[$item_id];
        }
    }
    
    return $filtered_items;
}
