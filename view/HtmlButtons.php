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
                    'api_name'      => 'me',
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
                'name'  => 'Passer au jour suivant',
                'title' => "Bouton spécial béta-test",
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
                'name'  => 'Entrer en ville !',
                'title' => "Vous êtes aux portes d'une ville ! Si vous y entrez vous serez protégé des zombies... provisoirement.",
                'fields' => [
                    'api_name'          => 'city',
                    'action'            => 'go_inout'
                    ],
                ],
            'enter_tent' => [
                'icon'  => '&#9978;',
                'name'  => 'Entrer dans la tente',
                'title' => "Une tente a été montée ici. Cet abri ne semble pas très solide mais vous protègera de la rigueur du désert.",
                'fields' => [
                    'api_name'          => 'city',
                    'action'            => 'go_inout'
                    ],
                ],
            'attack_tent' => [
                'icon'  => '&#10060;',
                'name'  => 'Détruire cette tente !',
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
                    'params[city_size]' => 1,
                    ],
                ],
            'build_city' => [
                'icon'  => '&#x1F307;',
                'name'  => 'Fonder une ville',
                'title' => "En vous rassemblant avec d'autres citoyens dans une ville, vous serez plus forts.",
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
            'well_add' => [
                'icon'  => '',
                'name'  => 'Ajouter 1 ration d\'eau',
                'title' => "",
                'fields' => [
                    'api_name'      => 'well',
                    'action'        => 'add',
                    ],
                ],
            'well_pickup' => [
                'icon'  => '',
                'name'  => 'Prendre 1 ration d\'eau',
                'title' => "",
                'fields' => [
                    'api_name'      => 'well',
                    'action'        => 'pickup',
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
        $icon   = ($show_icon === 'no_icon') ? '' : $button['icon'].'&nbsp;';
        $class_inactive = ($is_active !== true) ? 'inactive' : '';
        
        // Generates the hidden fields for the HTML form
        $hidden_fields = '';
        foreach ($button['fields'] as $fieldname=>$fieldval) {
            
            $hidden_fields.= '<input type="hidden" name="'.$fieldname.'" value="'.$fieldval.'">';
        }
        
        // Returns the complete HTML form
        return
        '<form method="post" action="#popsuccess" class="'.$class.'">
            '.$hidden_fields.'
            '.$icon.'<input type="submit" class="redbutton '.$class_inactive.'" value="'.$button['name'].'" title="'.$button['title'].'">'
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
                'label' => 'bâtiments',
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
        elseif ($amount >= $min_amount) {
            $dot_number = '<div class="dot_number">'.$amount.'</div>';
        }
        
        $class_inactive = ($is_active !== true) ? 'inactive' : '';
        
        return
        '<div class="round_action_block" id="round_'.$button_alias.'">'
            . '<input type="submit" class="round_action '.$class_inactive.'" value="'.$button['icon'].'" '
                    . 'onclick="toggleActionBlock(\''.$button_alias.'\')">'
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
     * @param int    $item_id   The ID of the iem to use (ex : 501)
     * @param string $item_name The name of the item to display (ex : Bandage")
     * 
     * @return string HTML
     */
    function use_item($button_alias, $item_id, $item_name)
    {
        
        $button = $this->buttons[$button_alias];
        $fields = $button['fields'];
        
        return
        '<form method="post" action="#popsuccess" class="formlink">
            <input type="hidden" name="api_name" value="'.$fields['api_name'].'">
            <input type="hidden" name="action" value="'.$fields['action'].'">
            <input type="hidden" name="params[item_id]" value="'.$item_id.'">
            <input type="submit" value="'.$button['name'].' '.$item_name.'" />
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
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="city">
            <input type="hidden" name="action" value="build">
            '.$icon.'<input type="submit" class="redbutton" value="'.$button['name'].'" title="'.$button['title'].'">
            de
            <select name="params[city_size]">
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
     * @return string
     */
    function kill_zombies($nbr_zombies, $button_alias='kill_zombie')
    {
        
        $button = $this->buttons[$button_alias];
        $class = '';
        $title = $button['title'];
        
        if ($nbr_zombies === 0) {
            $class = 'inactive';
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="api_name" value="zone">
            <input type="hidden" name="action" value="'.$button['fields']['action'].'">
            <input type="submit" value="'.$button['icon'].' '.$button['name'].'" class="redbutton '.$class.'"  title="'.$title.'">
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
        
        return
        '<form method="post" action="#popsuccess" style="display:inline">
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
        
        return
        '<form method="post" action="#popsuccess" style="display:inline">
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
            <input type="hidden" name="api_name" value="buildings">
            <input type="hidden" name="action" value="build">
            <input type="hidden" name="params[item_id]" value="'.$item_id.'">
            &#128295;<input type="submit" class="redbutton" value="Assembler l\'objet">	
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour investir des points d'action dans un chantier
     * 
     * @param  int $construction_id L'ID du chantier à construire
     * @return string
     */
    function construct($construction_id)
    {
        
        return
        '<form method="post" action="#popsuccess" class="formlink" style="text-align:center;margin-top:0.25em">
            <input type="hidden" name="api_name" value="buildings">
            <input type="hidden" name="action" value="build">
            <input type="hidden" name="params[construction_id]" value="'.$construction_id.'">
            &#x1F6E0;&#xFE0F; <input type="submit" value="Participer au chantier [1pa]" style="padding-top:0.8em">
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
        return '<a href="index?'.rand(100, 999).'#Outside" style="font-size:1.3em" title="Actualiser l\'affichage">&#x1F504;</a>';
    }
}
