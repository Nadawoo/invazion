<?php
require_once 'controller/autoload.php';
safely_require('model/set_default_variables.php');
safely_require('view/HtmlLayout.php');
safely_require('view/HtmlButtons.php');
safely_require('view/HtmlMap.php');
safely_require('view/HtmlMyZone.php');
safely_require('view/HtmlCityEnclosure.php');
safely_require('view/HtmlPopup.php');
safely_require('view/HtmlMovementPaddle.php');
safely_require('view/HtmlSmartphone.php');
safely_require('controller/official_server_root.php');
safely_require('controller/sort_citizens_by_coord.php');
safely_require('controller/filter_citizens_by_city.php');
safely_require('controller/filter_bag_items.php');
safely_require('ZombLib.php');

$api                = new ZombLib(official_server_root().'/api');
$layout             = new HtmlLayout();
$map                = new HtmlMap();
$my_zone            = new HtmlMyzone();
$enclosure          = new HtmlCityEnclosure();
$buttons            = new HtmlButtons();
$paddle             = new HtmlMovementPaddle();
$phone              = new HtmlSmartphone();
$popup              = new HtmlPopup();
$zone               = set_default_variables('zone');
$citizen            = set_default_variables('citizen');
$city_fellows       = [];
$zone_fellows       = [];
$healing_items      = [];
$msg_popup          = NULL;
$msg_move           = '';
$msg_build          = '';


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
    
    if (in_array($action_post, ['move', 'drop', 'pickup', 'bigfight'])
        or ($action_post === 'fight' and !isset($params_post['item_id']))
        ) {
        // The result of these actions is displayed under the movement paddle
        $msg_move  = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
    }
    else {
        // The result of all the other actions is displayed in a pop-up
        $msg_popup = '<p>'.$api_result['metas']['error_message'].'</p>';
    }
}


/**
 * Récupération des données
 * À laisser *après* l'exécution des actions, sinon l'affichage aura un retard 
 * d'actualisation (pseudo non modifié, citoyen non déplacé...)
 */
// Si le joueur est authentifié *et* que son jeton n'est pas expiré
if ($api->user_seems_connected() === true) {
    
    // Récupère les données du joueur
    $api_me = $api->call_api('me', 'get');
    $citizen['user_id'] = $api_me['datas']['user_id'];
    
    if ($api_me['metas']['error_code'] === 'success') {
        $citizen = $api_me['datas'];
    }
    else {
        $msg_build = '<p class="'.$api_me['metas']['error_class'].'">'.$api_me['metas']['error_message'].'</p>';
    }
}
// Récupère les données de jeu en appelant les API
$citizens           = $api->call_api('citizens', 'get', ['map_id'=>$citizen['map_id']])['datas'];
$citizens_by_coord  = sort_citizens_by_coord($citizens);
$maps               = $api->call_api('maps', 'get', ['map_id'=>$citizen['map_id']])['datas'];
$configs            = $api->call_api('configs', 'get')['datas'];
$specialities       = $configs['specialities'];
$speciality_caracs  = $specialities[$citizen['speciality']];

// Si le joueur est connecté et a déjà créé son citoyen
if ($citizen['citizen_id'] !== NULL) {
    
    $zone_fellows       = $citizens_by_coord[$citizen['coord_x'].'_'.$citizen['coord_y']];
    $zone               = $maps['zones'][$citizen['coord_x'].'_'.$citizen['coord_y']];
    $healing_items      = filter_bag_items('healing_wound', $configs['items'], $citizen['bag_items']);
    
    // If the citizen is inside a city, we get its characteristics (bank, well...)
    if ($citizen['is_inside_city'] === 1) {
        
        $city_data    = $api->call_api('cities', 'get', ['city_id'=>$zone['city_id']])['datas'];
        $city_fellows = filter_citizens_by_city($zone_fellows, $zone['city_id']);
    }
}


/**
 * HTML elements to build the interface
 */
