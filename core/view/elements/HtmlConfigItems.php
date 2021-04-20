<?php
/**
 * Liste des caractéristiques des objets paramétrés dans le jeu 
 */
class HtmlConfigItems
{
    
    /**
     * HTML de la liste des composants permettant d'assembler un objet
     * 
     * @param $items_caracs Liste des objets telle que définie dans le fichier de config
     */
    public function craft($items_caracs)
    {

        $html_craft = '';  

        if (!isset($items_caracs['craftable_from'])) {

            $html_craft = '–';
        }
        else {

            foreach ($items_caracs['craftable_from'] as $item_id=>$amount) {
                $html_craft .= '• item #'.$item_id.' (x'.$amount.')<br>';
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

        if (!isset($items_caracs['heaviness'])) {

            return  'non';
        }
        else {
            return '<span class="red">oui</span>';
        }
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
