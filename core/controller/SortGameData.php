<?php
/**
 * Methods to sort or filter the data returned by the APIs
 */
class SortGameData
{
    
    /**
     * Sort citizens by their location on the map instead of their id
     * 
     * @param array $citizens   The citizens data, as returned by the "citizens" API
     * 
     * @return array The citizens data index by coordinates
     *              [0_2] => [
     *                  [0] => [data of a citizen],
     *                  [1] => [data of another citizen],
     *                  ...
     *                  ],
     *              [0_3] => ...
     */
    function sort_citizens_by_coord($citizens)
    {

        $citizens_by_coord = [];

        foreach ($citizens as $val) {

            $coords = $val['coord_x'].'_'.$val['coord_y'];
            $citizens_by_coord[$coords][] = $val;
        }

        return $citizens_by_coord;
    }
    
    
    /**
     * Starting from a main city, gets of the citizens of all the habitations
     * linked to this city.
     * 
     * @param int $child_cities_ids The IDs of the habitations linked to the main city.
     * @param array $cities_data   The characteristics of the cities 
     *                             (coming from the API "cities")
     * @param array $citizens_data The characteristics of the citizens
     *                             (coming from the API "citizens")
     * @return array
     */
    function get_child_citizens($child_cities_ids, $cities_data, $citizens_data) {
        
        $city_fellows = [];
        
        foreach($child_cities_ids as $child_id) {
            $citizens_ids = $cities_data[$child_id]['citizens_ids'];
            if(!empty($citizens_ids)) {
                $city_fellows[$citizens_ids[0]] = $citizens_data[$citizens_ids[0]];
            }
        }
        
        return $city_fellows;
    }
    
    
    /**
     * Filters a list of citizens coming from the API "citizens"
     * to keep only the ones belonging to a specific city.
     * 
     * @param  array $citizens  Array contenant les citoyens de toute la carte,
     *                          issu de l'API map.php
     * @param  int   $city_id   L'ID de la ville dont on veut garder les citoyens
     * @return array Array ne contenant que les citoyens de la ville demandée
     */
    function filter_citizens_by_city($citizens, $city_id)
    {
        return  array_filter($citizens, 
                    function($a) use($city_id) {
                        return $a['city_id'] === $city_id;
                    }
                );
    }
    
    
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
}
