<?php
require_once '../core/controller/autoload.php';
safely_require('/core/model/set_default_variables.php');
safely_require('/core/controller/official_server_root.php');
safely_require('/core/controller/get_game_day.php');
safely_require('/core/controller/get_well_current_water.php');
safely_require('/core/controller/SortGameData.php');
safely_require('/core/ZombLib.php');

$api                = new ZombLib(official_server_root().'/api');
$layout             = new HtmlLayout();
$actionBlocks       = new HtmlActionBlocks();
$actionCards        = new HtmlActionCards();
$map                = new HtmlMap();
$legends            = new HtmlMapLegends();
$statusbar          = new HtmlStatusBar();
$enclosure          = new HtmlCityEnclosure();
$constructionCards  = new HtmlCityConstructionCards();
$cityIso            = new HtmlCityIso();
$buttons            = new HtmlButtons();
$paddle             = new HtmlMovementPaddle();
$phone              = new HtmlSmartphone();
$wall               = new HtmlWall();
$popup              = new HtmlPopup();
$tutorial           = new HtmlTutorial();
$htmlItem           = new HtmlItem();
$htmlPaths          = new HtmlPaths();
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
    
    // When we take control over a bot-citizen, update the cookie storing the token
    if($action_post === 'switch_citizen' and $api_result['metas']['error_code'] === 'success') {
        $api->update_cookie('token', $api_result['datas']['token']);
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
$current_cycle      = $maps['current_cycle'];
$map->set_config_buildings($configs['buildings']);


// If the player is connected and has already created his citizen
if ($citizen['citizen_id'] !== null) {
    
    $zone_fellows       = $citizens_by_coord[$citizen['coord_x'].'_'.$citizen['coord_y']];
    $zone               = $maps['zones'][$citizen['coord_x'].'_'.$citizen['coord_y']];
    $healing_items      = $sort->filter_bag_items('healing_wound', $configs['items'], $citizen['bag_items']);
    
    // If the citizen is inside a city
    if ($citizen['inside_city_id'] !== null) {
        // Gets the characteristics of this city (well, storage...)
        $cities_data  = $api->call_api('cities', 'get', ['map_id'=>$citizen['map_id']])['datas'];
        $items_inside_constructions = $api->call_api('items', 'get', [])['datas'];
        $city_data = $cities_data[$citizen['inside_city_id']];
        // Gets the citizens linked to this city
        $city_fellows = $sort->get_child_citizens($citizen['city_id'], $city_data['child_cities_ids'], $cities_data, $citizens);
        // TRUE if the player has connected his habitation to this city
        $is_citizen_home_connected = in_array($citizen['city_id'], $city_data['child_cities_ids']) ? true : false;
        
        // Keep only the game's buildings related to the city (ID #12)
        $city_buildings_caracs = $sort->filter_buildings_by_parent($configs['buildings'], $configs['map']['city_buildings_set_id']);
        // Idem for the personal house (ID #13)
        $home_buildings_caracs = $sort->filter_buildings_by_parent($configs['buildings'], $configs['map']['home_buildings_set_id']);
        // Get the ID of the buildings already terminated (not in progress)
        $completed_buildings_ids = $sort->get_completed_buildings_ids($city_data['constructions']);
        // Get the ID of the well (type #15) constructed in the city
        $well_construction_id = $sort->get_construction_id_from_type($city_data['constructions'], 15);
        $well_current_water = get_well_current_water($items_inside_constructions, $well_construction_id);
        // Amount of items in the main city storage (bank)
        $nbr_ground_items = array_sum($zone['items']);
        
        $cityIso->set_city_well($well_current_water);
        $cityIso->set_city_storage($nbr_ground_items);
    }
    
    // Show the ending popup when the citizen is dead
    if($citizen['unvalidated_death_cause'] !== null) {    
        $msg_popup = $popup->popdeath($citizen['unvalidated_death_cause']);    
        $is_custom_popup_visible = true;
    }
    // If there is a car (ID=1) in the zone
    // TODO: don't hardcode this ID
//    elseif($zone['building_id'] == 1) {
//        $msg_popup = $popup->popcar($msg_popup);
//    }
}


/**
 * HTML elements to build the interface
 */
$html = [
    // Data about the player for javascript treatments (his coordinates...)
    'hidden_player_data' => $layout->hidden_player_data($citizen, $speciality_caracs['action_points']),
    // The unvariable data of the game (buildings names...)
    'json_configs'      => $layout->json_configs(json_encode($configs['map']),
                                                 json_encode($configs['buildings']),
                                                 json_encode($configs['buildings_findable_items']),
                                                 json_encode($configs['items'])),
    // Assembling the HTML for the map
    'map' => $map->hexagonal_map($maps['map_width'], $maps['map_height'], $maps['zones'], $citizen, $maps['next_attack_hour']),
    'map_citizens'      => $layout->map_citizens($citizens),
    'attack_bar'        => $layout->attack_bar($citizen['map_id'], $configs['map']['current_cycle']),
    // Contents of the round action buttons at the right of the map
    'ground_items'      => $layout->block_ground_items($citizen['coord_x'], $citizen['coord_y']),
    // TODO: merge_zone_items with ground_items
    'zone_items'        => $layout->block_zone_items($configs['items'], $zone),
    'bag_items'         => $layout->block_bag_items($configs['items'], $citizen['bag_items'], $citizen['bag_size']),
    // Smartphone at the right of the map
    'smartphone'        => $phone->smartphone($maps['map_width'], $maps['map_height'], $citizen, $speciality_caracs, $zone),
    ];


unset($maps, $citizens, $citizens_by_coord);
?>



<?php
/**
 * Start of the HTML page
 */
echo $layout->page_header($citizen['user_id'], $citizen['citizen_id'], $citizen['citizen_pseudo']);
echo $html['hidden_player_data'];
echo $html['json_configs'];

//echo $tutorial->all_steps();
?>

<section id="popups">
    <?php echo $popup->all_popups($msg_popup, $citizen['map_id'], $citizen['citizen_id'], 
                       $configs['map'], $speciality_caracs,
                       $healing_items, $html['smartphone'], $is_custom_popup_visible) 
    ?>
</section>

<section id="templates">
    <?php
    echo $htmlItem->item_template()
       . $htmlPaths->path_template()
       . $htmlPaths->pathsbar_inactive_path_template()
       . $htmlPaths->pathsbar_active_path_template()
       . $layout->block_zone_fellow_template();
    ?>
    <template id="tplEmptySlot">
        <li class="empty_slot"></li>
    </template>
</section>
    
    <?php
    // Asks for chosing a citizen speciality (builder, digger...)
    if ($citizen['citizen_id'] !== null and $citizen['last_specialization_cycle'] < $current_cycle) {
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
                    '.$enclosure->city_menu($citizen['map_id'], $city_data['connected_city_id'], $citizen['city_id']).'
                    '.$enclosure->city_submenu($city_data['city_type_id'], $city_data['connected_city_id'], $is_citizen_home_connected, $completed_buildings_ids).'
                    <div id="city_defenses" class="city_row">
                        '.$enclosure->block_defenses($city_data['total_defenses'], $zone['zombies']).'
                    </div>
                </nav>
                
                <!-- Textual representation of the city -->
                <section id="city_contents">
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
                        '. $enclosure->block_constructions($configs['buildings'], $configs['buildings_components'], $configs['items'], 
                                                           $completed_buildings_ids, $zone['items'], $configs['map']['home_buildings_set_id']) .'
                    </div>
                    <div id="city_fellows" class="city_row">
                        '. $enclosure->block_fellows_list($city_fellows, $specialities) .'
                        '. $enclosure->block_fellows_homes($city_fellows, $specialities, $city_data['coord_x'], $city_data['coord_y']) .'
                        '. $enclosure->button_close_block().'
                    </div>
                    <div id="city_storage" class="city_row">
                        <div id="blockCityStorage" class="city_block">
                            '. $enclosure->block_city_storage($html['zone_items']) .'
                        </div>
                        '. $enclosure->block_bag($html['bag_items']) .'
                        '. $enclosure->button_close_block() .'
                    </div>
                    <div id="city_well" class="city_row">
                        '. $enclosure->block_well($well_construction_id, $well_current_water) .'
                        '. $enclosure->block_bag($html['bag_items']) .'
                        '. $enclosure->button_close_block() .'
                    </div>
                    <div id="city_workshop" class="city_row">
                        '. $enclosure->block_workshop($zone['items'], $configs['items']) .'
                        '. $enclosure->button_close_block().'
                    </div>
                    <div id="city_constructions" class="city_row">
                        '. $constructionCards->tip_cards($configs['items'], $zone['items'], 
                                                         $city_buildings_caracs, $configs['buildings_components'],
                                                         $city_data['constructions'], $completed_buildings_ids) .'
                        '. $enclosure->block_constructions($configs['buildings'], $configs['buildings_components'], $configs['items'], 
                                                           $completed_buildings_ids, $zone['items'], $configs['map']['city_buildings_set_id']) .'
                        '. $enclosure->button_close_block().'
                    </div>
                    <div id="city_door" class="city_row">
                        '. $enclosure->block_city_door($city_data['is_door_closed']) .'
                        '. $enclosure->button_close_block().'
                    </div>
                    <div id="explore" class="city_row">
                        '. $enclosure->block_explore() .'
                        '. $enclosure->button_close_block().'
                    </div>
                </section>
                
                <!-- Isometric representation of the city -->
                <section id="city_iso">
                    '.$cityIso->resources_bar($nbr_ground_items, $well_current_water).'
                    '.$cityIso->city().'
                </section>
            </div>';

        // Dark overlay to blur the map under the city interface
        echo '<div id="dark_background"></div>';
     } ?>
    
    
    <!-- Let this bar *before* the round action buttons if you want them 
         to go *below* the bar on small screens -->
    <div id="Outside" style="line-height:0"></div>
    
    <!-- The map -->
    <section id="map">
        
        <div id="map_viewport">
            <div id="map_body_wrapper">
                <div id="map_body">
                    <!-- Let the SVG *before* the map zones, otherwise the invisible
                     SVG area will cover it and block all interactions (clicking, hovering...) -->
                    <svg id="mapSvg" class="hidden"></svg>
                    <?php echo $html['map']; ?>
                </div>
            </div>
        </div>
        
        <?php echo $htmlPaths->paths_bar() ?>
        
        <form method="get" id="formPathDrawing" class="hidden">
            <ul class="center">
                <li class="place_first_stage">Placez le point de départ de votre expédition en cliquant sur une zone de la carte.</li>
                <li class="hidden place_second_stage">Bien ! Placez mainteant une seconde étape, sur une zone adjacente à la première.</li>
                <li class="hidden place_other_stages">Ajoutez d'autres étapes pour tracer le chemin que vous souhaitez.</li>
                <li class="hidden make_a_loop">Pour valider le tracé, formez une boucle jusqu'à revenir au point de départ.</li>
                <li class="hidden save_stages">
                    <button type="submit" class="redbutton">Enregistrer l'expédition</button>
                </li>
            </ul>
            <div class="fields"></div>
        </form>
        
        <section id="paths_panel" class="hidden">
            <a class="close" onclick="hideIds('paths_panel');unhideId('paths_bar');unhideId('tasks_button');unhideId('attack_bar');">
                <i class="material-icons">close</i>
            </a>
            <div class="body"></div>
        </section>
        
        <section id="personal_block_wrapper">
            <div id="personal_block">
                <?php
                echo $statusbar->statusbar($citizen['bag_items'], $citizen['city_id'], $citizen['is_wounded'],
                                           count($zone_fellows)-1);
                echo $layout->bagbar($configs['items'], $citizen['bag_items'], $citizen['bag_size']);
                ?>
            </div>
        </section>
        
        <ul id="views_bar">
            <li class="map active" onclick="toggle(['#map_navigation', '#game_footer', '.map_legend']);this.classList.toggle('active')">
                <button>&#x1F9ED;</button></li>
            <li id="action_mode_button" class="my_zone">
                <button style="left:0.25rem;">
                    <span style="position:absolute;top:0.5rem;left:-0.95rem;font-size:0.6em">&#x26CF;&#xFE0F;</span>
                    <img src="resources/img/free/human.png" height="48">
                </button>
            </li>
            <li class="paths" onclick="toggle('#paths_bar');this.classList.toggle('active')">
                <button>&#x1F97E;</button>
            </li>
        </ul>
        
        <?php
        if($citizen['citizen_id'] !== null) {
            ?>
            <div id="resizeMap">
                <button id="map_mode_button" style="display:none"><i class="material-icons">zoom_out_map</i></button>
                <!-- <button id="action_mode_button"><i class="material-icons">zoom_in_map</i></button> -->
            </div>
            <?php
        } ?>
        
        <div id="map_navigation">
            <?php echo $layout->block_map_navigation() ?>
        </div>
        
        <?php echo $legends->all_legends() ?>
        
        <div id="actions_panel">
            <div id="round_actions">
                <?php
                echo  $buttons->button_round('move', ($zone['controlpoints_zombies']-$zone['controlpoints_citizens']))
                    . $buttons->button_round('dig', array_sum((array)$zone['items']), (bool)$citizen['can_dig'])
                    . $buttons->button_round('zombies', $zone['zombies'], (bool)$zone['zombies'])
                    . $buttons->button_round('citizens', null, null)
                    . $buttons->button_round('build');
                // Warn if wounded
    //                echo $layout->block_alert_wounded((bool)$citizen['is_wounded']);
                ?>
            </div>
            
            <div id="actions">
                <fieldset id="block_move" class="z-depth-2">
                    <?php
                    if ($zone['controlpoints_citizens'] < $zone['controlpoints_zombies'] and time() < strtotime($zone['date_control_end'])) {
                        echo $layout->block_alert_escape(strtotime($zone['date_control_end']));
                    }
                    echo $layout->block_alert_tired($zone['zombies']);
                    echo $layout->block_alert_control($zone['zombies']);

                    echo '
                    <div class="main_block">'
                        .$paddle->paddle($citizen['coord_x'], $citizen['coord_y'])
                        .'<div>'
                            .$layout->block_landtype()
                            .$layout->block_distance()
                        .'</div>
                    </div>';

                    echo 
                    $actionCards->card_building().
                    $actionCards->card_citizens().
                    $actionCards->card_dig().
                    $actionCards->card_ap_cost();
                    ?>
                </fieldset>

                <?php
                echo $actionBlocks->block_dig($html['ground_items'], (bool)$citizen['can_dig']);
                echo $actionBlocks->block_zombies($zone['zombies'], $citizen['bag_items'], $configs['items'], $configs['map']['killing_zombie_cost']);
                echo $actionBlocks->block_citizens();
                echo $actionBlocks->block_build($citizen['coord_x'], $citizen['coord_y']);
                ?>
            </div>
        </div>
        
        <div id="message_move"><?php echo $msg_move ?></div>
        
        <a href="#poptasks" id="tasks_button">
            <span class="dot_number">8</span>
            &#x1F4D1; <span class="text">Objectifs</span>
        </a>
    </section>
    
    <?php
    if ($citizen['user_id'] === NULL) {        
        // If the player is not connected, display the connection panel
        echo $layout->block_connect();
    }
    elseif ($citizen['citizen_id'] === NULL) {         
        // If the player is connected but has not created his citizen yet,
        // display the panel for creating a citizen
        echo $layout->block_create_citizen();
    } ?>
    
    <section id="game_footer">
        <div id="floating_wall">
            <?php echo $wall->wall() ?>
        </div>
        <div id="attack_bar">
            <?php echo $html['attack_bar'] ?>
        </div>
    </section>