$html = [
    // Assembling the HTML for the map
    'map' => $map->hexagonal_map($maps['map_width'], $maps['map_height'], $maps['zones'], $citizens_by_coord, $citizen, $maps['next_attack_hour']),
    'map_citizens'      => $layout->map_citizens($citizens),
    // Contents of the round action buttons at the right of the map
    'actions_build'     => $layout->block_actions_build($zone['city_size'], $zone['building']),
    'actions_bag'       => $layout->block_actions_bag($configs['items'], $citizen['bag_items']),
    'actions_context'   => $layout->block_actions_context($zone['city_size'], $zone['building']),
    'actions_zombies'   => $layout->block_actions_zombies($zone['zombies']),
    'zone_items'        => $layout->block_zone_items($configs['items'], $zone, $citizen['citizen_id']),
    'bag_items'         => $layout->block_bag_items($configs['items'], $citizen['citizen_id'], $citizen['bag_items'], $citizen['bag_size']),
    'zone_fellows'      => $layout->block_zone_fellows($zone_fellows, $citizen['citizen_id']),
    // Smartphone at the right of the map
    'smartphone'        => $phone->smartphone($maps['map_width'], $maps['map_height'], $citizen, $speciality_caracs, $zone),
    ];


unset($maps);
unset($citizens);
unset($citizens_by_coord);
?>



<?php
/**
 * Début de la page HTML
 */
echo $layout->page_header();


// Textes des pop-up
// TODO : ne pas charger toutes les textes dans le code, seulement celui utile
echo $popup->predefined('popvault',   '');
echo $popup->predefined('popwounded', '', ['citizen_id'=>$citizen['citizen_id'], 'healing_items'=>$healing_items]);
echo $popup->predefined('popcontrol', 'Aide : le contrôle de zone');

// Pop-up générique indiquant le résultat d'une action
echo $popup->customised('popsuccess', '', nl2br($msg_popup));
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
        <div id="Outside">
            <a href="#Outside">#</a>&nbsp;Carte n° <?php echo $citizen['map_id'] ?>
        </div>
        <a id="notifsButton">&#x1F514; <strong>Notifications</strong></a>
        <?php echo $buttons->refresh() ?>
    </div>
    <div id="notifsBlock">
        <a id="notifsClose">X</a>
        <div id="notifsList"></div>
    </div>
    
    
    <?php
    // Demande de choisir une spécialité (bâtisseur...) (bâtisseur, fouineur...)
    if ($citizen['can_change_speciality'] === 1) {
        ?>
        
        <fieldset id="citizen_caracs">
            <legend>Mes caractéristiques</legend>
            <?php echo $layout->block_speciality_choice($specialities) ?>
        </fieldset>
        
        <?php
    } ?>
    
    
    <?php echo $msg_build ?>
    
