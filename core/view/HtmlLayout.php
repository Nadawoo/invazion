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
     * Store in the HTML the unvariable data of the game (building names...).
     * Useful to reuse those data with javascript without calling the Invazion's API
     * 
     * @param string $json_map       The JSON string containing the general characteristics
     *                               of the map : cost of a move...
     * @param string $json_buildings The JSON string containing the informations
     *                               about the buildings, as returned by the "configs" API
     * @param string $json_items     The JSON string containing the informations 
     *                               about the items, as returned by the "configs"  API
     * @return string HTML
     */
    function json_configs($json_map, $json_buildings, $json_items) {
        
        return '<section id="configs">
                    <div class="map">
                    '.$json_map.'
                    </div>
                    <div class="buildings">
                    '.$json_buildings.'
                    </div>
                    <div class="items">
                    '.$json_items.'
                    </div>
                </section>';
    }
    
    
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
                <form method="post" action="#popsuccess">
                    Donnez un nom à votre citoyen&nbsp;:
                    <input type="hidden" name="method" value="POST">
                    <input type="hidden" name="api_name" value="user">
                    <input type="hidden" name="action" value="create_citizen"><br>
                    <input type="text" name="params[pseudo]" placeholder="Votre pseudo"><br>
                    <br>
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
        
        $html_specialities = '';
        foreach($specialities as $alias=>$speciality) {
            
            $html_specialities .= '
                <li>'.$buttons->button('specialize_'.$alias, '', 'inline').'&nbsp;
                    [<abbr title="Les points d\'action vous permettent d\'explorer le désert, construire des bâtiments et d\'autres actions encore.">Points d\'action</abbr>&nbsp;:
                                  '. $speciality['action_points'].'&nbsp; |&nbsp;
                    <abbr title="Plus votre sac est grand, plus vous pouvez transporter d\'objets en même temps.">Sac</abbr>&nbsp;:
                    '.plural($speciality['bag_size'], 'objet').']
                    <div style="margin-left:0.5em;margin-bottom:0.5em;font-style:italic">'.$speciality['descr_purpose'].'</div>
                </li>';
        }
        
        return '
                <p class="center">
                    <button class="redbutton" onclick="toggle(\'specialities\');hide(\'capacities\');return false">Changer ma spécialité</button>
                    <button class="redbutton" onclick="toggle(\'capacities\');hide(\'specialities\');return false">Améliorer une capacité</button>
                </p>

                <ul id="specialities">
                    '.$html_specialities.'
                </ul>
                
                <ul id="capacities">
                    <li>'.$buttons->button('upgrade_camouflage', '', 'inline').'<br>
                        &nbsp;&nbsp;&nbsp;Permet de vous dissimuler aux yeux des autres humains
                    </li>
                    <li>'.$buttons->button('upgrade_vision', '', 'inline').'<br>
                        &nbsp;&nbsp;&nbsp;Permet de percer le camouflage des humains et des bâtiments
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
        
        $buttons = new HtmlButtons();
        
        return '
            <div class="left">
                Carte n° '.$map_id.'<br>
                &#x1F551; Jour '.(int)$day.'
            </div>
            <div id="timer">
                '.$buttons->button('end_cycle').'
            </div>
            <div class="right">
                <a id="notifsButton" title="Notifications">&#x1F514;</a>
                '.$buttons->refresh().'
                <div id="notifsBlock">
                    <a id="notifsClose">X</a>
                    <div id="notifsList"><div style="text-align:center;padding:0.8em;color:grey">Chargement en cours...</div></div>
                </div>
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
     * HTML blank template to display one citizen in the "humans" action block
     * next to the map (list of the citizens in the zone)
     * The appropriate data are then fulfilled by the javascript.
     * 
     * @return string HTML
     */
    function block_zone_fellow_template()
    {
        
        $buttons = new HtmlButtons;
        
        $template = '
            <template id="tplActionBlockFellow">
                <li class="userListItem">
                    <div class="userLabel">
                        <div class="avatar">&#x1F464;</div> 
                        <div class="pseudo">{{citizen_pseudo}}</div>
                    </div>
                    <div class="itsMe" style="display:flex;align-items:center;color:grey;font-size:0.8em">[c\'est vous !]</div>
                    <div class="actionButtons">
                        '.$buttons->attack_citizen('{citizen_id}', '{citizen_pseudo}').'
                        '.$buttons->heal_citizen('{citizen_id}', '{citizen_pseudo}').'
                    </div>
                </li>
            </template>';
        
        return $template;
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
                    . '<button type="submit" name="params[item_id]" value="'.$item_id.'" class="drop_button">&wedgeq;</button> '
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
    
    
    /**
     * HTML blank template to display one item in the "Dig" action block
     * next to the map (list of the items on the ground)
     * The appropriate data are then fulfilled by the javascript.
     * 
     * @return string
     */
    function block_zone_item_template()
    {
                
        return '
            <template id="tplActionBlockItem">
                <li class="item_label">
                    <button type="submit" name="params[item_id]" value="{item_id}" 
                            class="drop_button" title="Ramasser cet objet">&wedgeq;</button> 
                    <var>
                        <img src="" alt="{icon_symbol}">
                        &nbsp;<span class="item_name">{item_name}</span>
                    </var> <span style="font-size:0.95em">×&nbsp;<span class="item_amount">{item_amount}</span><span>
                </li>
            </template>';
    }
    
    
    function block_actions_zombies($zone_zombies, $ap_cost)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div id="action_zombies">
                <div class="zombies_text">
                    <strong class="nbr_zombies">'.plural($zone_zombies, 'zombie').'</strong> autour de vous !
                </div>
                <div class="zombies_visual">'. str_repeat('<span class="zombie">&#x1F9DF;</span>', $zone_zombies) .'</div>
                <div class="buttons_kill">'
                    . $buttons->kill_zombies($zone_zombies, 'kill_zombie', $ap_cost)
                    . $buttons->kill_zombies($zone_zombies, 'kill_mass_zombies') 
                    . $buttons->kill_zombies($zone_zombies, 'repel_zombie')
                    .'
                </div>
            </div>';
    }
    
    
    /**
     * Displays informations about the action points to the player
     * 
     * @param int $citizen_AP The amount of action points the player currently has
     * @param int $total_AP   The maximumum amount of action points of his speciality
     * @return string HTML
     */
    function block_movement_AP($citizen_AP, $total_AP) {
        
        // Draws the thunder icons to symbolize the action points
        $ap_bar = str_repeat('&#x26A1;', $citizen_AP);
        $ap_bar .= '<span style="opacity:0.3">'.str_repeat('&#x26A1;', $total_AP-$citizen_AP).'</span>';

        return '<div id="movement_ap">
                <a href="#popmove" id="actionpoints">
                    <p style="opacity:0.7">Points d\'action</p>
                    <p id="apBar">'.$ap_bar.'</p>
                </a>
            </div>';
    }
    
    
    /**
     * Displays the distance between the citizen and his city
     * 
     * @return string HTML
     */
    function block_distance() {
        
        return '
            <div id="block_distance">
                <div class="city_image"><img src="resources/img/free/city.svg" alt="ville"></div>'
                .'<span class="distance"></span> km
            </div>';
    }
    
    
    function block_actions_build()
    {
        
        $buttons = new HtmlButtons;
        $popup   = new HtmlPopup;
        $table = '';
        
//        // If there is a TENT in the zone, display the button to enter.
//        if ($city_size === 1) {
//            
//            $table .= '
//            <tr>
//                <td>'.$buttons->icon('destroy_city').'</td>
//                <td>'.$buttons->button('destroy_city', 'no_icon').'</td>
//            </tr>';
//        }
//        // If there is a CITY in the zone, display the button to enter.
//        elseif ($city_size > 0) {
//            
//            $table .= '<tr>
//                <td>'.$buttons->icon('enter_city').'</td>
//                <td>'.$buttons->button('enter_city', 'no_icon').'</td>
//            </tr>';
//        }
//        else {
            
            $table .= '<tr>
                <td>'.$buttons->icon('build_tent').'</td>
                <td>'.$buttons->button('build_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td><img style="height:1.4em" src="resources/img/free/city.svg" alt="&#127751;"></td>
                <td>'.$buttons->build_city('no_icon').'</td>
            </tr>
            <tr>
                <td><img style="height:2.1em" src="resources/img/copyrighted/tiles/greenjungle/11.png" alt="&#128508;"></td>
                <td>'.$buttons->button('build_outpost', 'no_icon').'</td>
            </tr>';            
//        }
//        
//        
//        // If there is a CRYPT in the zone, display the button to enter.
//        // TODO: replace this hardcoded ID (2 = crypt) by a generic method
//        if ($building_id === 2) {
//
//            $table .= '<tr>'
//                    . '<td>'.$buttons->icon('add_vault').'</td>'
//                    . '<td>'. $popup->link('popvault', 'Pouvoir cryptique').'</td>'
//                . '</tr>';
//        }
//        else {
            $table .= '<tr>
                <td>'.$buttons->icon('add_vault').'</td>
                <td>'.$buttons->button('add_vault', 'no_icon').'</td>
            </tr>';
//        }
        
        
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
        
        return '
            <div id="alert_control" class="block_alert cover_paddle">
                <div class="title">Bloqué par les zombies !</div>
                <div class="text">
                    Les zombies 
                    sont trop nombreux et vous empêchent de quitter la zone !
                    <a href="#popcontrol">[Pourquoi ?]</a>
                    <p>Vous pouvez tenter d\'attaquer ces putrides afin de dégager le passage...</p>
                    <p>
                        <input type="button" class="redbutton" 
                               onclick="toggleActionBlock(\'zombies\'); updateBlockAction(\'zombies\')" 
                               value="&#x1F9DF; Voir mes actions d\'attaque...">
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
            <div id="alert_tired" class="block_alert cover_paddle">
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
            <div id="alert_control_lost" class="block_alert">
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
    function block_bag_items($items_caracs, $bag_items, $max_bag_slots)
    {
        
        $htmlItem = new HtmlItem();
        $nbr_free_slots = $max_bag_slots - array_sum(array_values($bag_items));
        
        return '
            <div id="items_bag">
                <ul class="items_list">
                    ' . $htmlItem->items($bag_items, $items_caracs) . '
                    ' . $htmlItem->empty_slots($nbr_free_slots) . '
                </ul>
            </div>';
    }
    
    
    /**
     * List of the items on the ground of the citizen's zone 
     * 
     * @param array $items_caracs   Les caractéristiques de tous les items existants dans le jeu
     * @param array $ground_items   The items on the ground, structured as pairs
     *                              "item id => item amount"
     * @return string HTML
     */
    function block_ground_items($items_caracs, $ground_items, $coord_x, $coord_y)
    {
        
        $htmlItem = new HtmlItem();
        
        return '
            <div id="items_ground">
                <p class="greytext">
                    Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...
                </p>
                <ul class="items_list" data-coordx="'.$coord_x.'" data-coordy="'.$coord_y.'">
                    ' . $htmlItem->items($ground_items, $items_caracs) . '
                </ul>
            </div>';
    }
    
    
    /**
     * Form to change the ground type of a zone (sand, grass, lava...)
     * 
     * @param int $coord_x The X coordinate of the zone to modify
     * @param int $coord_y The Y coordinate of the zone to modify
     * @return string HTML
     */
    function block_edit_land($coord_x, $coord_y) {
        // The land aliases must exist in the Invazion API, otherwise 
        // the land type won't be changed.
        // If you add a land type here, don't forget to add the appropriate
        // CSS class in map.css (.ground_xxxx), otherwise the default tile (sand)
        // will be displayed.
        $lands = [
                'drywoods'  => ['name'  => 'Arbres morts',
                                'image' => 'desert/5.png'],
                'grass'     => ['name'  => 'Herbe',
                                'image' => 'greenjungle/3.png'],
                'lava'      => ['name'  => 'Lave',
                                'image' => 'volcanic/7.png'],
                'peeble'    => ['name'  => 'Cailloux',
                                'image' => 'desert/2.png'],
                'sand'      => ['name'  => 'Sable',
                                'image' => 'desert/9.png'],
                'water'     => ['name'  => 'Eau',
                                'image' => 'greenjungle/6.png'],
                ];
        
        $html_lands = '';
        foreach($lands as $alias=>$land) {
            $html_lands .= '<button type="submit" name="stuff" value="'.$alias.'">
                    <img src="resources/img/copyrighted/tiles/'.$land['image'].'" 
                         alt="'.$land['name'].'" title="'.$land['name'].'" height="60">
                </button>';
        }
        
        return '
            <p><strong>Changer le type de terrain :</strong></p>
            <form action="" method="GET" id="landform">
                '.$html_lands.'
                <br><br>
                <label>X <input type="number" name="coord_x" min="0" style="width:3em"
                                onClick="this.select()" value="{coordX}"></label>&nbsp;
                <label>Y <input type="number" name="coord_y" min="0" style="width:3em" 
                                onClick="this.select()" value="{coordY}"></label>
            </form>';
    }
    
    

    
    
    /**
     * Data about the player for javascript treatments (his coordinates...)
     * 
     * @param array $citizen The data about the citizen, as returned by the "citizen" API
     *                       (citizen ID, his coordinates on the map...)
     * @param int $max_action_points The maximum amount of AP stockable by the player
     * @return string HTML
     */
    function hidden_player_data($citizen, $max_action_points) {
        
        return '
            <section id="gameData">
                <div id="citizenId">'.$citizen['citizen_id'].'</div>
                <div id="citizenPseudo">'.$citizen['citizen_pseudo'].'</div>
                <div id="citizenCoordX">'.$citizen['coord_x'].'</div>
                <div id="citizenCoordY">'.$citizen['coord_y'].'</div>
                <div id="actionPoints">'.$citizen['action_points'].'</div>
                <div id="maxActionPoints">'.$max_action_points.'</div>
                <div id="mapId">'.$citizen['map_id'].'</div>
                <div id="cityId">'.$citizen['city_id'].'</div>
            </section>';
    }
}
