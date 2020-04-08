<?php

/**
 * Crée les boutons d'actions pour l'interface HTML du jeu
 * (fouiller la zone, attaquer un zombie...)
 */
class HtmlButtons
{
    
    /**
     * Retourne le bouton pour contruire une tente (ville d'un seul citoyen)
     * 
     * @return string
     */
    function build_tent($show_icon=true)
    {
        
        $icon = ($show_icon === 'no_icon') ? '' : '<span style="font-size:1.35em">&#9978;</span>';
        
        return 
        '<form   method="post" action="#Outside">
            
            <input type="hidden" name="action" value="build_city">
            <input type="hidden" name="city_size" value="1">
            '.$icon.'<input type="submit" value="Planter ma tente" 
                        title="Une tente vous protègerait de la rigueur du désert.">
                   
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour contruire une ville
     * 
     * @return string
     */
    function build_city($show_icon=true)
    {
        
        $icon = ($show_icon === 'no_icon') ? '' : '<span style="font-size:1.35em"><img src="resources/img/city.png" alt="&#10224;">&nbsp;</span>';
        
        return
        '<form method="post" action="#Outside">
            
            <input type="hidden" name="action" value="build_city">

            '.$icon.'<input type="submit" value="Fonder une ville"
                      title="En vous rassemblant avec d\'autres citoyens dans une ville, vous serez plus forts.">
            de
            <select name="city_size">
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
        
        $class = '';
        $title = 'Attaquer un zombies à mains nues. Vous gagnerez un picto en cas de succès.';
        
        if ($nbr_zombies === 0) {
            $class = 'inactive';
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="action" value="fight">
            <input type="submit" value="Attaquer à mains nues !" class="'.$class.'"  title="'.$title.'">
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
        
        $class = '';
        $title = "Comme les zombies sont particulièrement nombreux ici, vous pouvez "
               . "les attaquer par groupe. C'est très efficace, mais en contrepartie "
               . "vous ne gagnerez aucun picto.";
        
        if ($nbr_zombies === 0) {
            $class = 'inactive';
            $title = "Il n'y a aucun zombie dans la zone";
        }
                
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="action" value="bigfight">
            <input type="submit" value="Nettoyer la zone au lance-flammes" class="'.$class.'"  title="'.$title.'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour fouiller la zone
     * 
     * @return string
     */
    function dig($show_icon=true)
    {
        
        $icon = ($show_icon === 'no_icon') ? '' : '&#x26CF;&#xFE0F;&nbsp;';
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="action" value="dig">
            '.$icon.' <input type="submit" value="Fouiller la zone">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour générer une crypte sur la carte
     * 
     * @return string
     */
    function add_vault($show_icon=true)
    {
        
        $icon = ($show_icon === 'no_icon') ? '' : '&#9961;&#65039;&nbsp;';
        
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="action" value="vault">
            '.$icon.' <input type="submit" value="Chercher une crypte" title="Trouver une crypte '
                    . 'peut servir vos intérêts mais aussi causer votre perte... ou celle de vos amis.">'
        .'</form>';
    }
    
    
    /**
     * Retourne le bouton pour détruire une tente
     * 
     * @return string
     */
    function attack_tent($show_icon=true)
    {
        
        $icon = ($show_icon === 'no_icon') ? '' : '&nbsp;<span style="font-size:1.2em">X</span>&nbsp;&nbsp;';
        
        return
        '<form method="post" action="#Outside">
            <input type="hidden" name="action" value="attack_city">
            '.$icon.' <input type="submit" value="Détruire cette tente !" 
                title="Un citoyen a planté sa tente ici. Vous avez l\'opportunité de la détruire...">
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
        
        $icon = ($show_icon === 'no_icon') ? '' : '&#128074;&#127995; ';
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="action" value="attack_citizen">
            <input type="hidden" name="target_id" value="'.$target_id.'">
            <input type="submit" value="'.$icon.'Agresser '.$target_pseudo.' !">
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
        
        $icon = ($show_icon === 'no_icon') ? '' : '&#129657; ';
        $text = ($text !== 'default') ? $text : 'Soigner '.$target_pseudo.' !'; 
        
        return
        '<form method="post" action="#popsuccess">
            <input type="hidden" name="action" value="heal_citizen">
            <input type="hidden" name="target_id" value="'.$target_id.'">
            <input type="submit" value="'.$icon.$text.'">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour entrer en ville
     * 
     * @return string
     */
    function enter_city()
    {
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
           <input type="hidden" name="action" value="go_inout_city">

           <!-- <span style="font-size:1.2em">&gt;</span>&nbsp; -->
           <input type="submit" value="Entrer !" 
                  title="Dans les villes, vous êtes protégé des zombies... provisoirement.">
       </form>';
    }
    
    
    /**
     * Retourne le bouton pour sortir de la ville
     * 
     * @return string
     */
    function get_out_city()
    {
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="go_inout_city">

            <!-- <span style="font-size:1.2em">&gt;</span>&nbsp; -->
            <input type="submit" value="Sortir de la ville" 
                   title="Dans les villes, vous êtes protégé des zombies... provisoirement.">
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour ouvrir la porte de la ville
     * 
     * @return string
     */
    function open_city_door()
    {
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="open_city_door">
            <input type="submit" value="Ouvrir les portes !" >
        </form>';
    }
    
    
    /**
     * Retourne le bouton pour fermer la porte de la ville
     * 
     * @return string
     */
    function close_city_door()
    {
        
        return
        '<form style="text-align:center" method="post" action="#Outside">
            <input type="hidden" name="action" value="close_city_door">
            <input type="submit" value="Fermer les portes !" >
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
