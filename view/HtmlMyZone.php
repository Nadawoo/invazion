<?php

/**
 * Génère le HTML de la case sur laquelle le citoyen se trouve
 * (en grand format par-dessus la carte)
 */
class HtmlMyzone
{
    
    private $_citizens_in_zone  = [];
    private $_citizens_in_city  = [];
    private $_nbr_zombies = 0;
    private $_nbr_items   = 0;
    private $_city_size   = 0;
    
    
    function set_nbr_zombies($nbr_zombies)
    {
        
        $this->_nbr_zombies = $nbr_zombies;
    }
    
    function set_nbr_items($items_list)
    {
        
        $this->_nbr_items = array_sum( ($items_list == null) ? [] : $items_list );
    }
    
    function set_citizens_in_zone($citizens)
    {
        
        $this->_citizens_in_zone = $citizens;
    }
    
    function set_citizens_in_city($citizens_in_city)
    {
        
        $this->_citizens_in_city = $citizens_in_city;
    }

    function set_city_size($city_size)
    {
        
        $this->_city_size = $city_size;
    }
    
    function set_citizen_pseudo($pseudo)
    {
        
        $this->_citizen_pseudo = $pseudo;
    }

    /**
     * Affiche la case où se trouve le joueur, en grand par-dessus la carte
     * 
     * @return string HTML
     */
    function main()
    {
        
        $buttons = new HtmlButtons();
        
        // S'il y a une ville sur la carte
        // NB : 2 car une "ville" de 1 place est en réalité une tente.
        if ($this->_city_size >= 2) {
            
            $hexagon_content = '
                <div id="city_name">Ville anonyme</div>
                <div id="city_descr">0 défenses | '.$this->_city_size.' habitations</div>
                <div id="city_enclosure">
                    <div id="city_icon">&#127751;</div>
                    <div id="citizens_in_city">'.$this->citizens_in_city().'</div>
                    <div id="constructions_slots">
                        '.str_repeat('<div class="construction_slot">&nbsp;</div>', 5).'
                    </div>
                    <div class="city_gates">&#128679;&#128679;</div>
                </div>
                '.$buttons->button('enter_city').'
                <div style="margin-top:1em">&#128100;<br>'.$this->_citizen_pseudo.'</div>️';
        }
        else {
            
            // S'il y a une tente, on l'affiche à la place des objets au sol
            $top_container = ($this->_city_size === 0) ? $this->block_items() : $this->block_tent();
            
            $hexagon_content = 
                $top_container .
                '<div id="container_citizens">
                    '.$this->citizens_in_zone($this->_citizens_in_zone).'
                </div>
                <div id="container_zombies">
                    '.$this->zombies().'
                </div>
                <div id="button_kill">'.$buttons->kill_zombie($this->_nbr_zombies).'</div>';
        }
        
        return '
            <div id="back_to_map" onclick="toggle(\'my_zone\');setCookie(\'show_zone\', 1)">
                Afficher ma&nbsp;zone
            </div>
            <div id="my_zone">
                <span id="back_to_map" onclick="toggle(\'my_zone\');setCookie(\'show_zone\', 0)">
                    Afficher la&nbsp;carte
                </span>
                <div id="hexagon">&#x2B22;</div>
                <div id="zone_container">'.$hexagon_content.'</div>
            </div>';
    }
    
    
    /**
     * Bloc affichant la tente dans la zone
     * 
     * @return string HTML
     */
    function block_tent()
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div style="font-size:2.5em">&#9978;</div>
            <div style="font-size:0.8em;font-style:italic">Une tente abri de fortune a été plantée ici.</div>
            '. $buttons->button('enter_city')
             . $buttons->button('attack_tent', 'no_icon');
    }
    
    
    /**
     * Bloc affichant les objets au sol dans la zone
     * 
     * @return string HTML
     */
    function block_items()
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div id="button_pickup">
                <input type="submit" value="Ramasser..." onclick="toggleItemsPanel()">
            </div>
            <div id="button_dig">'.$buttons->button('dig', 'no_icon').'</div>
            <div id="items" onclick="toggleItemsPanel()">
                '.$this->items().'
            </div>';
    }
    
    /**
     * Bloc affichant les zombies de la zone
     * 
     * @return string HTML
     */
    function zombies()
    {
        
        // On n'affiche pas plus de X zombies pour ne pas déborder
        $max_lines          = 5;
        $zombies_per_line   = 5;
        $max_zombies        = $max_lines*$zombies_per_line;
        $nbr_hidden_zombies = max(0, $this->_nbr_zombies-$max_zombies);
        
        $nbr_full_lines = min($max_lines, floor($this->_nbr_zombies/$zombies_per_line));
        $zombies_used   = 0;
        $html_zombies   = '';
        
        for ($line=0; $line<=$nbr_full_lines and $this->_nbr_zombies>0 and $zombies_used<$max_zombies; $line++) {
            
            // Détermine le nombre de zombies dans la ligne
            $repeat = ($line<$nbr_full_lines) ? $zombies_per_line : $this->_nbr_zombies-$zombies_used;
            
            $html_zombies .=  '<div class="zombie_line">'
                            . '<span style="color:red">◄</span>️ '
                            . str_repeat('<span class="zombie">&#129503;</span>', $repeat)
                            . '</div>';
            
            $zombies_used += $repeat;
        }
        
        $html_hidden_zombies =  ($nbr_hidden_zombies > 0) 
                                ? '<div id="nbr_zombies">+'.$nbr_hidden_zombies.' zombies...</div>'
                                : '';
        
        // Texte par défaut si 0 zombies dans la zone
        if ($html_zombies === '') {
            
            $html_zombies = '<div style="font-size:0.9em;color:maroon">Aucun zombie<br>Zone sécurisée<br><br><br></div>';
        }
        
        return  '<div id="zombies">
                '.$html_zombies.'
                </div>
                '.$html_hidden_zombies;
    }
    
    
    /**
     * Liste des objets au sol
     * 
     * @return string HTML
     */
    function items()
    {
        
        if ($this->_nbr_items === 0) {
            
            return '<div style="color:maroon;text-align:center;margin-top:2.2em;font-size:0.9em">Aucun objet au&nbsp;sol</div>';
        }
        
        return str_repeat('<span>&#128188;</span>', $this->_nbr_items);
    }
    
    
    /**
     * Bloc affichant les citoyens de la zone (mais pas en ville)
     * 
     * @param array $citizens Liste des pseudos des citoyens de la case
     * @return string
     */
    function citizens_in_zone($citizens)
    {
        
        $nbr_citizens = count($this->_citizens_in_zone);
        
        // On part du principe que tous les citoyens ont 
        // le même nombre de points de contrôle pour l'instant
        $individual_controlpoints = $citizens[array_keys($citizens)[0]]['control_points'];
        
        // On n'affiche pas plus de X citoyens pour ne pas déborder
        $max_lines              = 5;
        $human_controlpoints    = $individual_controlpoints * $nbr_citizens;
        $reinforcement_needed   = '';
        $citizens_list          = '';
        $count                  = 0;
        
        $is_controlled_by_humans = ($human_controlpoints >= $this->_nbr_zombies) ? true : false;
        
        // Si la zone est sous contrôle zombie, on affichera 1 citoyen de moins
        // pour laisser la place à la case "Renfort requis"
        $max_citizens = ($is_controlled_by_humans === false) ? $max_lines-1 : $max_lines;
        
        // Liste des citoyens présents dans la zone
        for ($ctz=current($citizens); $count<$nbr_citizens and $count<$max_citizens; $ctz=next($citizens)) {
            
            $citizens_list .= '<div style="display:flex;flex-grow:1;">'
                    . '<span class="citizen">&#128100;'.$ctz['citizen_pseudo'].'</span>️'
                    . '&#128737;&#65039;️'
                    . "</div>\n";
            
            $count++;
        }
        
        // Cases des citoyens manquants si zombies trop nombreux
        $nbr_repeat = min(($max_lines-$max_citizens),
                          ceil(($this->_nbr_zombies-$human_controlpoints)/$individual_controlpoints)
                          );
        if ($is_controlled_by_humans === false) {
            
            $reinforcement_needed = str_repeat(
                     '<div style="display:flex;flex-grow:1">'
                   . '    <span class="reinforcement">&nbsp;&#8252;&nbsp;Renfort requis</span>'
                   . '    <span style="opacity:0.3">&#128737;&#65039;️</span>'
                   . '</div>',
                   $nbr_repeat);
        }
        
        $nbr_hidden_citizens = max(0, $nbr_citizens-$max_citizens);
        
        $html_hidden_citizens = ($nbr_hidden_citizens > 0)
                                ? '<div id="nbr_citizens">+'.$nbr_hidden_citizens.' humains...</div>'
                                : '';
        
        return '<div id="citizens">'.$citizens_list . $reinforcement_needed.'</div>
               '.$html_hidden_citizens;
    }
    
    
    /**
     * Affiche les pseudos des citoyens à l'intérieur de la ville sur la case
     * 
     * @return string HTML
     */
    function citizens_in_city()
    {
        
        $html_citizens = '';

        foreach ($this->_citizens_in_city as $citizen) {

            $html_citizens .= '<span class="citizen" title="'.$citizen['citizen_pseudo'].' est en ville">'
                    . substr($citizen['citizen_pseudo'], 0, 2)
                    .'</span> ';
        }
        
        return $html_citizens;
    }
}