<div id="game_container">
    
    <?php
    // Si le citoyen est dans une ville, affiche l'enceinte de la ville
    // (puits, dépôt, chantiers...) par-dessus la carte 
    if ($citizen['is_inside_city'] === 1) {
        
        echo '
            <div id="city_container">
                <div id="city_menu">
                    '.$enclosure->city_menu().'
                </div>
                <div class="city_row city_perso">
                    '. $enclosure->block_home() .'
                    '. $enclosure->block_bag($html['bag_items']) .'
                </div>
                <div class="city_row city_fellows">
                    '. $enclosure->block_fellows_list($city_fellows, $specialities) .'
                    '. $enclosure->block_fellows_homes($city_fellows, $specialities, $city_data['coord_x'], $city_data['coord_y']) .'
                </div>
                <div class="city_row city_storage">
                    '. $enclosure->block_bank($html['zone_items']) .'
                    '. $enclosure->block_bag($html['bag_items']) .'
                </div>
                <div class="city_row city_well">
                    '. $enclosure->block_well($city_data['well_current_water']) .'
                    <div class="city_block" style="visibility:hidden"></div>
                </div>
                <div class="city_row city_craft">
                    '. $enclosure->block_workshop($zone['items'], $configs['items']) .'
                </div>
                <div class="city_row city_build">
                    '. $enclosure->block_constructions($configs['constructions'], $configs['items'], $city_data['constructions'], 
                                                       $city_data['total_defenses'], $zone['items'], $citizen['citizen_pseudo']) .'
                </div>
                <div class="city_row city_door">
                    '. $enclosure->block_door($city_data['is_door_closed']) .'
                </div>
            </div>';

        // Fond sombre en surimpression par-dessus la carte
        echo '<div id="dark_background"></div>';
     } ?>
    
    
    <div id="round_actions">
        <?php
        echo  $buttons->button_round('move', max($citizen['is_wounded'], ($zone['controlpoints_zombies']-$zone['controlpoints_citizens'])))
            . $buttons->button_round('dig', array_sum((array)$zone['items']))
            . $buttons->button_round('zombies', $zone['zombies'])
            . $buttons->button_round('citizens', count($zone_fellows))
            . $buttons->button_round('build', min($zone['city_size'], 1));
        ?>
    </div>
    
    <!-- La carte -->
    
    <div id="map">
        
        <div id="map_header">
            <span onclick="toggleMapItems()">Objets sur la carte</span>
        </div>
        
        <div id="backToMap">
            <span id="displayMyZone">Afficher ma zone</span>
            <span id="hideMyZone">Afficher la carte</span>
        </div>
        
        <?php 
        // Affiche la zone sur laquelle le joueur se trouve       
        $my_zone->set_nbr_zombies($zone['zombies']);
        $my_zone->set_nbr_items($zone['items']);
        $my_zone->set_citizens_in_zone($zone_fellows);
        $my_zone->set_citizens_in_city($city_fellows);
        $my_zone->set_city_size($zone['city_size']);
        $my_zone->set_citizen_pseudo($citizen['citizen_pseudo']);
        echo $my_zone->main();
        
        // Affiche la carte complète
        echo $html['map'];
        ?>
        
    </div>
    
    <div style="margin-top:8rem">
        <?php
        if ($citizen['user_id'] === NULL) {        
            // Si le joueur n'est pas connecté, affiche le panneau de connexion
            echo $layout->block_connect();
        }
        elseif ($citizen['citizen_id'] === NULL) {         
            // Si le joueur est connecté mais n'a pas encore créé son citoyen, 
            // affiche le panneau de création de citoyen
            echo $layout->block_create_citizen();
        }
        ?>
    </div>
    
    <div id="column_right">
        
        <div id="message_move"><?php echo $msg_move ?></div>
        
        <div id="actions">
            <fieldset id="block_move">
                <legend>Me déplacer</legend>
                <?php 
                if ($zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {
                    echo $layout->block_alert_control($zone['zombies']);
                }
                elseif ($citizen['action_points'] === 0 and $zone['zombies'] > 0) {                    
                    echo $layout->block_alert_tired($zone['zombies']);
                }
                
                echo $paddle->paddle($citizen['coord_x'], $citizen['coord_y']);
                echo $layout->block_movement_AP($citizen['action_points'], $speciality_caracs['action_points'], $zone['zombies']);
                
                // Special actions depending of the zone (go into a crypt, a city...)
                echo $html['actions_context'];
                // Warn if wounded
                echo $layout->block_alert_wounded((bool)$citizen['is_wounded']);
                ?>
            </fieldset>
            
            <fieldset id="block_dig">
                <legend>Fouiller</legend>
                <?php 
                echo $buttons->button('dig').'<br>';
                ?>
                &#x1F4BC; <strong>Déposer un objet de mon sac :</strong>
                    <div style="margin-left:1.5rem;"><?php echo $html['bag_items'] ?></div>
                &#x270B;&#x1F3FC; <strong>Ramasser un objet au sol :</strong>
                    <div style="margin-left:1.5rem;"><?php echo $html['zone_items'] ?></div>
            </fieldset>

            <fieldset id="block_zombies">
                <legend>Actions de zone</legend>
                <?php
                echo $html['actions_zombies'];
                echo '<br>'.$html['actions_bag'];
                ?>                
            </fieldset>

            <fieldset id="block_build">
                <legend>Bâtiments</legend>
                <?php 
                echo $html['actions_build'];
                ?>                
            </fieldset>

            <fieldset  id="block_citizens">
                <legend>Humains dans ma zone</legend>
                <?php echo $html['zone_fellows'] ?>
            </fieldset>
        </div>
        
        <?php
        // Displays the smartphone at the right of the map (GPS, health...)
        echo $html['smartphone'];
        ?>
        
    </div>
 
</div>
    
<div id="rules">
    
    <?php echo $buttons->button('add_mass_zombies') ?>
    
    <br>
    
    <form method="post" action="<?php echo official_server_root().'/apis-list' ?>">
        <input type="hidden" name="token" value="<?php echo $api->get_token() ?>" />
        <input type="submit" value="Debugage"  class="formlink" style="color:grey"
               title="Lien spécial pour le débugage - Ignorez-le sauf si un administrateur du jeu vous le demande." />
    </form>
    
    
    <h3 id="Citizens"><a href="#Citizens">&Hat;</a>&nbsp;Liste des citoyens</h3>
    
    <?php echo $html['map_citizens'] ?>
    
    <hr>
    
    <p>Merci à <a href="http://twinoid.com/user/7912453" target="_blank"><strong>Ross</strong></a> 
        pour son image de ville <img src="resources/img/city.png" alt="city.png">
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
