<?php
require_once '../core/controller/autoload.php';
safely_require('/core/model/set_default_variables.php');
safely_require('/core/controller/official_server_root.php');
safely_require('/core/controller/get_game_day.php');
safely_require('/core/controller/SortGameData.php');
safely_require('/core/ZombLib.php');

$api                = new ZombLib(official_server_root().'/api');
$layout             = new HtmlLayout();
$map                = new HtmlMap();
$my_zone            = new HtmlMyZone();
$enclosure          = new HtmlCityEnclosure();
$buttons            = new HtmlButtons();
$paddle             = new HtmlMovementPaddle();
$phone              = new HtmlSmartphone();
$wall               = new HtmlWall();
$popup              = new HtmlPopup();
$sort               = new SortGameData();
$zone               = set_default_variables('zone');
$citizen            = set_default_variables('citizen');
$city_fellows       = [];
$zone_fellows       = [];
$healing_items      = [];
$msg_popup          = NULL;
$msg_move           = '';
$is_custom_popup_visible = false;


/**
 * Executes the actions asked by the player (moving, digging...)
 */
if (!empty($_POST)) {
    
    $api_name       = filter_input(INPUT_POST, 'api_name', FILTER_SANITIZE_STRING);
    $action_post    = filter_input(INPUT_POST, 'action',   FILTER_SANITIZE_STRING);
    $params_post    = filter_input(INPUT_POST, 'params',   FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
    $method         = filter_input(INPUT_POST, 'method',   FILTER_SANITIZE_STRING);
   
    // Calls the API ont the central server of InvaZion
    $api_result = $api->call_api($api_name, $action_post, $params_post, $method);
    
    if (in_array($action_post, ['move', 'drop', 'bigfight'])
        or ($action_post === 'fight' and !isset($params_post['item_id']))
        or ($api_name === 'zone' and $action_post === 'pickup')
        ) {
        // The result of these actions is displayed under the movement paddle
        $msg_move  = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
    }
    else {
        // The result of all the other actions is displayed in a pop-up
        $msg_popup = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
    }
}


/**
 * Get the data to build the interface
 * Keep this *after* the execution of actions above, otherwise the last action
 * wouldn't be taken in account without refreshing (citizen not moved...)
 */
// If the player is connected *and* his token is not expired
if ($api->user_seems_connected() === true) {
    // Get the player data from the API
    $api_me = $api->call_api('me', 'get');
    
    if ($api_me['metas']['error_code'] === 'success') {
        $citizen = $api_me['datas'];
    }
    elseif ($api_me['metas']['error_code'] === 'citizen_not_created') {
        $citizen['user_id'] = $api_me['datas']['user_id'];
    }
    else {
        $msg_move = '<p class="'.$api_me['metas']['error_class'].'">'.$api_me['metas']['error_message'].'</p>';
    }
}

// Get the game data by calling the APIs
$citizens           = $api->call_api('citizens', 'get', ['map_id'=>$citizen['map_id']])['datas'];
$citizens_by_coord  = $sort->sort_citizens_by_coord($citizens);
$maps               = $api->call_api('maps', 'get', ['map_id'=>$citizen['map_id']])['datas'];
$configs            = $api->call_api('configs', 'get', ['map_id'=>$citizen['map_id']])['datas'];
$specialities       = $configs['specialities'];
$speciality_caracs  = $specialities[$citizen['speciality']];
$map->set_config_buildings($configs['buildings']);


// If the player is connected and has already created his citizen
if ($citizen['citizen_id'] !== NULL) {
    
    $zone_fellows       = $citizens_by_coord[$citizen['coord_x'].'_'.$citizen['coord_y']];
    $zone               = $maps['zones'][$citizen['coord_x'].'_'.$citizen['coord_y']];
    $healing_items      = $sort->filter_bag_items('healing_wound', $configs['items'], $citizen['bag_items']);
    
    // If the citizen is inside a city
    if ($citizen['inside_city_id'] !== NULL) {
        // Gets the characteristics of this city (well, storage...)
        $cities_data  = $api->call_api('cities', 'get', ['map_id'=>$citizen['map_id']])['datas'];
        $city_data = $cities_data[$citizen['inside_city_id']];
        // Gets the citizens linked to this city
        $city_fellows = $sort->get_child_citizens($city_data['child_cities_ids'], $cities_data, $citizens);
        // TRUE if the player has connected his habitation to this city
        $is_citizen_home_connected = in_array($citizen['city_id'], $city_data['child_cities_ids']) ? true : false;
    }
    
    // Show the ending popup when the citizen is dead
    if($citizen['unvalidated_death_cause'] !== null) {    
        $msg_popup = $popup->popdeath($citizen['unvalidated_death_cause']);    
        $is_custom_popup_visible = true;
    }
    // If there is a car (ID=1) in the zone
    // TODO: don't hardcode this ID
    elseif($zone['building_id'] == 1) {
        $msg_popup = $popup->popcar($msg_popup);
    }
}


/**
 * HTML elements to build the interface
 */
$html = [
    // Data about the player for javascript treatments (his coordinates...)
    'hidden_player_data' => $layout->hidden_player_data($citizen, $speciality_caracs['action_points']),
    // The unvariable data of the game (buildings names...)
    'json_configs'      => $layout->json_configs(json_encode($configs['buildings']),
                                                 json_encode($configs['items'])),
    // Assembling the HTML for the map
    'map' => $map->hexagonal_map($maps['map_width'], $maps['map_height'], $maps['zones'], $citizen, $maps['next_attack_hour']),
    'map_citizens'      => $layout->map_citizens($citizens),
    'attack_bar'        => $layout->attack_bar($citizen['map_id'], get_game_day($citizen['last_death'])),
    // Contents of the round action buttons at the right of the map
    'actions_build'     => $layout->block_actions_build($zone['city_size'], $zone['building_id']),
    'actions_bag'       => $layout->block_actions_bag($configs['items'], $citizen['bag_items']),
    'actions_context'   => $layout->block_actions_context($zone['city_size'], $zone['building_id'], $configs['buildings']),
    'actions_zombies'   => $layout->block_actions_zombies($zone['zombies'], $configs['map']['killing_zombie_cost']),
    'edit_land'         => $layout->block_edit_land($citizen['coord_x'], $citizen['coord_y']),
    'zone_items'        => $layout->block_zone_items($configs['items'], $zone),
    'zone_items_template' => $layout->block_zone_item_template(),
    'bag_items'         => $layout->block_bag_items($configs['items'], $citizen['citizen_id'], $citizen['bag_items'], $citizen['bag_size']),
    'zone_fellows_template' => $layout->block_zone_fellow_template(),
    // Smartphone at the right of the map
    'smartphone'        => $phone->smartphone($maps['map_width'], $maps['map_height'], $citizen, $speciality_caracs, $zone),
    ];


unset($maps, $citizens, $citizens_by_coord);
?>



<?php
/**
 * Start of the HTML page
 */
echo $layout->page_header();
echo $html['hidden_player_data'];
echo $html['json_configs'];

// Textes des pop-up
// TODO : ne pas charger toutes les textes dans le code, seulement celui utile
echo $popup->predefined('popvault',   '');
echo $popup->predefined('popwounded', '', ['citizen_id'=>$citizen['citizen_id'], 'healing_items'=>$healing_items]);
echo $popup->predefined('popcontrol', 'Aide : le contrôle de zone');
echo $popup->predefined('popmove', 'Aide : les déplacements', 
                        ['moving_cost_no_zombies' => $configs['map']['moving_cost_no_zombies'], 
                         'moving_cost_zombies'    => $configs['map']['moving_cost_zombies']
                        ]);
echo $popup->predefined('popattack', 'Aide : l\'attaque zombie quotidienne');
echo $popup->template_popbuilding($msg_popup);
// Generic pop-up describing the result of an action
echo $popup->customised('popsuccess', '', $msg_popup, $is_custom_popup_visible);
?>
    
    <div id="connectionbar">
        <?php echo $layout->connection_bar($citizen['user_id'], $citizen['citizen_id'], $citizen['citizen_pseudo']); ?>
    </div>
    
    <p id="GameDemo" class="aside">L'interface est volontairement minimaliste pour le moment. 
        La priorité du projet est de mettre à disposition les API (le moteur du jeu) 
        à partir desquelles toute personne  sachant coder peut développer 
        sa propre interface graphique. <a href="https://invazion.nadazone.fr/project">[En savoir plus]</a>
    </p>
    
    <div id="gamebar">
        <div id="Outside" style="width:12%">&nbsp;</div>
        <a id="notifsButton">&#x1F514; <strong>Notifications</strong></a>
        <?php echo $buttons->refresh() ?>
    </div>
    <div id="notifsBlock">
        <a id="notifsClose">X</a>
        <div id="notifsList"><div style="text-align:center;padding:0.8em;color:grey">Chargement en cours...</div></div>
    </div>
    
    
    <?php
    // Asks for chosing a citizen speciality (builder, digger...)
    if ($citizen['can_change_speciality'] === 1) {
        ?>
        
        <fieldset id="citizen_caracs">
            <legend>Action du jour</legend>
            <?php echo $layout->block_speciality_choice($specialities) ?>
        </fieldset>
        
        <?php
    } ?>
    
    
<div id="game_container">
    
    <?php
    // If the citizen is inside a city, display the city enclosure over the map
    // (well, storage, constructions...)
    if ($citizen['inside_city_id'] !== NULL) {
        
        echo '
            <div id="city_container">
                <nav id="city_menu">
                    '.$enclosure->city_menu($citizen['map_id'], $city_data['parent_city_id'], $citizen['city_id']).'
                    '.$enclosure->city_submenu($city_data['city_type_id'], $city_data['parent_city_id'], $is_citizen_home_connected).'
                </nav>
                <div id="city_contents">
                    <div id="home_house" class="city_row">
                        '. $enclosure->block_home() .'
                    </div>
                    <div id="home_storage" class="city_row">
                        <div id="blockHomeStorage" class="city_block">
                            '. $enclosure->block_home_storage($html['zone_items']) .'
                        </div>
                        '. $enclosure->block_bag($html['bag_items']) .'
                    </div>
                    <div id="home_build" class="city_row">
                        '. $enclosure->block_constructions($configs['constructions_home'], $configs['items'], $city_data['constructions'], 
                                                           $city_data['total_defenses'], $zone['items']) .'
                    </div>
                    <div id="city_fellows" class="city_row">
                        '. $enclosure->block_fellows_list($city_fellows, $specialities) .'
                        '. $enclosure->block_fellows_homes($city_fellows, $specialities, $city_data['coord_x'], $city_data['coord_y']) .'
                    </div>
                    <div id="city_storage" class="city_row">
                        <div id="blockCityStorage" class="city_block">
                            '. $enclosure->block_city_storage($html['zone_items']) .'
                        </div>
                        '. $enclosure->block_bag($html['bag_items']) .'
                    </div>
                    <div id="city_well" class="city_row">
                        '. $enclosure->block_well($city_data['well_current_water']) .'
                        '. $enclosure->block_bag($html['bag_items']) .'
                    </div>
                    <div id="city_craft" class="city_row">
                        '. $enclosure->block_workshop($zone['items'], $configs['items']) .'
                    </div>
                    <div id="city_build" class="city_row">
                        '. $enclosure->block_constructions($configs['constructions'], $configs['items'], $city_data['constructions'], 
                                                           $city_data['total_defenses'], $zone['items']) .'
                    </div>
                    <div id="city_door" class="city_row">
                        '. $enclosure->block_city_door($city_data['is_door_closed']) .'
                    </div>
                    <div id="explore" class="city_row">
                        '. $enclosure->block_explore() .'
                    </div>
                </div>
            </div>';

        // Dark overlay to blur the map under the city interface
        echo '<div id="dark_background"></div>';
     } ?>
    
    
    <!-- Let this bar *before* the round action buttons if you want them 
         to go *below* the bar on small screens -->
    <a href="#popattack" id="attack_bar">
        <?php echo $html['attack_bar'] ?>
    </a>    
    
    <!-- The map -->
    
    <div id="map">
        
        <div id="map_header">
            <span onclick="toggleMapItems()">Objets sur la carte</span>
        </div>
        
        <div id="backToMap">
            <span id="displayMyZone">Afficher ma zone</span>
            <span id="hideMyZone" class="hidden">Afficher la carte</span>
        </div>
        
        <?php 
        // Display the zone where the player is       
        $my_zone->set_nbr_zombies($zone['zombies']);
        $my_zone->set_nbr_items($zone['items']);
        $my_zone->set_citizens_in_zone($zone_fellows);
        $my_zone->set_citizens_in_city($city_fellows);
        $my_zone->set_city_size($zone['city_size']);
        $my_zone->set_citizen_pseudo($citizen['citizen_pseudo']);
        echo $my_zone->main();
        ?>
        
        <?php echo $html['map']; ?>
        
    </div>
    
    <div id="column_right">
        
        <div>
            <?php
            if ($citizen['user_id'] === NULL) {        
                // If the player is not connected, display the connection panel
                echo $layout->block_connect();
            }
            elseif ($citizen['citizen_id'] === NULL) {         
                // If the player is connected but has not created his citizen yet,
                // display the panel for creating a citizen
                echo $layout->block_create_citizen();
            }
            ?>
        </div>
        
        <div id="round_actions">
            <?php
            echo  $buttons->button_round('move', ($zone['controlpoints_zombies']-$zone['controlpoints_citizens']))
                . $buttons->button_round('dig', array_sum((array)$zone['items']), (bool)$citizen['can_dig'])
                . $buttons->button_round('zombies', $zone['zombies'], (bool)$zone['zombies'])
                . $buttons->button_round('citizens', null, null)
                . $buttons->button_round('build');
            // Warn if wounded
            echo $layout->block_alert_wounded((bool)$citizen['is_wounded']);
            ?>
        </div>
        
        <div id="actions">
            <fieldset id="block_move">
                <?php
                if ($zone['controlpoints_citizens'] < $zone['controlpoints_zombies'] and time() < strtotime($zone['date_control_end'])) {
                    echo $layout->block_alert_escape(strtotime($zone['date_control_end']));
                }
                elseif (($zone['zombies'] === 0 and $citizen['action_points'] < $configs['map']['moving_cost_no_zombies'])
                     or ($zone['zombies'] === 0 and $citizen['action_points'] === 0 and $configs['map']['moving_cost_no_zombies'] > 0)
                     or ($zone['zombies'] >   0 and $citizen['action_points'] < $configs['map']['moving_cost_zombies'])
                    ) {                    
                    echo $layout->block_alert_tired($zone['zombies']);
                }
                
                echo $layout->block_alert_control($zone['zombies']);
                
                echo '
                <div id="column_move">'
                    .$paddle->paddle($citizen['coord_x'], $citizen['coord_y'])
                    .$layout->block_distance()
                    .'<div class="buildingName"></div>'
                    .$buttons->button('enter_city', 'no_icon')
                    .$buttons->button('destroy_city', 'no_icon').'
                    <a href="#popsuccess" id="button_explore" class="redbutton center">Explorer : <br><span class="building_name"></span></a>
                </div>';
                
                echo $layout->block_movement_AP($citizen['action_points'], $speciality_caracs['action_points'], $zone['zombies'], 
                                                $configs['map']['moving_cost_no_zombies'], $configs['map']['moving_cost_zombies']);
                
                echo '<br><br><br><br><br>
                    <strong>Objectifs</strong><br>
                    Construire un abri avant minuit<br>
                    Chercher des ressources<br>
                    Rejoindre l\'abri avant minuit<br>
                    Améliorer les défenses<br>
                    Fermer les portes de l\'abri avant l\'attaque<br>
                    <br>
                    <br>
                    <strong>Mes caractéristiques</strong><br>
                    Spécialité : '.$speciality_caracs['name'].'<br>
                    Vision niv. '.$citizen['vision'].'<br>
                    Camouflage niv. '.$citizen['camouflage'].'<br>
                    Blessé : '.($citizen['is_wounded'] === 1 ? 'oui' : 'non');
                
                // Special actions depending of the zone (go into a crypt, a city...)
                echo $html['actions_context'];
                ?>
            </fieldset>
            
            <fieldset id="block_dig">
                <?php 
                echo $buttons->button('dig', false, '', (bool)$citizen['can_dig']).'<br>';
                ?>
                
                &#x1F4BC; <strong>Déposer un objet de mon sac :</strong>
                    <?php echo $html['bag_items'] ?>
                    
                &#x270B;&#x1F3FC; <strong>Ramasser un objet au sol :</strong>
                    <?php echo $html['zone_items_template'] ?>
                    <form name="items_ground" method="post" action="#Outside">
                        <p class="greytext">
                            Aucun objet au sol pour l'instant. Vous allez devoir fouiller...
                        </p>
                        <input type="hidden" name="api_name" value="zone">
                        <input type="hidden" name="action" value="pickup">
                        <ul class="items_list" style="margin-left:1.5rem;"
                            data-coordx="" data-coordy=""></ul>
                    </form>
            </fieldset>

            <fieldset id="block_zombies">
                <?php
                echo $html['actions_zombies'];
                echo '<br>'.$html['actions_bag'];
                ?>                
            </fieldset>

            <fieldset id="block_build">
                <?php 
                echo $html['actions_build']
                     .'<br>'
                     . $html['edit_land']
                ?>
            </fieldset>

            <fieldset id="block_citizens">                
                <p class="greytext">Personne à proximité. Vous êtes seul au milieu 
                    de cette zone désertique...</p>
                
                <?php echo $html['zone_fellows_template'] ?>
                <ol class="citizens" data-coordx="" data-coordy=""></ol>
            </fieldset>
        </div>
        
        <div id="message_move"><?php echo $msg_move ?></div>
        
    </div>
 
    
    <div id="floating_wall">
        <?php echo $wall->wall($html['smartphone']) ?>
    </div>
    
