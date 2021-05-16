<?php
require 'HtmlPage.php';
require 'plural.php';
safely_require('/core/controller/get_item_action.php');

/**
 * Génère les blocs HTML du jeu.
 * Certains éléments spécifiques (la carte, les boutons...) ne figurent pas ici 
 * car ils font l'objet d'une classe dédiée.
 */
class HtmlLayout extends HtmlPage
{
    
    /**
     * Barre de connexion horizontale en haut de la page
     * 
     * @param int    $user_id
     * @param int    $citizen_id
     * @param string $citizen_pseudo
     * @return string HTML
     */
    function connection_bar($user_id, $citizen_id, $citizen_pseudo)
    {
        
        $buttons = new HtmlButtons;
        
        // Si le joueur n'est pas connecté
        if ($user_id === NULL) { 
            
            return $buttons->register() . $buttons->connect();
        }
        // Si le joueur est connecté mais n'a pas encore de créé de citoyen,
        // on affiche son nom de joueur
        elseif ($citizen_id === NULL) {
            
            return '<span class="bold">Connecté en tant que joueur n°' . $user_id . '</span>'
                    . $buttons->disconnect();
        }
        // Si le joueur est connecté et a déjà créé son citoyen,
        // on affiche le nom de ce citoyen
        else {
            
            return 'Vous êtes le citoyen <strong>'.$citizen_pseudo.'</strong>'
                   . '<span id="citizenId" class="hidden">'.$citizen_id.'</span>'
                   . $buttons->disconnect();
        } 
    }
    
    
    /**
     * Displays the block to connect to the player account (at the right of the map)
     * 
     * @return string HTML
     */
    function block_connect()
    {
        
        return '
            <div id="identification_near_map">
                Identifiez-vous pour commencer à&nbsp;jouer...
                <a href="register" id="register">Créer un&nbsp;compte</a>
                ou
                <a href="connect" id="connect">Connectez-vous</a> si vous êtes déjà inscrit
            </div>';
    }
    
    
    /**
     * Affiche le bloc pour créer son premier citoyen (à droite de la carte)
     * 
     * @return string
     */
    function block_create_citizen()
    {
        
        return '
            <div id="identification_near_map">
                <h3>Créer mon premier citoyen</h3>
                <form method="post" action="#popsuccess">
                    Nom de mon citoyen&nbsp;:
                    <input type="hidden" name="method" value="POST">
                    <input type="hidden" name="api_name" value="user">
                    <input type="hidden" name="action" value="create_citizen"><br>
                    <input type="text" name="params[pseudo]"><br>
                    <input type="submit" value="Valider">
                </form>
            </div>';
    }
    
    
    /**
     * Boutons pour choisir sa spécialité citoyenne
     * (explorateur, fouineur, bâtisseur)
     * 
     * @param array $specialities Les caractéristiques de chaque spécialité, issues de l'API
     *                            (points d'action, temps de fouille..)
     * @return string
     */
    function block_speciality_choice($specialities)
    {
        
        $buttons = new HtmlButtons();
        
        return '
                <p class="center">
                    <button class="redbutton" onclick="toggle(\'specialities\');return false">Choisir ma spécialité du jour</button>
                </p>

                <ul id="specialities">
                    <li>'.$buttons->button('specialize_digger', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['digger']['action_points']    .'&nbsp;&nbsp; |
                        Capacité du sac&nbsp;: '. $specialities['digger']['bag_size'] .'&nbsp;objets]
                    </li>
                    <li>'.$buttons->button('specialize_explorer', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['explorer']['action_points']    .'&nbsp;&nbsp; |
                        Capacité du sac&nbsp;: '. $specialities['explorer']['bag_size'] .'&nbsp;objets]
                    </li>
                    <li>'.$buttons->button('specialize_builder', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['builder']['action_points']    .' |
                        Capacité du sac&nbsp;: '. $specialities['builder']['bag_size'] .'&nbsp;objets]
                    </li>
                </ul>';
    }
    
    
    /**
     * Bloc contenant les caractéristiques du joueur (points d'action, temps de fouille...)
     * INUTILISÉ depuis que ces données sont affichés dans le smartphone à droite de la carte
     * 
     * @param array $speciality_caracs Les caractéristiques de la spécialité du citoyen,
     *                                 issues de l'API (points d'action, temps de fouille..)
     * @param int   $current_action_points Le nombre de points d'action dont dispose 
     *                                     actuellement le citoyen
     * @param int   $is_wounded Vaut 1 si le joueur est blessé
     * @return type
     */
    function block_health($speciality_caracs, $current_action_points, $is_wounded)
    {
        
        $html_wounded = '';
        
        if ($is_wounded !== 0) {
            
            $html_wounded = '<p><a href="#popwounded">
                    &#129656;<strong style="background:red;color:white">Vous êtes blessé !</strong>
                    </a></p>';
        }
        
        return '
                <p><strong style="color:lightgrey">Spécialité&nbsp;:</strong>
                    '.$speciality_caracs['name'].'</p>
                <p><strong style="color:lightgrey">Points d\'action&nbsp;:</strong>
                    '.$current_action_points.'&nbsp;/&nbsp;'.$speciality_caracs['action_points'].'</p>
                <p><strong style="color:lightgrey">Temps de fouille&nbsp;:</strong>
                    '.$speciality_caracs['digging_duration'].'&nbsp;mn</p>'
                . $html_wounded;
    }
    
    
    /**
     * Bar above the map showing the countdown before the next attack, the current day...
     * @param  int $map_id The ID of the map on which the player is
     * @param  int $day The number of days since the game start
     * @return string HTML
     */
    function attack_bar($map_id, $day)
    {
        
        return '
            <div id="day">
                Carte n° '.$map_id.'<br>
                &#x1F551; Jour '.(int)$day.'
            </div>        
            <div id="timer">
                Attaque dans
                <div id="attackCountdown">&nbsp;</div>
            </div>
            <div id="balance">
                <div>00 zombies</div>
                <div>00 défenses</div>
            </div>';
    }
    
    
    /**
     * Retourne la liste HTML des citoyens de la ville
     * 
     * @param array $citizens_caracs    Les données sur tous les citoyens
     *                                  issues de la base de données
     * 
     * @return string
     */
    function map_citizens($citizens_caracs)
    {
        
        $html = '';

        foreach ($citizens_caracs as $caracs) {

            $html.= '<li>'.$caracs['citizen_pseudo'].' (citoyen n°&nbsp;'.$caracs['citizen_id'].') '
                    . 'est en ['.$caracs['coord_x'].':'.$caracs['coord_y'].']</li>';
        }

        return '<ul>'.$html.'</ul>';
    }
    
    
    /**
     * Retourne la liste HTML des citoyens sur la case
     * 
     * @param array $citizens_caracs    Les données sur tous les citoyens de la zone
     *                                  (issues de la BDD et triées avec citizens_by_coords())
     * 
     * @return string
     */
    function block_zone_fellows($citizens_caracs, $citizen_id)
    {
        
        $buttons = new HtmlButtons;
        $html = '';
        
        if (count($citizens_caracs) <= 1) {
            
            return '<p class="greytext">Personne à proximité. Vous êtes seul au milieu de cette zone désertique...</p>';
        }
        
        foreach ($citizens_caracs as $caracs) {
            
            $attack_button = '<span style="color:grey;font-size:0.8em"> [c\'est vous !]</span>';
            
            if ($caracs['citizen_id'] !== $citizen_id) {
                // Bouton pour agresser/soigner le citoyen
                $attack_button = ($caracs['is_wounded'] === 0)
                              ? $buttons->attack_citizen($caracs['citizen_id'], $caracs['citizen_pseudo'])
                              : $buttons->heal_citizen($caracs['citizen_id'], $caracs['citizen_pseudo']);
            }

            $html.= '<li><span class="userlabel"><span class="avatar">&#x1F464;</span> '.$caracs['citizen_pseudo'].'</span>'
                  . $attack_button
                  . '</li>';
        }
        
        return '<ol style="padding-left:0.9em">'.$html.'</ol>';
    }
    
    
    /**
     * Liste des objets sur une case de la carte
     * 
     * @param array $items_caracs Caractéristiques des objets disponibles en jeu (issu de l'API "config")
     * @param array $zone       Données de la zone où se trouve le joueur (telles que retournées par l'API)
     * @param int   $citizen_id L'id du citoyen connecté
     * 
     * @return string
     */
    function block_zone_items($items_caracs, $zone)
    {
        
        $html_items = '';
        
        if (!empty($zone['items'])) {
            
            foreach ($zone['items'] as $item_id=>$item_amount) {
                
                $html_items .= '<li class="item_label">'
                    . '<button type="submit" name="params[item_id]" value="'.$item_id.'" class="drop_button" title="Ramasser cet objet">&wedgeq;</button> '
                    . '<var>
                        <img src="../resources/img/copyrighted/items/'.$item_id.'.png" alt="'.$items_caracs[$item_id]['icon_symbol'].'">
                        &nbsp;'. $items_caracs[$item_id]['name'] 
                    . '</var> <span style="font-size:0.95em">×&nbsp;'.$item_amount.'<span>' 
                    . '</li>';
            }

            return '<form method="post" action="#Outside">'
                . '<input type="hidden" name="api_name" value="zone">'
                . '<input type="hidden" name="action" value="pickup">'
                . '<ul class="items_list">'.$html_items.'</ul>'
                . '</form>';
        }
        else {
            
            return '<div class="greytext" style="margin-top:0.5rem">
                    Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...
                    </div>';
        }
    }
    
    
    function block_actions_zombies($zone_zombies)
    {
        
        $buttons = new HtmlButtons;
        
        if ($zone_zombies === 0) {
            
            return '<p class="greytext">Aucun zombie dans la zone. Vous êtes libre de vos mouvements...</p>';
        }
        else {
            return '<div class="zombies_text">
                    <strong class="nbr_zombies">'.plural($zone_zombies, 'zombie').'</strong> autour de vous !
                    </div>
                    <div class="zombies_visual">'. str_repeat('<span class="zombie">&#x1F9DF;</span>', $zone_zombies) .'</div>
                    <div class="buttons_kill">'
                        . $buttons->kill_zombies($zone_zombies, 'kill_zombie')
                        . $buttons->kill_zombies($zone_zombies, 'kill_mass_zombies') .'
                    </div>';
        }
    }
    
    
    /**
     * Displays informations about the action points to the player
     * 
     * @param int $citizen_AP The amount of action points the player currently has
     * @param int $total_AP   The maximumum amount of action points of his speciality
     * @param int $zone_zombies The amount of zombies in the zone
     * @param int $moving_cost_no_zombies Amount of action points required to move 
     *                                    in a zone without any zombie
     * @param int $moving_cost_zombies Amount of action points required to move 
     *                                 in a zone with 1 zombie or more
     * @return string HTML
     */
    function block_movement_AP($citizen_AP, $total_AP, $zone_zombies,
                               $moving_cost_no_zombies, $moving_cost_zombies) {
        
        $moving_cost = ($zone_zombies > 0) ? $moving_cost_zombies : $moving_cost_no_zombies;
        
        $AP_cost = ($moving_cost > 0)
                   ? '<div class="darkred"><strong>-'.$moving_cost.'</strong> point pour quitter la zone</div>' 
                   : '<div style="font-size:0.9em">Déplacement gratuit</div>';
        
        
        if($moving_cost_no_zombies === $moving_cost_zombies) {
            $AP_cost_reason = 'coût de la marche';
        }
        elseif($zone_zombies > 0 and $moving_cost_zombies > 0) {
            $AP_cost_reason = 'présence de zombies';
        }
        elseif($zone_zombies === 0 and $moving_cost_no_zombies === 0) {
            $AP_cost_reason = 'aucun zombie dans la zone';
        }
        else {
            $AP_cost_reason = 'coût de la marche sans zombies';
        }
        

        return '<div id="movement_ap">
                <a href="#popmove" id="actionpoints">
                    <strong style="font-size:2em">'.$citizen_AP.'</strong>
                    <span style="opacity:0.8">/'.$total_AP.'<br>points d\'action</span>
                </a>
                <a href="#popmove" id="movement_cost">
                    '.$AP_cost.'
                    <span style="font-size:0.85em">('.$AP_cost_reason.')</span>
                </a>
            </div>';
    }
    
    
    /**
     * Actions block next to the map
     * 
     * @param int $city_size Size of the city in the zone where the player is.
     * @return string HTML
     */
    function block_actions_context($city_size, $building_id, $configs_buildings)
    {
        
        $buttons = new HtmlButtons();
        $popup   = new HtmlPopup();
        $table   = '';
        
        // TODO: make a generic class to get the configs without checking the nullity
        // e.g.: Config()->building(5)->name;
        $building_name = ($building_id !== null) 
                         ? $building_name = $configs_buildings[$building_id]['name']
                         : '';
        
        // If there is a CRYPT in the zone, display the button to enter.
        // TODO: replace this hardcoded ID by a standard treatment
        if ($building_id === 2) {

            $table .= '<tr>'
                    . '<td class="center">'
                    . '<span class="warning">Vous avez découvert une crypte&nbsp;!</span><br>'
                    . $popup->link('popvault', 'Pouvoir cryptique')
                    . '</td>'
                . '</tr>';
        }
        elseif ($building_id !== null) {
            
            $table .= '<tr>'
                    . '<td class="center">'
                    . '<span class="warning">Vous avez découvert un bâtiment !</span><br>'
                    . $buttons->explore_building($building_name)
                    . '</td>'
                . '</tr>';
        }
        // If there is a TENT in the zone, display the button to enter.
        elseif ($city_size === 1) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_tent').'</td>
                <td>'.$buttons->button('enter_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td>'.$buttons->icon('attack_tent').'</td>
                <td>'.$buttons->button('attack_tent', 'no_icon').'</td>
            </tr>';
        }
        // If there is a CITY in the zone, display the button to enter.
        elseif ($city_size > 0) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_city').'</td>
                <td>'.$buttons->button('enter_city', 'no_icon').'</td>
            </tr>';
        }
                
        return '<table style="margin:1.5rem auto 0 auto">'.$table.'</table>';
    }
    
    
    function block_actions_build($city_size, $building_id)
    {
        
        $buttons = new HtmlButtons;
        $popup   = new HtmlPopup;
        $table = '';
        
        // If there is a TENT in the zone, display the button to enter.
        if ($city_size === 1) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_tent').'</td>
                <td>'.$buttons->button('enter_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td>'.$buttons->icon('attack_tent').'</td>
                <td>'.$buttons->button('attack_tent', 'no_icon').'</td>
            </tr>';
        }
        // If there is a CITY in the zone, display the button to enter.
        elseif ($city_size > 0) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_city').'</td>
                <td>'.$buttons->button('enter_city', 'no_icon').'</td>
            </tr>';
        }
        else {
            
            $table .= '<tr>
                <td>'.$buttons->icon('build_tent').'</td>
                <td>'.$buttons->button('build_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td><img style="height:1.4em" src="resources/img/free/city.png" alt="&#127751;"></td>
                <td>'.$buttons->build_city('no_icon').'</td>
            </tr>';               
        }
        
        
        // If there is a CRYPT in the zone, display the button to enter.
        // TODO: replace this hardcoded ID (2 = crypt) by a generic method
        if ($building_id === 2) {

            $table .= '<tr>'
                    . '<td>'.$buttons->icon('add_vault').'</td>'
                    . '<td>'. $popup->link('popvault', 'Pouvoir cryptique').'</td>'
                . '</tr>';
        }
        else {
            $table .= '<tr>
                <td>'.$buttons->icon('add_vault').'</td>
                <td>'.$buttons->button('add_vault', 'no_icon').'</td>
            </tr>';
        }
        
        
        return '<table>'.$table.'</table>';
    }
    
    
    /**
     * Specific block to use the items in the bag
     * 
     * @param array $items_caracs   Les caractéristiques des objets du jeu, telles que 
     *                              retournées par l'API "configs"
     * @param array $bag_items      Liste des objets dans le sac du joueur
     * @return string HTML
     */
    function block_actions_bag($items_caracs, $bag_items)
    {
        
        $buttons    = new HtmlButtons();
        $html_items = null;
        
        foreach ($bag_items as $id=>$amount) {
            
            $button_alias = get_item_action($items_caracs[$id]);
            
            if ($button_alias !== null) {
                $html_items .= '<li>'.$buttons->use_item($button_alias, $id, $items_caracs[$id]['name']).'</li>';
            }
        }
        
        if ($html_items === null) {
            $html_items = "<li class=\"greytext\" style=\"font-size:0.9em\">Le sac ne contient pas d'objet utilisable ici...</li>";
        }            
        
        return '<div id="actions_bag">
                &#128188; <strong>Utiliser un objet de mon sac</strong>
                <ul>
                    '.$html_items.'
                </ul>
            </div>';
    }
    
    
    /**
     * Bloc rouge affiché quand le joueur est bloqué par les zombies
     * 
     * @param  int $zombies Le nombre de zombies sur la case
     * @return string HTML
     */
    function block_alert_control($zombies)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div id="alert_control" class="cover_paddle">
                <div class="title">Bloqué par les zombies !</div>
                <div class="text">
                    Les <span class="nbr_zombies">'.$zombies.'</span> zombies 
                    sont trop nombreux et vous encerclent ! Vous pouvez tenter 
                    d\'attaquer ces putrides afin de dégager le passage...<br>
                    <a href="#popcontrol">[En savoir plus...]</a>
                    <p>
                        '.$buttons->kill_zombies($zombies).'<br>
                        '.$buttons->kill_zombies($zombies, 'kill_mass_zombies').'
                    </p>
                </div>
            </div>';
    }
    
    
    /**
     * Block displayed if the player has not enough action points to move
     * 
     * @param int $zombies The amount of zombies in the zone
     * @return string HTML
     */
    function block_alert_tired($zombies)
    {
        
        $tip_kill_zombie = ($zombies > 0) 
                            ? '► Tuez <span class="nbr_zombies">'.plural($zombies, 'zombie').'</span>
                               pour vous déplacer sans effort<br>'
                            : '';
        
        return '
            <div id="alert_control" class="cover_paddle">
                <div class="title">Vous êtes épuisé !</div>
                <div class="text">
                    Vous n\'avez plus assez de points d\'action pour quitter la zone !
                    Elle sera votre tombe si vous ne vous abritez pas avant la nuit...
                    <div style="margin-top:0.7rem;padding:0.7rem;color:lightsteelblue;border-top:1px solid grey">
                        Quelques suggestions pour vous sortir de ce mauvais pas...<br>
                        '.$tip_kill_zombie.'
                        ► Explorez la zone pour chercher de la nourriture<br>
                        ► Construisez une tente pour vous abriter<br>
                        ► Demandez l\'aide d\'un autre citoyen
                    </div>
                </div>
            </div>';
    }
    
    
    /**
     * Displays the countdown to escape before the control of the zone is lost
     * 
     * @param int $timestamp The limit date to escape (Unix timestamp)
     * @return string
     */
    function block_alert_escape($timestamp)
    {
        
        $delay = date('i\m\n s\s', $timestamp-time());
        
        return '
            <div id="alert_control">
                <div class="text">
                    Les humains ont perdu le contrôle de la zone ! Il vous reste 
                    peu de temps pour fuir avant d\'être totalement bloqué :
                    <br> <strong id="controlCountdown">'.$delay.'</strong><br>
                    <a href="#popcontrol">[En savoir plus...]</a>
                </div>
                <div class="hidden" id="controlTimestamp">'.$timestamp.'</div>
            </div>';
    }
    
    
    /**
     * Displayed under the movement paddle if the player is wounded
     * 
     * @param bool $is_wounded True if the player is wounded
     * @return string HTML
     */
    function block_alert_wounded($is_wounded)
    {
        
        if ($is_wounded === true) {
            return '<a href="#popwounded" id="alert_wounded">
                    <span class="alert_sign">&#x26A0;&#xFE0F;</span>
                    Vous êtes blessé !
                    <span class="link">[Me soigner...]</span>
                </a>';
        }
    }
    
    
    /**
     * Liste des objets dans le sac du citoyen
     * 
     * @param array $items_caracs   Les caractéristiques de tous les items existants dans le jeu
     * @param array $bag_items      Les objets dans le sac du citoyen, sous forme
     *                              de paires "id de l'objet => quantité"
     * @param int   $max_bag_slots  Nombre total d'emplacements dans le sac
     * 
     * @return string   La liste des objets sous forme de liste HTML
     */
    function block_bag_items($items_caracs, $citizen_id, $bag_items, $max_bag_slots)
    {
        
        $nbr_free_slots = $max_bag_slots - array_sum(array_values($bag_items));
        
        return '
            <form method="post" action="#Outside">
                <input type="hidden" name="api_name" value="zone">
                <input type="hidden" name="action" value="drop">
                <input type="hidden" name="params[citizen_id]" value="'.$citizen_id.'">
                <ul class="items_list">
                    ' . $this->bag_filled_slots($bag_items, $items_caracs) . '
                    ' . $this->bag_free_slots($nbr_free_slots) . '
                </ul>
            </form>';
    }
    
    
    /**
     * Emplacements vides du sac
     * 
     * @param type $nbr_free_slots Nombre d'emplacements libres dans le sac
     * @return string
     */
    private function bag_free_slots($nbr_free_slots)
    {
        
        $result = '';
        
        for ($i=0; $i<$nbr_free_slots; $i++) {
            
            $result.= "\n<li><var class=\"empty_slot\">-vide-</var></li>\n";
        }
        
        return $result;
    }
    
    
    /**
     * Emplacements occupés du sac
     * 
     * @param array $bag_items      Liste des id des objets dans le sac
     * @param array $items          Les caractéristiques de tous les items exitants dans le jeu
     * @return string
     */
    private function bag_filled_slots($bag_items, $items)
    {
        
        $result = '';
        
        foreach ($bag_items as $item_id=>$item_amount) {
            
            // Si le citoyen possède un objet en plusieurs exemplaires, on le fait 
            // apparaître autant de fois dans le sac.
            while ($item_amount > 0) {
                
                $result .= '
                    <li class="item_label">
                        <button type="submit" name="params[item_id]" value="'.$item_id.'" class="drop_button" title="Déposer cet objet">&veeeq;</button>
                        <var>
                            <img src="../resources/img/copyrighted/items/'.$item_id.'.png" alt="'.$items[$item_id]['icon_symbol'].'"> 
                            &nbsp;' . $items[$item_id]['name'] .
                        '</var>
                    </li>';
                
                $item_amount--;
            }
        }
        
        return $result;
    }
    
    
    /**
     * Data about the player for javascript treatments (his coordinates...)
     * 
     * @param array $citizen The data about the citizen, as returned by the API
     *                       (citizen ID, his coordinates on the map...)
     * @return string HTML
     */
    function hidden_player_data($citizen) {
        
        return '<div id="citizenId" class="hidden">'.$citizen['citizen_id'].'</div>
                <div id="citizenPseudo" class="hidden">'.$citizen['citizen_pseudo'].'</div>
                <div id="citizenCoordX" class="hidden">'.$citizen['coord_x'].'</div>
                <div id="citizenCoordY" class="hidden">'.$citizen['coord_y'].'</div>
                <div id="mapId" class="hidden">'.$citizen['map_id'].'</div>';
    }
}