</div>
    
<div id="rules">
    <br>
    <br>
    <br>
    <br>

    <?php
    echo $buttons->button('start_game');
    echo $buttons->button('add_mass_zombies');
    ?>
    <br>
    <a href="edit" title="Paramétrez les objets disponibles dans le jeu (bêta)">Créer des objets</a>
    <br>
    
    <br>
    
    <form method="post" action="<?php echo official_server_root().'/apis-list' ?>" target="_blank">
        <input type="hidden" name="token" value="<?php echo $api->get_token() ?>" />
        <input type="submit" value="Debugage"  class="formlink" style="color:grey"
               title="Lien spécial pour le débugage - Ignorez-le sauf si un administrateur du jeu vous le demande." />
    </form>
    
    
    <section>
        <h3 id="Citizens"><a href="#Citizens">&Hat;</a>&nbsp;Liste des citoyens</h3>    
        <?php echo $html['map_citizens'] ?>
    </section>
    
    <hr>
    
    <p>Merci à <a href="http://twinoid.com/user/7912453" target="_blank" rel="noopener"><strong>Ross</strong></a> 
        pour son image de ville <img src="resources/img/free/city.png" alt="city.png">
    </p>
    
    <hr>
    
    <section>
        <h3 id="Help">Mémo des règles</h3>
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
    </section>

</div>
    
<?php echo $layout->page_footer();
