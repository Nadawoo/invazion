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
     * @param string $json_buildings_findable_items The JSON list of items findable in each building,
     *                               as returned by the "configs" API
     * @param string $json_items     The JSON string containing the informations 
     *                               about the items, as returned by the "configs"  API
     * @return string HTML
     */
    function json_configs($json_map, $json_buildings, $json_buildings_findable_items, $json_items) {
        
        return '<section id="configs">
                    <div class="map">
                    '.$json_map.'
                    </div>
                    <div class="buildings">
                    '.$json_buildings.'
                    </div>
                    <div class="buildings_findable_items">
                    '.$json_buildings_findable_items.'
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
//    function connection_bar($user_id, $citizen_id, $citizen_pseudo)
//    {
//        
//        $buttons = new HtmlButtons;
//        
//        // Si le joueur n'est pas connecté
//        if ($user_id === NULL) { 
//            
//            return $buttons->register() . $buttons->connect();
//        }
//        // Si le joueur est connecté mais n'a pas encore de créé de citoyen,
//        // on affiche son nom de joueur
//        elseif ($citizen_id === NULL) {
//            
//            return '<span class="bold">Connecté en tant que joueur n°' . $user_id . '</span>'
//                    . $buttons->disconnect();
//        }
//        // Si le joueur est connecté et a déjà créé son citoyen,
//        // on affiche le nom de ce citoyen
//        else {
//            
//            return 'Vous êtes le citoyen <strong>'.$citizen_pseudo.'</strong>'
//                   . $buttons->disconnect();
//        } 
//    }
    
    
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
                <a href="register" id="register" class="z-depth-5">Créer un&nbsp;compte</a>
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
                    <button class="redbutton" onclick="toggle(\'#specialities\');hide(\'capacities\');return false">Changer ma spécialité</button>
                    <button class="redbutton" onclick="toggle(\'#capacities\');hide(\'specialities\');return false">Améliorer une capacité</button>
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
     * Bar above the map showing the countdown before the next attack, the current day...
     * @param  int $map_id The ID of the map on which the player is
     * @param  int $day The number of days since the game start
     * @return string HTML
     */
    function attack_bar($map_id, $day)
    {
        
        $buttons = new HtmlButtons();
        
        return '
            <div>
                <a id="appMenu" class="sidenav-trigger" data-target="slide-out">
                    <span class="icon"><i class="material-icons">menu</i></span>
                </a>
            </div>
            <div id="tuto_dayclock">
                <a href="#popdayclock" id="dayclock">
                    Jour <span style="font-size:1.6em;font-weight:bold;">'.(int)$day.'</span>
                </a>
            </div>
            <p id="messageEndCycle" style="display:none;margin:0"></p>
            <div class="right">
                <a id="showWall" title="Communications">
                    <span class="icon"><i class="material-icons">sms</i></span>
                    <span class="text">Communications</span>
                </a>
                
                <!--
                <a id="notifsButton" title="Notifications">&#x1F514;</a>
                <div id="notifsBlock">
                    <a id="notifsClose">X</a>
                    <div id="notifsList"><div style="text-align:center;padding:0.8em;color:grey">Chargement en cours...</div></div>
                </div>
                -->
                
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
                    <div class="userLabel z-depth-2">
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
        
        $htmlItem = new HtmlItem();
        
        $result = !empty($zone['items'])
                    ? $htmlItem->items($zone['items'], $items_caracs)
                    : '<div class="greytext" style="margin-top:0.5rem">
                       Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...
                       </div>';
        
        return $result;
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
    
    
    /**
     * Displays informations about the type of land of the current zone
     * 
     * @return string HTML
     */
    function block_landtype() {
        
        return '
            <div id="block_landtype"></div>';
    }
    
    
    function block_actions_build()
    {
        
        $buttons = new HtmlButtons;
        $popup   = new HtmlPopup;
        $table = '<strong>Construire</strong>';
        
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
    
    
    function block_zombie_powers() {
        
        return '<p><strong>Pouvoirs (joueur zombie)</strong></p>
            <p><em>[Ces fonctions sont en cours de développement, pas encore actives]</em></p>
            <img src="resources\img\copyrighted\wolf.png" alt="&#128200;" height="32" width="32"> Augmenter l\'attaque quotidienne <abbr title="Augmente le nombre de zombies qui attaqueront la ville lors de la prochaine attaque de fin de cycle.">[?]</abbr><br>
            <img src="resources\img\motiontwin\zombie6.gif" alt="&#129503;" height="32" width="32"> Ajouter des zombies sur la carte <abbr title="Ajoute aléatoirement des zombies sur la carte du jeu.">[?]</abbr><br>
            <img src="resources\img\motiontwin\zombie2.gif" alt="&#129503;" height="32" width="32"> Punir l\'extermination <abbr title="Chaque fois qu\'un humain tue un zombie sur une zone, deux nouveaux zombies apparaissent sur une zone voisine. L\'effet dure pendant 1 cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\chemistry.png" alt="&#9763;&#65039;" height="32" width="32"> Empoisonner l\'eau <abbr title="Les rations d\'eau consommées par les humains ne leur restaurent que la moitié des points d\'action normaux. L\'effet dure pendant 1 cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\city_well.png" alt="&#128167;" height="32" width="32"> Percer le puits <abbr title="Chaque fois qu\'un humain puise 1 ration d\'eau, 2 rations disparaissent du puits. L\'effet dure pendant 1 cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\wound.png" alt="&#129656;" height="32" width="32"> Blesser un humain <abbr title="50% de risque qu\'un humain, choisi aléatoirement, reçoive une blessure à la fin du cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\oracle.png" alt="&#128374;&#65039;" height="32" width="32"> Obscurcir la carte <abbr title="Masque temporairement toutes les zones de la carte, comme si elle n\'avait jamais été explorée. L\'effet dure pendant 1 cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\forging.png" alt="&#128295;" height="32" width="32"> Empêcher les assemblages <abbr title="Les humains ne peuvent plus fabriquer d\'objets à partir de composants. L\'effet dure pendant 1 cycle.">[?]</abbr><br>
            <img src="resources\img\copyrighted\city_door.png" alt="" height="32" width="32"> Saboter la porte <abbr title="Manipuler la porte de la ville coûte 2 points d\'action au lieu d\'un seul.">[?]</abbr>
            ';
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
                    Les zombies sont trop nombreux et vous empêchent de quitter la zone !
                    <br><a href="#popcontrol">[Pourquoi ?]</a>
                    <p>
                        <button class="bluebutton" 
                               onclick="toggleActionBlock(\'zombies\'); updateBlockAction(\'zombies\')" 
                               >Éliminer des zombies <i class="material-icons">chevron_right</i></button>
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
    
    
    function bagbar($items_caracs, $bag_items, $max_bag_slots) {
        
        $htmlItem = new HtmlItem();
        $nbr_free_slots = $max_bag_slots - array_sum(array_values($bag_items));
        
        return '
            <fieldset id="bagbar">
                <div class="block_icon" onclick="toggleBag()">
                    <div class="icon">&#x1F392;</div>
                    <div class="name">Sac</div>
                </div>
                <ul class="items_list">
                    ' . $htmlItem->items($bag_items, $items_caracs) . '
                    ' . $htmlItem->empty_slots($nbr_free_slots) . '
                </ul>
                <button title="Afficher/masquer les autres objets du sac" class="show_more" onclick="toggleBag()">
                    <i class="material-icons">chevron_right</i>
                </button>
            </fieldset>';
    }
    
    
    /**
     * List of the items on the ground of the citizen's zone 
     * 
     * @return string HTML
     */
    function block_ground_items($coord_x, $coord_y)
    {
        
        return '
            <div id="items_ground">
                <p class="greytext">
                    Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...
                </p>
                <ul class="items_list" data-coordx="'.$coord_x.'" data-coordy="'.$coord_y.'"></ul>
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
                'sand'      => ['name'  => 'Sable',
                                'image' => 'desert/9.png'],
                'peeble'    => ['name'  => 'Cailloux',
                                'image' => 'desert/2.png'],
                'bigpeeble' => ['name'  => 'Cailloux',
                                'image' => 'desert/1.png'],
                'desertpound' => ['name'  => 'Cailloux',
                                'image' => 'desert/4.png'],
                'drywoods'  => ['name'  => 'Arbres morts',
                                'image' => 'desert/5.png'],
                'desertmountains' => ['name'  => 'Montagnes',
                                'image' => 'desert/8.png'],
                'grass'     => ['name'  => 'Herbe',
                                'image' => 'greenjungle/3.png'],
                'greenwoods' => ['name'  => 'Arbres verts',
                                 'image' => 'greenjungle/4.png'],
                'water'     => ['name'  => 'Eau',
                                'image' => 'greenjungle/6.png'],
                'lava'      => ['name'  => 'Lave',
                                'image' => 'volcanic/7.png'],
                ];
        
        $html_lands = '';
        foreach($lands as $alias=>$land) {
            $html_lands .= '<button type="submit" name="stuff" value="'.$alias.'">
                    <img src="resources/img/copyrighted/tiles/'.$land['image'].'" 
                         alt="'.$land['name'].'" title="'.$land['name'].'" height="60">
                </button>';
        }
        
        return '
            <p><strong>Changer le type de terrain</strong></p>
            <form action="" method="GET" id="landform">
                '.$html_lands.'
                <br>
                Zone à modifier :
                <label>X <input type="number" name="coord_x" min="0" style="width:3em"
                                onClick="this.select()" value="{coordX}"></label>&nbsp;
                <label>Y <input type="number" name="coord_y" min="0" style="width:3em" 
                                onClick="this.select()" value="{coordY}"></label>
                <br>
                et un rayon de <label><input type="number" name="radius" min="0" max="2" style="width:3em" 
                                onClick="this.select()" value="0"></label> zones alentour
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
        
        // #23 = ID of the item "action points" in the citizen's bag
        $citizen_action_points = isset($citizen['bag_items'][23]) ? $citizen['bag_items'][23] : 0;
        
        return '
            <section id="gameData">
                <div id="citizenId">'.$citizen['citizen_id'].'</div>
                <div id="citizenPseudo">'.$citizen['citizen_pseudo'].'</div>
                <div id="citizenCoordX">'.$citizen['coord_x'].'</div>
                <div id="citizenCoordY">'.$citizen['coord_y'].'</div>
                <div id="actionPoints">'.$citizen_action_points.'</div>
                <div id="maxActionPoints">'.$max_action_points.'</div>
                <div id="mapId">'.$citizen['map_id'].'</div>
                <div id="cityId">'.$citizen['city_id'].'</div>
            </section>';
    }
    
    
    /**
     * Lateral bar above the map to change its view mode
     * (to zoom, choose the layers...)
     * 
     * @return string HTML
     */
    function block_map_navigation() {
        
        $buttons = new HtmlButtons();
        
        return '
            <form action="#" id="zoom_form">
                <button id="zoomMapStepIn" title="Zoomer la carte"><i class="material-icons small">add</i></button>
                <p class="range-field">
                    <input id="zoom_range" type="range" value="100" min="70" max="220" step="30">
                </p>
                <hr onclick="toggleZoomRange()">
                <button id="zoomMapStepOut" title="Dézoomer la carte"><i class="material-icons small">remove</i></button>
            </form>
            
            <button onclick="centerMapOnMe()" title="Centrer sur ma zone">
                <img src="resources/img/icons8/mylocation-48.png" height="28" alt="Cible ma position">
            </button>
            
            <button onclick="toggle(\'#mapRadarMenu\')" title="Vue satellite">
                <span style="font-size:50%;margin-top:-0.2em;">&#x1F6F0;&#xFE0F;</span>
            </button>
            
            <ul id="mapRadarMenu" class="hidden">
                <li onclick="resetMapView();toggleMapMarker()">&#x1F5FA;&#xFE0F; Carte réelle</li>
                <li onclick="toggleMapNeighborhoodView()" title="Afficher des informations sur les bâtiments">&#x1F3E2; Bâtiments</li>
                <li onclick="resetMapView();toggleMapZombiesView();toggleMapItemMarker(106)"  title="Voir les zombies sur la carte">&nbsp;<img src="resources/img/motiontwin/zombie.gif" alt="&#x1F9DF;">&nbsp; Zombies</li>
                <li onclick="resetMapView();toggleMapItemsView();toggleMapMarker()" title="Voir les objets au sol sur la carte">&#x1F392; Objets</li>
            </ul>
            
            <button><a href="#popsmartphone" style="font-size:55%">&#128241;</a></button>
            
            <button>'.$buttons->refresh().'</button>
        
            <!-- 
            <button id="launchTutorial"><i class="material-icons small grey-text text-darken-2">help</i></button>
            -->';        
    }
}
