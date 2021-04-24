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

        if (!isset($items_caracs['weapon'])) {

            return  '–';
        }
        else {
            $weapon = $items_caracs['weapon'];
            return 'Tuer zombie : '.$weapon['killing_rate'].'%<br>'
                  .'Casse :       '.$weapon['break_rate'].'%<br>'
                  .'Disparition : '.$weapon['destruction_rate'].'%<br>';
        }
    }


    /**
     * Objet encombrant ou pas
     */
    public function heaviness($items_caracs)
    {

        return ($items_caracs['heaviness'] === 0) ? 'non' : '<span class="red">oui</span>';
    }


    /**
     * L'objet est-il un boost (nourriture, eau, drogue...)
     */
    public function boost($items_caracs)
    {

        if (!isset($items_caracs['boost'])) {

            return  '–';
        }
        else {
            return $items_caracs['boost'];
        }
    }
}