</div>
    
<div id="rules">
    <br>
    <br>
    <br>
    <br>

    <?php
    echo $buttons->button('start_game');
    echo $buttons->button('add_mass_zombies');
    echo $buttons->button('end_cycle');
    ?>
    <br>
    <a href="edit" title="Paramétrez les objets disponibles dans le jeu (bêta)">Créer des objets</a>
    <br>
    
    <br>
    
    <form method="post" action="<?php echo official_server_root().'/apis-list' ?>">
        <input type="hidden" name="token" value="<?php echo $api->get_token() ?>" />
        <input type="submit" value="Debugage"  class="formlink" style="color:grey"
               title="Lien spécial pour le débugage - Ignorez-le sauf si un administrateur du jeu vous le demande." />
    </form>
    
    
    <h3 id="Citizens"><a href="#Citizens">&Hat;</a>&nbsp;Liste des citoyens</h3>
    
    <?php echo $html['map_citizens'] ?>
    
    <hr>
    
    <p>Merci à <a href="http://twinoid.com/user/7912453" target="_blank" rel="noopener"><strong>Ross</strong></a> 
        pour son image de ville <img src="resources/img/free/city.png" alt="city.png">
    </p>
    
    <hr>
    
    <h2 id="Help">Mémo des règles</h2>
    <strong>Se déplacer</strong>
    <ul class="expanded">
        <li>Le contrôle d'une zone dépend du nombre d'humains et de zombies présents
            sur la case&nbsp;: 10 points par humain et 1 point par zombie.</li>
        <li>Les humains ne peuvent pas quitter une zone dont ils n'ont pas le contrôle 
            (forces zombies supérieures aux forces humaines). Il faudra tuer des zombies 
            jusqu'à ce que le rapport de force s'inverse.</li>
        <li>Se déplacer sur une case qui contient un zombie ou plus coûte 1 point d'action,
            même si les humains en ont le contrôle de la zone. Si vous n'avez plus de point d'action,
            vous ne pouvez pas quitter la zone.</li>
        <li>S'il n'y a aucun zombie sur la case, le déplacement ne coûte aucun point d'action.</li>
    </ul>
    <strong>L'attaque de la horde</strong>
    <ul class="expanded">
        <li>La horde progresse chaque jour du nord vers le sud, 
            à&nbsp;raison d'une ligne par&nbsp;heure.</li>
        <li>Les citoyens qui ne sont pas abrités dans une ville ou une tente
            au moment où la horde passe sur leur zone meurent. Ils se réincarnent 
            dans la zone 0:0 (en haut à gauche).</li>
        <li>Les tentes sont à usage unique&nbsp;: elles protègent les citoyens
            qui sont à l'intérieur, mais elles sont détruites par l'attaque.</li>
    </ul>

</div>
    
<?php echo $layout->page_footer();
