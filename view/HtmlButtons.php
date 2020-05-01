<?php

/**
 * Crée les boutons d'actions pour l'interface HTML du jeu
 * (fouiller la zone, attaquer un zombie...)
 */
class HtmlButtons
{
    
    
    function __construct()
    {
        
        $this->buttons = [
            'dig' => [
                'icon'  => '&#x26CF;&#xFE0F;',
                'name'  => 'Fouiller la zone',
                'title' => ''
                ],
            'vault' => [
                'icon'  => '&#9961;&#65039;',
                'name'  => 'Chercher une crypte',
                'title' => "Trouver une crypte peut servir vos intérêts mais aussi causer votre perte... ou celle de vos amis.",
                ],
            'enter_city' => [
                'icon'  => '',
                'name'  => 'Entrer en ville !',
                'title' => "Vous êtes aux portes d'une ville ! Si vous y entrez vous serez protégé des zombies... provisoirement.",
                ],
            'attack_tent' => [
                'icon'  => '',
                'name'  => 'Détruire cette tente !',
                'title' => "Un citoyen a planté sa tente ici. Vous avez l'opportunité de la détruire...",
                ],
            'attack_citizen' => [
                'icon'  => '&#128074;&#127995;',
                'name'  => 'Agresser !',
                'title' => "",
                ],
            'heal_citizen' => [
                'icon'  => '&#129657;',
                'name'  => 'Soigner !',
                'title' => "",
                ],
            'kill_zombie' => [
                'icon'  => '',
                'name'  => 'Attaquer à mains nues !',
                'title' => "Attaquer un zombie à mains nues. Vous gagnerez un picto en cas de succès.",
                ],
            
            'kill_mass_zombies' => [
                'icon'  => '',
                'name'  => 'Nettoyer la zone au lance-flammes',
                'title' => "Comme les zombies sont particulièrement nombreux ici, vous pouvez "
                         . "les attaquer par groupe. C'est très efficace, mais en contrepartie "
                         . "vous ne gagnerez aucun picto.",
                ],
            'build_tent' => [
                'icon'  => '&#9978;',
                'name'  => 'Planter ma tente',
                'title' => "Une tente vous protègerait de la rigueur du désert.",
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
                ],
            'open_city_door' => [
                'icon'  => '',
                'name'  => 'Ouvrir les portes !',
                'title' => "",
                ],
            'close_city_door' => [
                'icon'  => '',
                'name'  => 'Fermer les portes !',
                'title' => "",
                ],
        ];
    }
    
    
    /**
     * Retourne le bouton pour contruire une tente (ville d'un seul citoyen)
     * 
     * @return string
     */
    function build_tent($show_icon=true)
    {
        
        $button = $this->buttons['build_tent'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'];
        
        return 
        '<form   method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="city">
            <input type="hidden" name="action" value="build">
            <input type="hidden" name="params[city_size]" value="1">
            '.$icon.'<input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
                   
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
            '.$icon.'<input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
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
    function kill_zombie($nbr_zombies)
    {
        
        $button = $this->buttons['kill_zombie'];
        $class = '';
        $title = $button['title'];
        
        if ($nbr_zombies === 0) {
            $class = 'inactive';
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="api_name" value="zone">
            <input type="hidden" name="action" value="fight">
            <input type="submit" value="'.$button['name'].'" class="'.$class.'"  title="'.$title.'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour attaquer des zombies en masse
     * 
     * @param int $nbr_zombies  Le nombre de zombies dans la zone
     * @return string
     */
    function kill_mass_zombies($nbr_zombies)
    {
        
        $button = $this->buttons['kill_mass_zombies'];
        $class = '';
        $title = $button['title'];
        
        if ($nbr_zombies === 0) {
            $class = 'inactive';
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="api_name" value="zone">
            <input type="hidden" name="action" value="bigfight">
            <input type="submit" value="'.$button['name'].'" class="'.$class.'"  title="'.$title.'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour fouiller la zone
     * 
     * @return string
     */
    function dig($show_icon=true)
    {
        
        $button = $this->buttons['dig'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].'&nbsp;';
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="zone">
            <input type="hidden" name="action" value="dig">
            '.$icon.' <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour générer une crypte sur la carte
     * 
     * @return string
     */
    function add_vault($show_icon=true)
    {
        
        $button = $this->buttons['vault'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].'&nbsp;';
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="zone">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="params[stuff]" value="vault">
            '.$icon.' <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">'
        .'</form>';
    }
    
    
    /**
     * Retourne le bouton pour détruire une tente
     * 
     * @return string
     */
    function attack_tent($show_icon=true)
    {
        
        $button = $this->buttons['attack_tent'];
        $icon = ($show_icon === 'no_icon') ? '' : '&nbsp;<span style="font-size:1.2em">X</span>&nbsp;&nbsp;';
        
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="action" value="attack_city">
            '.$icon.' <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
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
     * @param string $target_pseudo Le nom du citoyen à soigner
     * @return string
     */
    function heal_citizen($target_id, $target_pseudo, $show_icon=true, $text='default')
    {
        
        $button = $this->buttons['heal_citizen'];
        $icon = ($show_icon === 'no_icon') ? '' : $button['icon'].' ';
        $text = ($text !== 'default') ? $text : $button['name']; 
        
        return
        '<form method="post" action="#popsuccess" style="display:inline">
            <input type="hidden" name="api_name" value="me">
            <input type="hidden" name="action" value="heal">
            <input type="hidden" name="params[target_id]" value="'.$target_id.'">
            <input type="submit" value="'.$icon.$text.'" title="'.$button['title'].'" style="min-width:auto">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour entrer en ville
     * 
     * @return string
     */
    function enter_city()
    {
        
        $button = $this->buttons['enter_city'];
        
        return
        '<form method="post" action="#Outside">
           <input type="hidden" name="action" value="go_inout_city">

           <!-- <span style="font-size:1.2em">&gt;</span>&nbsp; -->
           <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
       </form>';
    }
    
    
    /**
     * Retourne le bouton pour sortir de la ville
     * 
     * @return string
     */
    function get_out_city()
    {
        
        $button = $this->buttons['get_out_city'];
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="go_inout_city">

            <!-- <span style="font-size:1.2em">&gt;</span>&nbsp; -->
            <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour ouvrir la porte de la ville
     * 
     * @return string
     */
    function open_city_door()
    {
        
        $button = $this->buttons['get_out_city'];
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="open_city_door">
            <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour fermer la porte de la ville
     * 
     * @return string
     */
    function close_city_door()
    {
        
        $button = $this->buttons['close_city_door'];
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="close_city_door">
            <input type="submit" value="'.$button['name'].'" title="'.$button['title'].'">
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
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="action" value="craft_item">
            <input type="hidden" name="item_id" value="'.$item_id.'">
            <button class="as_link"><span style="font-weight:normal;font-size:1.1em;">&#9004;</span> Assembler l\'objet</button>
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
            '<form method="post" action="#popsuccess" style="text-align:center;margin-top:0.25em">
            <input type="hidden" name="action" value="construct">
            <input type="hidden" name="construction_id" value="'.$construction_id.'">
            <button class="as_link"><span style="font-weight:normal;font-size:1.1em;">&#9750;</span> Participer au chantier [1pa]</button>
        </form>';
    }
    
    
    /**
     * Bouton pour consommer un objet (eau, nourriture...)
     * 
     * @param int    $item_id   L'id de l'objet
     * @param string $item_name Le nom de l'objet à afficher
     * @return string HTML
     */
    function item_eat($item_id, $item_name)
    {
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="api_name" value="me">
            <input type="hidden" name="action" value="eat">
            <input type="hidden" name="params[item_id]" value="'.$item_id.'">
            <input type="submit" class="formlink" value="Consommer '.$item_name.'" />
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
     * Bouton imitant l'apparence d'un lien pour appeler une API
     * 
     * @param  string $action L'action à déclencher (ex : "move" pour créer un $_POST['move'])
     * @param  string $text   Le texte du lien
     * @param  string $anchor Ancre HTML pour indiquer quel endroit de la page
     *                        afficher après le clic sur le lien
     * @return string
     */
    function api_link($action, $text, $anchor='#Outside')
    {
        return '
            <form method="post" action="'.$anchor.'" style="display:inline">
                <input type="hidden" name="action" value="'.$action.'" />
                <input type="submit" value="'.$text.'" class="formlink" />
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
        return '[&nbsp;<a href="index?'.rand(100, 999).'#Outside">Actualiser&nbsp;la&nbsp;page</a>&nbsp;]';
    }
}
