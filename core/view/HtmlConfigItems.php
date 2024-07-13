<?php
/**
 * Liste des caractéristiques des objets paramétrés dans le jeu 
 */
class HtmlConfigItems
{
    
    /**
     * HTML de la liste des composants permettant d'assembler un objet
     * 
     * @param array $items The characteristics of all the items of the game
     * @param int $item_id The ID of the item to craft
     */
    public function craft($items, $item_id)
    {

        $html_craft = '';  

        if (!isset($items[$item_id]['craftable_from'])) {
            $html_craft = '–';
        }
        else {
            foreach ($items[$item_id]['craftable_from'] as $item_id=>$amount) {
                $html_craft .= '• '.$items[$item_id]['name'].' (x'.$amount.')<br>';
            }
        }

        return $html_craft;
    }


    /**
     * HTML des caractéristiques liées aux armes
     */
    public function weapon($items_caracs)
    {

        if (!isset($items_caracs['killing_rate'])) {
            return  '?';
        }
        else {
            return '<abbr title="Probabilité que l\'arme tue un zombie">Réussite</abbr> : '
                  . $items_caracs['killing_rate'].'%<br>'
                  .'<abbr title="Probabilité que l\'arme se casse après un coup (mais réparable)">Casse</abbr> : '
                  . $items_caracs['break_rate'].'%<br>'
                  .'<abbr title="Probabilité que l\'arme disparaisse définitivement après un coup">Destruction</abbr> : '
                  . $items_caracs['destruction_rate'].'%<br>';
        }
    }


    /**
     * Objet encombrant ou pas
     */
    public function heaviness($items_caracs)
    {

        return ($items_caracs['heaviness'] === 0)
                ? '<span class="grey-text">non</span>'
                : '<strong class="red-text">oui</strong>';
    }


    /**
     * L'objet est-il un boost (nourriture, eau, drogue...)
     */
    public function boost($items_caracs, $boost_types)
    {

        return $boost_types[$items_caracs['boost_type']];
    }
    
    
    public function health($items_caracs)
    {
        
        $result = '';
        $result .= ($items_caracs['healing_wound'] === 1) ? 'Soigne blessure<br>' : '';
        $result .= ($items_caracs['healing_infection'] === 1) ? 'Soigne infection<br>' : '';
        
        return $result;
    }
    
    
    public function tags($items_caracs)
    {
        
        // Temporarily hardcoding the names of the tags. Should replace this 
        // by an API returning the names of the tags.
        $tags_names = [
            1 => 'Boost',
            2 => 'Nourriture',
            3 => 'Drogue',
            4 => 'Alcool',
            5 => 'Ennemi',
        ];
        
        $result = '';
        if(isset($items_caracs['tags'])) {
            foreach($items_caracs['tags'] as $tag) {
                $result .= ' <div class="chip">'.$tags_names[$tag].'</div>';
            }
        }
        
        return $result;
    }
}
