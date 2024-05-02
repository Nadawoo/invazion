<?php

/**
 * Create action buttons for the HTML interface of the game
 * (dig in the area, attack a zombie ...)
 */
class HtmlButtons
{
    
    
    function __construct()
    {
        
        // Visible labels and icons for the buttons.
        // The "fields" key contains the parameters to call the API (in hidden fields)
        $this->buttons = [
            'dig' => [
                'icon'  => '&#x26CF;&#xFE0F;',
                'name'  => 'Fouiller la zone',
                'title' => '',
                'fields' => [
                    'api_name'      => 'zone',
                    'action'        => 'dig'
                    ],
                ],
            'eat' => [
                'icon'  => '',
                'name'  => 'Consommer',
                'title' => "",
                'fields' => [
                    'api_name'      => 'items',
                    'action'        => 'eat'
                    ]
                ],
            'fight' => [
                'icon'  => '',
                'name'  => 'Attaquer avec',
                'title' => "",
                'fields' => [
                    'api_name'      => 'zone',
                    'action'        => 'fight'
                    ],
                ],
            'heal' => [
                'icon'  => '',
                'name'  => 'Me soigner avec',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'heal'
                    ],
                ],
            'open' => [
                'icon'  => '',
                'name'  => 'Ouvrir',
                'title' => "",
                'fields' => [
                    'api_name'      => 'items',
                    'action'        => 'open'
                    ],
                ],
            'add_vault' => [
                'icon'  => '&#9961;&#65039;',
                'name'  => 'Chercher une crypte',
                'title' => "Trouver une crypte peut servir vos intérêts mais aussi causer votre perte... ou celle de vos amis.",
                'fields' => [
                    'api_name'      => 'zone',
                    'action'        => 'add',
                    'params[stuff]' => 'vault'
                    ],
                ],
            'add_map_zombies' => [
                'icon'  => '',
                'name'  => 'Ajouter des zombies sur toute la carte',
                'title' => "",
                'fields' => [
                    'api_name'      => 'zone',
                    'action'        => 'add',
                    'params[stuff]' => 'zombies',
                    ],
                ],
            'add_mass_zombies' => [
                'icon'  => '',
                'name'  => 'Ajouter des zombies sur toute la carte',
                'title' => "Bouton spécial béta-test",
                'fields' => [
                    'api_name'           => 'zone',
                    'action'             => 'add',
                    'params[stuff]'      => 'zombies',
                    'params[conditions]' => 'noconditions'
                    ],
                ],
            'end_cycle' => [
                'icon'  => '',
                'name'  => 'Déclencher l\'attaque !
                            <span style="display:flex;align-items:center;color:lightgrey">
                                &#x1F9DF; <span class="material-icons">chevron_right</span>
                                <span class="material-icons">chevron_right</span>
                                <span class="material-icons">chevron_right</span>
                                <span class="material-icons">chevron_right</span>
                                <span class="material-icons">chevron_right</span>
                                &#x1F307;
                                <!--
                                &#x1F304; <span class="material-icons">chevron_right</span>
                                &#x2600;&#xFE0F; <span class="material-icons">chevron_right</span>
                                &#x1F307; <span class="material-icons">chevron_right</span>
                                &#x1F9DF;
                                -->
                            </span>',
                'title' => "Met fin au cycle en cours et déclenche l'attaque zombie quotidienne.",
                'fields' => [
                    'api_name'           => 'events',
                    'action'             => 'endcycle',
                    ],
                ],
            'reveal_zones' => [
                'icon'  => '',
                'name'  => 'Dévoiler 10 zones de la carte',
                'title' => "",
                'fields' => [
                    'api_name'          => 'zone',
                    'action'            => 'reveal',
                    'params[stuff]'     => 'random7',
                    ],
                ],
            'enter_city' => [
                'icon'  => '&#x1F5DD;&#xFE0F;',
                'name'  => 'Entrer',
                'title' => "",
                'fields' => [
                    'api_name'          => 'city',
                    'action'            => 'go_inout'
                    ],
                ],
            'destroy_city' => [
                'icon'  => '&#10060;',
                'name'  => 'Détruire',
                'title' => "Un citoyen a planté sa tente ici. Vous avez l'opportunité de la détruire...",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'attack'
                    ],
                ],
            'attack_citizen' => [
                'icon'  => '&#128074;&#127995;',
                'name'  => 'Agresser !',
                'title' => "",
                ],
            'heal_citizen' => [
                'icon'  => '&#x1F489;',
                'name'  => 'Soigner !',
                'title' => "",
                ],
            'kill_zombie' => [
                'icon'  => '&#x1F91C;&#x1F3FC;',
                'name'  => 'Attaquer à mains nues !',
                'title' => "Attaquer un zombie à mains nues. Vous gagnerez un picto en cas de succès.",
                'fields' => [
                    'api_name' => 'zone',
                    'action'   => 'fight',
                    ],
                ],            
            'kill_mass_zombies' => [
                'icon'  => '&#x1F525;',
                'name'  => 'Nettoyer au lance-flammes',
                'title' => "Comme les zombies sont particulièrement nombreux ici, vous pouvez "
                         . "les attaquer par groupe. C'est très efficace, mais en contrepartie "
                         . "vous ne gagnerez aucun picto.",
                'fields' => [
                    'api_name' => 'zone',
                    'action'   => 'bigfight',
                    ],
                ],
            'build_tent' => [
                'icon'  => '&#9978;',
                'name'  => 'Planter ma tente',
                'title' => "Une tente vous protègerait de la rigueur du désert.",
                'fields' => [
                    'api_name'          => 'city',
                    'action'            => 'build',
                    'params[city_type_id]' => 13,
                    ],
                ],
            'build_city' => [
                'icon'  => '&#x1F307;',
                'name'  => 'Fonder une ville',
                'title' => "En vous rassemblant avec d'autres citoyens dans une ville, vous serez plus forts.",
                'fields' => [
                    'api_name'          => 'city',
                    'action'            => 'build',
                    'params[city_type_id]' => 12,
                    ],
                ],
            'build_outpost' => [
                'icon'  => '',
                'name'  => 'Bâtir avant-poste',
                'title' => "Les avant-postes fournissent ressources et protection lors des explorations.",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'build',
                    'params[city_type_id]' => 11,
                    ],
                ],
            'connect_tent' => [
                'icon'  => '',
                'name'  => 'Connecter ma maison à la ville',
                'title' => "",
                'fields' => [
                    'api_name'  => 'city',
                    'action'    => 'connect'
                    ],
                ],
            'get_out_city' => [
                'icon'  => '',
                'name'  => 'Sortir de la ville',
                'title' => "Dans les villes, vous êtes protégé des zombies... provisoirement.",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'go_inout'
                    ],
                ],
            'get_out_home' => [
                'icon'  => '',
                'name'  => 'Explorer le désert',
                'title' => "",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'go_inout'
                    ],
                ],
            'dig_path' => [
                'icon'  => '',
                'name'  => "&#x26CF;&#xFE0F; Fouiller",
                'title' => "Fouiller la zone où se trouve l'expédition (FONCTION A PROGRAMMER)",
                'fields' => [
                    'api_name'      => 'paths',
                    'action'        => ''
                    ],
                ],
            'move_path' => [
                'icon'  => '',
                'name'  => "&#x25B6;&#xFE0F; Avancer",
                'title' => "Faire avancer l'expédition vers l'étape suivante",
                'fields' => [
                    'api_name'      => 'paths',
                    'action'        => 'move'
                    ],
                ],
            'open_door' => [
                'icon'  => '',
                'name'  => 'Ouvrir les portes !',
                'title' => "",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'open_door'
                    ],
                ],
            'close_door' => [
                'icon'  => '',
                'name'  => 'Fermer les portes !',
                'title' => "",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'close_door'
                    ],
                ],
            'repel_zombie' => [
                'icon'  => '&#x1F4A8;',
                'name'  => 'Chasser un zombie',
                'title' => "Repousser un zombie vers une case voisine",
                'fields' => [
                    'api_name'      => 'zone',
                    'action'        => 'repel'
                    ],
                ],
            'specialize_digger' => [
                'icon'  => '',
                'name'  => 'Fouineur',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'digger',
                    ],
                ],
            'specialize_explorer' => [
                'icon'  => '',
                'name'  => 'Explorateur',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'explorer',
                    ],
                ],
            'specialize_builder' => [
                'icon'  => '',
                'name'  => 'Bâtisseur',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'builder',
                    ],
                ],
            'specialize_weak' => [
                'icon'  => '',
                'name'  => 'Ordinaire',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'weak',
                    ],
                ],
            'specialize_citizens_5' => [
                'icon'  => '',
                'name'  => 'Groupe de 5 citoyens',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'citizens_5',
                    ],
                ],
            'specialize_zombie_cryptic' => [
                'icon'  => '',
                'name'  => 'Zombie cryptique',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'zombie_cryptic',
                    ],
                ],
            'specialize_zombie_excavator' => [
                'icon'  => '',
                'name'  => 'Zombie excavateur',
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'specialize',
                    'params[type]'  => 'zombie_excavator',
                    ],
                ],
            'start_game' => [
                'icon'  => '',
                'name'  => "Nouvel essai (réincarnation)",
                'title' => "Essayez à nouveau de survivre dans la même partie",
                'fields' => [
                    'api_name'      => 'events',
                    'action'        => 'startgame',
                    ],
                ],
            'switch_citizen' => [
                'icon'  => '',
                'name'  => "Contrôler ce citoyen",
                'title' => "Vous pouvez prendre le contrôle de certains citoyens\npour mener des expéditions dans le désert.",
                'fields' => [
                    'api_name'          => 'me',
                    'action'            => 'switch_citizen',
                    ],
                ],
            'upgrade_camouflage' => [
                'icon'  => '',
                'name'  => "Camouflage +1 niveau",
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'upgrade_capacity',
                    'params[type]'  => 'camouflage',
                    ],
                ],
            'upgrade_vision' => [
                'icon'  => '',
                'name'  => "Vision +1 niveau",
                'title' => "",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'upgrade_capacity',
                    'params[type]'  => 'vision',
                    ],
                ],
            'validate_death' => [
                'icon'  => '',
                'name'  => 'Mort sans réincarnation',
                'title' => "Valide votre mort sans lancer de nouvelle partie",
                'fields' => [
                    'api_name'      => 'me',
                    'action'        => 'validate_death',
                    ],
                ],
            'well_drop' => [
                'icon'  => '',
                'name'  => 'Ajouter 1 ration d\'eau',
                'title' => "",
                'fields' => [
                    'api_name'      => 'items',
                    'action'        => 'drop',
                    'params[item_id]' => 9 // ID of the water ration
                    ],
                ],
            'well_pickup' => [
                'icon'  => '',
                'name'  => 'Prendre 1 ration d\'eau',
                'title' => "",
                'fields' => [
                    'api_name'      => 'items',
                    'action'        => 'pickup',
                    'params[item_id]' => 9 // ID of the water ration
                    ],
                ],
            'win_game' => [
                'icon'  => '',
                'name'  => "Monter dans la voiture !",
                'title' => "",
                'fields' => [
                    'api_name'      => 'events',
                    'action'        => 'wingame',
                    ],
                ],
            'explore_building' => [
                'icon'  => '',
                'name'  => "Explorer le bâtiment",
                'title' => "",
                'fields' => [
                    'api_name'      => 'city',
                    'action'        => 'explore',
                    ],
                ],
        ];
    }
    
    
    /**
     * Generates a generic action button, ie :
     * - destinated to send data to a game API
     * - no variable parameter
     * - result displayed in pop-up (#popsucess)
     * 
     * @param string $button_alias  The alias of the action button. Must exist in $this->buttons
     * @param bool   $show_icon Set at 'no_icon' to display the button without its icon.
     *                          Any other value will display the icon.
     * @param string $class     Set to "formlink" to display the button like an simple link
     * @param bool   $is_active If set to "false", the button will be grayed out
     * 
     * @return string HTML
     */
    function button($button_alias, $show_icon=true, $class='', $is_active=true)
    {
        
        $button = $this->buttons[$button_alias];
        $icon   = ($show_icon !== true) ? '' : $button['icon'].'&nbsp;';
        $class_inactive = ($is_active !== true) ? 'inactive' : '';
        
        // Generates the hidden fields for the HTML form
        $hidden_fields = '';
        foreach ($button['fields'] as $fieldname=>$fieldval) {
            
            $hidden_fields.= '<input type="hidden" name="'.$fieldname.'" value="'.$fieldval.'">';
        }
        
        // Returns the complete HTML form
        return
        '<form method="post" action="#popsuccess" name="'.$button_alias.'" class="'.$class.'">
            '.$hidden_fields.'
            <button type="submit" class="redbutton '.$class_inactive.'" title="'.$button['title'].'">'.$icon.$button['name'].'</button>'
        .'</form>';
    }
    
    
    /**
     * Generates a big round button with icon for the main actions (digging...)
     * 
     * @param string $button_alias  The alias of the action button. Must exist in $this->buttons
     * @param int    $amount A number to display in a small pastille beside the button
     * @param bool   $is_active If set to "false", the button will be grayed out
     * @return string HTML
     */
    function button_round($button_alias, $amount=0, $is_active=true)
    {
        
        $icons = [
            'move' => [
                'icon'  => '&#x1F4A0;',
                'label' => 'bouger',
                'alert' => '&#x26A0;&#xFE0F;',
                ],
            'dig' => [
                'icon'  => '&#x26CF;&#xFE0F;',
                'label' => 'fouiller',
                ],
            'zombies' => [
                'icon'  => '&#x1F9DF;',
                'label' => 'zombies',
                ],
            'citizens' => [
                'icon'  => '&#x1F465;',
                'label' => 'humains',
                'min_amount' => 2,
                ],
            'build' => [
                'icon'  => '&#x1F3DA;&#xFE0F;',
                'label' => 'construire',
                ],
        ];
                
        $button = $icons[$button_alias];
        // Minimal amount required to display the number over the button.
        // Useful for the number of citizens in zone (must not notify "1" while 
        // the player is the only human there)
        $min_amount = (isset($button['min_amount'])) ? $button['min_amount'] : 1;
        $dot_number = '';
        
        if ($amount >= $min_amount and isset($button['alert'])) {
             $dot_number = '<div class="alert_icon">'.$button['alert'].'</div>';
        } 
        elseif ($amount >= 0) {
            $dot_number = '<div class="dot_number z-depth-2">'.$amount.'</div>';
        }
        
        $class_inactive = ($is_active !== true) ? 'inactive' : '';
        
        return
        '<div class="round_action_block z-depth-3" id="round_'.$button_alias.'">'
            .'<div id="tuto_button_'.$button_alias.'">'
                .'<input type="submit" class="round_action '.$class_inactive.'" value="'.$button['icon'].'" '
                    . 'onclick="toggleActionBlock(\''.$button_alias.'\'); updateBlockAction(\''.$button_alias.'\')">'
            .'</div>'
            . $dot_number 
            . '<label>'.$button['label'].'</label>'
        . '</div>';
    }
    
    
    /**
     * Gets the icon for a button. Useful when icon and button are separated by HTML
     * (e.g. to display the icon in a table cell and the button in another)
     * 
     * @param string $button_alias  The alias of the action button. Must exist in $this->buttons
     * 
     * @return string The unicode for the icon
     */
    function icon($button_alias)
    {
        
        return $this->buttons[$button_alias]['icon'];
    }
    
    
    /**
     * Generates a button to use an item (drink water, heal with bandage...)
     * Distinct from the standard method $this->button because :
     * - the content of the field item_id is variable
     * - the name of the button contains a variable element (the name of the item)
     * 
     * @param string $button_alias  The alias of the action button. Must exist in $this->buttons
     * @param int    $item_id   The ID of the item to use (ex : 501)
     * @param string $item_name The name of the item to display (ex : "Bandage")
     * 
     * @return string HTML
     */
    function use_item($button_alias, $item_id, $item_name)
    {
        
        if(isset($this->buttons[$button_alias])) {
            
            $button = $this->buttons[$button_alias];
            $fields = $button['fields'];

            return
            '<form method="post" action="#popsuccess">
                <input type="hidden" name="api_name" value="'.$fields['api_name'].'">
                <input type="hidden" name="action" value="'.$fields['action'].'">
                <input type="hidden" name="params[item_id]" value="'.$item_id.'">
                <button type="submit" class="redbutton">'.$button['name'].' '.$item_name.'</button>
            </form>';
        }
    }
    
    
    function drop_item($item_id)
    {
        
        return '
            <form class="form_drop" method="post" action="#Outside">
                <input type="hidden" name="api_name" value="zone">
                <input type="hidden" name="action" value="drop">
                <button type="submit" name="params[item_id]" value="'.$item_id.'" class="redbutton">&veeeq; Déposer</button>
            </form>';
    }
    
    
    function pickup_item($item_id)
    {
        
        return '
            <form class="form_pickup" method="post" action="#Outside">
                <input type="hidden" name="api_name" value="zone">
                <input type="hidden" name="action" value="pickup">
                <button type="submit" name="params[item_id]" value="'.$item_id.'" class="redbutton">&wedgeq; Prendre</button>
            </form>';
    }
    
    
    /**
     * Retourne le bouton pour contruire une ville
     * 
     * @return string
     */
    function build_city($show_icon=true)
    {
        
        $button = $this->buttons['build_city'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'];
        
        // Parameters to send to the API
        $hidden_fields = '';
        foreach ($button['fields'] as $fieldname=>$fieldval) {            
            $hidden_fields.= '<input type="hidden" name="'.$fieldname.'" value="'.$fieldval.'">';
        }
        
        return
        '<form method="post" action="#popsuccess">
            '.$hidden_fields.'
            '.$icon.'<input type="submit" class="redbutton" value="'.$button['name'].'" title="'.$button['title'].'">
            de
            <select name="params[city_size]" class="browser-default">
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
            </select>
            citoyens
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour attaquer un zombie
     * 
     * @param int $nbr_zombies  Le nombre de zombies dans la zone
     * @param string $button_alias
     * @param int $ap_cost Amount of action points required to do the action
     * @return string
     */
    function kill_zombies($nbr_zombies, $button_alias='kill_zombie', $ap_cost=0)
    {
        
        $button = $this->buttons[$button_alias];
        $title = $button['title'];
        
        $text_nbr_ap = ($ap_cost > 0) ? ' [-'.$ap_cost.'&#x26A1;]' : '';
        
        if ($nbr_zombies === 0) {
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside" onclick="killZombies(\''.$button['fields']['action'].'\'); return false;">
            <input type="submit" value="'.$button['icon'].' '.$button['name'] . $text_nbr_ap.'" 
                   class="redbutton"  title="'.$title.'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour agresser un citoyen
     * 
     * @param int    $target_id     L'id du citoyen à agresser
     * @param string $target_pseudo Le nom du citoyen à agresser
     * @return string
     */
    function attack_citizen($target_id, $target_pseudo, $show_icon=true)
    {
        
        $button = $this->buttons['attack_citizen'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].' ';
        
        return'
        <form name=attack method="post" action="#popsuccess" style="display:inline">
            <input type="hidden" name="api_name" value="me">
            <input type="hidden" name="action" value="attack">
            <input type="hidden" name="params[target_id]" value="'.$target_id.'">
            <input type="submit" value="'.$icon.$button['name'].'" title="'.$button['title'].'" style="min-width:auto">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour soigner un citoyen
     * 
     * @param int    $target_id     L'id du citoyen à soigner
     * @param int    $item_id       L'id de l'objet à utiliser pour se soigner (bandage...)
     * @return string
     */
    function heal_citizen($target_id, $item_id, $show_icon=true, $text='default')
    {
        
        $button = $this->buttons['heal_citizen'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].' ';
        $text = ($text !== 'default') ? $text : $button['name']; 
        
        return '
        <form name="heal" method="post" action="#popsuccess" style="display:inline">
            <input type="hidden" name="api_name" value="me">
            <input type="hidden" name="action" value="heal">
            <input type="hidden" name="params[item_id]" value="'.$item_id.'">
            <input type="hidden" name="params[target_id]" value="'.$target_id.'">
            <input type="submit" value="'.$icon.$text.'" title="'.$button['title'].'" style="min-width:auto">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour assembler un objet à l'atelier
     * 
     * @param  int $item_id L'ID de l'objet à fabriquer
     * @return string
     */
    function craft($item_id)
    {
        
        return
        '<form method="post" action="#popsuccess" style="margin-top:0.25em">
            <input type="hidden" name="api_name" value="items">
            <input type="hidden" name="action" value="craft">
            <input type="hidden" name="params[item_id]" value="'.$item_id.'">
            &#128295;<input type="submit" class="redbutton" value="Assembler l\'objet">	
        </form>';
    }
    
    
    /**
     * Returns the button to pick up water from the well
     * 
     * @param int $construction_id The ID of the well constructed in the city
     *                             (not the ID of the building type "well")
     * @param boolean $show_icon
     * @return string HTML
     */
    function well_pickup($construction_id, $show_icon=true)
    {
        
        $button = $this->buttons['well_pickup'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].' ';
        
        return'
        <form name=attack method="post" action="#popsuccess" style="display:inline">
            <input type="hidden" name="api_name" value="'.$button['fields']['api_name'].'">
            <input type="hidden" name="action" value="'.$button['fields']['action'].'">
            <input type="hidden" name="params[item_id]" value="'.$button['fields']['params[item_id]'].'">
            <input type="hidden" name="params[construction_id]" value="'.$construction_id.'">
            <input type="submit" value="'.$icon.$button['name'].'" class="redbutton" title="'.$button['title'].'" style="min-width:auto">
        </form>';
    }
    
    
    /**
     * Returns the button to add water in the the well
     * 
     * @param int $construction_id The ID of the well constructed in the city
     *                             (not the ID of the building type "well")
     * @param boolean $show_icon
     * @return string HTML
     */
    function well_drop($construction_id, $show_icon=true)
    {
        
        $button = $this->buttons['well_drop'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].' ';
        
        return'
        <form name=attack method="post" action="#popsuccess" style="display:inline">
            <input type="hidden" name="api_name" value="'.$button['fields']['api_name'].'">
            <input type="hidden" name="action" value="'.$button['fields']['action'].'">
            <input type="hidden" name="params[item_id]" value="'.$button['fields']['params[item_id]'].'">
            <input type="hidden" name="params[construction_id]" value="'.$construction_id.'">
            <input type="submit" value="'.$icon.$button['name'].'" class="redbutton" title="'.$button['title'].'" style="min-width:auto">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour investir des points d'action dans un chantier
     * 
     * @param  int $building_id L'ID du chantier à construire
     * @return string
     */
    function construct($building_id, $notify, $button_label='Participer au chantier [1&#9889;]')
    {
        
        // Hide the round notifiction chip over the button
        $hide_notif = ($notify === 'no_notif') ? 'hidden' : '';
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="buildings">
            <input type="hidden" name="action" value="build">
            <input type="hidden" name="params[building_id]" value="'.$building_id.'">
            <button type="submit" class="redbutton" style="position:relative;width:100%;height:3em;border-radius:0.2em">
                <div class="dot_number '.$hide_notif.'">&nbsp;</div>
                '.$button_label.'
            </button>
        </form>';
    }
    
    
    /**
     * Explore a building in the desert
     * 
     * @param string $building_alias The alias of the building, as returned by the API
     * @return string
     */
    function explore_building($building_alias)
    {
        
        return '<a href="#popsuccess" class="redbutton">Explorer : <br>'.$building_alias.'</a>';
    }
    
    
    /**
     * Take the control of another citizen. Useful to control bot-citizens.
     *  
     * @param string $button_alias
     * @param int $target_id The ID of the citizen (not the user!) you want to control
     * @return string HTML
     */
    function switch_citizen($button_alias, $target_id)
    {
        
        $button = $this->buttons[$button_alias];
        $fields = $button['fields'];

        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="'.$fields['api_name'].'">
            <input type="hidden" name="action" value="'.$fields['action'].'">
            <input type="hidden" name="params[target_id]" value="'.$target_id.'">
            <button type="submit" class="redbutton" title="'.$button['title'].'">'.$button['name'].'</button>
        </form>';
    }
    
    
    /**
     * Button to move the members of an expedition on the map
     * 
     * @param int $path_id The ID of the expedition to move
     * @return string HTML
     */
    function move_path($path_id)
    {
        
        $button = $this->buttons['move_path'];
        $fields = $button['fields'];
        
        return
        '<form method="post" action="#Outside" class="form_move_path">
            <input type="hidden" name="api_name" value="'.$fields['api_name'].'">
            <input type="hidden" name="action" value="'.$fields['action'].'">
            <input type="hidden" name="params[path_id]" value="'.$path_id.'">
            <button type="submit" title="'.$button['title'].'">'.$button['name'].'</button>
        </form>';
    }
    
    
    /**
     * Button to dig the zone where the expeiditon is
     * 
     * @param int $path_id The ID of the expedition
     * @return string HTML
     */
    function dig_path($path_id)
    {
        
        $button = $this->buttons['dig_path'];
        $fields = $button['fields'];
        
        return
        '<form method="post" action="#Outside" class="form_dig_path">
            <input type="hidden" name="api_name" value="'.$fields['api_name'].'">
            <input type="hidden" name="action" value="'.$fields['action'].'">
            <input type="hidden" name="params[path_id]" value="'.$path_id.'">
            <button disabled type="submit" title="'.$button['title'].'">'.$button['name'].'</button>
        </form>';
    }
        
    
    /**
     * Retourne le bouton pour créer un compte
     * 
     * @return string
     */
    function register()
    {
        
        return '<a class="button" href="register">Créer un compte</a>';
    }
    
    
    /**
     * Retourne le bouton pour se connecter
     * 
     * @return string
     */
    function connect()
    {
        
        return '<a class="button" href="connect">Me connecter</a>';
    }
    
    
    /**
     * Retourne le bouton pour se déconnecter
     * 
     * @return string
     */
    function disconnect()
    {
        
        return
        '<form method="post" action="connect">
            <input type="hidden" name="action" value="disconnect">
            <input type="submit" value="Me déconnecter" />
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour actualiser la page principale du jeu
     * 
     * @return string
     */
    function refresh()
    {
        
        // Le nombre aléatoire assure que la page sera réellement actualisée 
        // au lieu d'être simplement ramenée au niveau de l'ancre #Outside
        // (le navigateur considérera que c'est une url différente)
        return '<a href="index?'.rand(100, 999).'#Outside" style="font-size:55%" title="Actualiser l\'affichage">&#x1F504;</a>';
    }
}
