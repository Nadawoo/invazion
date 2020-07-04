<?php
require_once 'controller/autoload.php';
safely_require('model/set_default_variables.php');
safely_require('view/BuildHtml.php');
safely_require('view/HtmlButtons.php');
safely_require('view/HtmlMap.php');
safely_require('view/HtmlMyZone.php');
safely_require('view/HtmlCityEnclosure.php');
safely_require('view/HtmlPopup.php');
safely_require('view/movement_paddle.php');
safely_require('view/smartphone.php');
safely_require('controller/official_server_root.php');
safely_require('controller/sort_citizens_by_coord.php');
safely_require('controller/filter_citizens_by_city.php');
safely_require('controller/filter_bag_items.php');
safely_require('ZombLib.php');


// TEMPORAIRE - Par défaut si le joueur n'est pas connecté, on affiche la carte n°1
$map_id = 1;

$api_name        = filter_input(INPUT_POST, 'api_name', FILTER_SANITIZE_STRING);
$action_post     = filter_input(INPUT_POST, 'action',   FILTER_SANITIZE_STRING);
$params_post     = filter_input(INPUT_POST, 'params',   FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

$api                = new ZombLib(official_server_root().'/api');
$html               = new BuildHtml();
$map                = new HtmlMap();
$my_zone            = new HtmlMyzone();
$enclosure          = new HtmlCityEnclosure();
$buttons            = new HtmlButtons();
$popup              = new HtmlPopup();
$zone               = set_default_variables('zone');
$citizen            = set_default_variables('citizen');
$citizen_id         = NULL;
$city_data          = NULL;
$user_id            = NULL;
$city_fellows       = [];
$zone_citizens      = [];
$healing_items      = [];
$msg_popup          = NULL;
$msg_move           = '';
$msg_build          = '';


/**
 * Exécution des actions demandées par le joueur (se déplacer, creuser...)
 */
if ($action_post !== null) {
    
    // Non-standard action of the ZombLib (base64 encoding made by the lib)
    if ($action_post === 'create_citizen') {

        $api_result = $api->create_citizen($params_post['pseudo']);
        $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
    }
    // Actions standardized with the call_api() method of the ZombLib
    else {
        
        // Calls the API ont the central server of InvaZion
        $api_result = $api->call_api($api_name, $action_post, $params_post);
        
        if (in_array($action_post, ['move', 'drop', 'pickup', 'bigfight'])
            or ($action_post === 'fight' and !isset($params_post['item_id']))
            ) {
            // The result of these actions is displayed under the movement paddle
            $msg_move  = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
        }
        else {
            // The result of all the other actions is displayed in a pop-up
            $msg_popup = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
        }
    }
}


/**
 * Récupération des données
 * À laisser *après* l'exécution des actions, sinon l'affichage aura un retard 
 * d'actualisation (pseudo non modifié, citoyen non déplacé...)
 */
// Si le joueur est authentifié *et* que son jeton n'est pas expiré
if ($api->user_seems_connected() === true) {
    
    $token          = $api->get_token_data()['data'];
    $user_id        = $token['user_id'];
    $citizen_id     = $token['citizen_id'];
    // Récupère l'id de la carte où se trouve le citoyen. Si citoyen pas encore créé,
    // on garde la carte par défaut
    $map_id         = ($token['map_id'] === NULL) ? $map_id : $token['map_id'];
    
    // Récupère les données du joueur
    $api_me = $api->call_api('me', 'get');
    
    // Si erreur dans les données, on considère le joueur n'a pas de citoyen
    if ($api_me['metas']['error_code'] !== 'success') {
        
        $citizen_id = NULL;        
        $msg_build  = '<p class="'.$api_me['metas']['error_class'].'">'.$api_me['metas']['error_message'].'</p>';
    }
}
// Récupère les données de jeu en appelant les API
$citizens           = $api->call_api('citizens', 'get', ['map_id'=>$map_id])['datas'];
$citizens_by_coord  = sort_citizens_by_coord($citizens);
$maps               = $api->call_api('maps', 'get', ['map_id'=>$map_id])['datas'];
$configs            = $api->call_api('configs', 'get')['datas'];
$specialities       = $configs['specialities'];
$speciality         = key($specialities);

// Si le joueur est connecté et a déjà créé son citoyen
if ($citizen_id !== NULL) {
    
    $citizen            = $api_me['datas'];
    $zone_citizens      = $citizens_by_coord[$citizen['coord_x'].'_'.$citizen['coord_y']];
    $zone               = $maps['zones'][$citizen['coord_x'].'_'.$citizen['coord_y']];
    $healing_items      = filter_bag_items('healing_wound', $configs['items'], $citizen['bag_items']);
    $speciality         = $citizen['speciality'];
    
    // If the citizen is inside a city, we get its characteristics (bank, well...)
    if ($citizen['is_inside_city'] === 1) {
        
        $city_data    = $api->call_api('cities', 'get', ['city_id'=>$zone['city_id']])['datas'];
        $city_fellows = filter_citizens_by_city($zone_citizens, $zone['city_id']);
    }
}

// Assembling the HTML for the map
$html_map_citizens = $html->map_citizens($citizens);
$html_map = $map->hexagonal_map($maps['map_width'], $maps['map_height'], $maps['zones'], $citizens_by_coord, $citizen, $maps['next_attack_hour']);

// Contents of the round action buttons at the right of the map
$html_actions_build     = $html->block_actions_build($zone['city_size'], $zone['building']);
$html_actions_bag       = $html->block_actions_bag($configs['items'], $citizen['bag_items']);
$html_actions_context   = $html->block_actions_context($zone['city_size'], $zone['building']);
$html_actions_zombies   = $html->block_actions_zombies($zone['zombies']);
$html_zone_items        = $html->block_zone_items($configs['items'], $zone, $citizen['citizen_id']);
$html_bag_items         = $html->block_bag_items($configs['items'], $citizen_id, $citizen['bag_items'], $citizen['bag_size']);
$html_zone_citizens     = $html->block_zone_citizens($zone_citizens, $citizen_id);

// Smartphone at the right of the map
$html_smartphone = smartphone($maps['map_width'], $maps['map_height'], $citizen, $specialities[$speciality], $zone);

unset($maps);
unset($citizens);
unset($citizens_by_coord);
?>



<?php
/**
 * Début de la page HTML
 */
echo $html->page_header();


// Textes des pop-up
// TODO : ne pas charger toutes les textes dans le code, seulement celui utile
echo $popup->predefined('popvault',   '');
echo $popup->predefined('popwounded', '', ['citizen_id'=>$citizen_id, 'healing_items'=>$healing_items]);
echo $popup->predefined('popcontrol', 'Contrôle de zone');

// Pop-up générique indiquant le résultat d'une action
echo $popup->customised('popsuccess', '', nl2br($msg_popup));
?>
    
    <div id="connectionbar">
        
        <?php echo $html->connection_bar($user_id, $citizen_id, $citizen['citizen_pseudo']); ?>
    
    </div>
    
    <p id="GameDemo" class="aside">L'interface est volontairement minimaliste pour le moment. 
        La priorité du projet est de mettre à disposition les API (le moteur du jeu) 
        à partir desquelles toute personne  sachant coder peut développer 
        sa propre interface graphique. <a href="#Project">[En savoir plus]</a>
    </p>
    
    <div id="gamebar">
        <div id="Outside">
            <a href="#Outside">#</a>&nbsp;Carte n° <?php echo $map_id ?>
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
    if ($citizen_id !== NULL and $citizen['can_change_speciality'] === 1) {
        ?>
        
        <fieldset id="citizen_caracs">
            <legend>Mes caractéristiques</legend>
            <?php echo $html->block_speciality_choice($specialities) ?>
        </fieldset>
        
        <?php
    } ?>
    
    
    <?php echo $msg_build ?>
    
<div id="game_container">
    
    <?php
    // Si le citoyen est dans une ville, affiche l'enceinte de la ville
    // (puits, banque, chantiers...) par-dessus la carte 
    if ($citizen['is_inside_city'] === 1) {
        
        echo '
            <div id="city_container">
                <div id="city_menu">
                    '.$enclosure->city_menu().'
                </div>
                <div class="city_row city_perso">
                    '. $enclosure->block_home() .'
                    '. $enclosure->block_bag($html_bag_items) .'
                </div>
                <div class="city_row city_fellows">
                    '. $enclosure->block_fellows_list($city_fellows, $specialities) .'
                    '. $enclosure->block_fellows_homes($city_fellows, $specialities, $city_data['coord_x'], $city_data['coord_y']) .'
                </div>
                <div class="city_row city_common">
                    '. $enclosure->block_bank($html_zone_items) .'
                    '. $enclosure->block_bag($html_bag_items) .'
                </div>
                <div class="city_row city_common">
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
        // Affiche la zone sur laquelle le joueur connecté se trouve
        if ($citizen_id !== NULL) {
            
            $my_zone->set_nbr_zombies($zone['zombies']);
            $my_zone->set_nbr_items($zone['items']);
            $my_zone->set_citizens_in_zone($zone_citizens);
            $my_zone->set_citizens_in_city($city_fellows);
            $my_zone->set_city_size($zone['city_size']);
            $my_zone->set_citizen_pseudo($citizen['citizen_pseudo']);
            echo $my_zone->main();
        }
        else {
            // On affiche le div de la zone pour ne pas avoir une erreur javascript si bloc inexistant
            echo '<div id="my_zone"></div>';
        }
        
        // Affiche la carte complète
        echo $html_map;
        ?>
        
        <div id="map_footer">
            <!--
            <fieldset id="bag_panel">
                <div class="legend" onclick="toggleItemsPanel()"><span class="icon">&#128188;</span> Dans mon sac</div>
                <?php echo $html_bag_items ?>
            </fieldset>
            
            <fieldset id="ground_panel">
                <div class="legend" onclick="toggleItemsPanel()">Objets au sol <span class="icon">&#9935;&#65039;</span></div>
                <?php echo $html_zone_items ?>
            </fieldset>
            -->
        </div>
    </div>
    
    
    <?php
    if ($user_id === NULL AND $citizen_id === NULL) {
        
        // Si le joueur n'est pas connecté, affiche le panneau de connexion
        echo $html->block_login();
    }
    elseif ($citizen_id === NULL) { 
        
        // Si le joueur est connecté mais n'a pas encore créé son citoyen, 
        // affiche le panneau de création de citoyen
        echo $html->block_create_citizen();
    }
    ?>
    
    <div style="min-height:16em;margin-bottom:1em;overflow:auto;">

        <div id="round_actions">
            <?php
            echo  $buttons->button_round('move', ($zone['controlpoints_zombies']-$zone['controlpoints_citizens']))
                . $buttons->button_round('dig', array_sum((array)$zone['items']))
                . $buttons->button_round('zombies', $zone['zombies'])
                . $buttons->button_round('citizens', count($zone_citizens)-1)
                . $buttons->button_round('build', min($zone['city_size'], 1));
            ?>
        </div>
        
        <div id="message_move"><?php echo $msg_move ?></div>
        
        <div id="actions">
            <fieldset id="block_move">
                <legend>Me déplacer</legend>
                <?php
                // Displays the movement paddle 
                if ($zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {
                    echo $html->block_alert_control($zone['zombies']);
                }
                elseif ($citizen['action_points'] === 0 and $zone['zombies'] > 0) {                    
                    echo $html->block_alert_tired($zone['zombies']);
                }
                else {
                    echo movement_paddle($citizen['coord_x'], $citizen['coord_y']);
                    echo $html->block_movement_AP($citizen['action_points'], $specialities[$speciality]['action_points'], $zone['zombies']);
                }
                
                // Special actions depending of the zone (go into a crypt, a city...)
                echo $html_actions_context;
                ?>
            </fieldset>
            
            <fieldset id="block_dig">
                <legend>Fouiller</legend>
                <?php 
                echo $buttons->button('dig').'<br>';
                ?>
                &#x1F4BC; <strong>Déposer un objet de mon sac :</strong>
                    <div style="margin-left:1.5rem;"><?php echo $html_bag_items ?></div>
                &#x270B;&#x1F3FC; <strong>Ramasser un objet au sol :</strong>
                    <div style="margin-left:1.5rem;"><?php echo $html_zone_items ?></div>
            </fieldset>

            <fieldset id="block_zombies">
                <legend>Actions de zone</legend>
                <?php
                echo $html_actions_zombies;
                echo '<br>'.$html_actions_bag;
                ?>                
            </fieldset>

            <fieldset id="block_build">
                <legend>Bâtiments</legend>
                <?php 
                echo $html_actions_build;
                ?>                
            </fieldset>

            <fieldset  id="block_citizens">
                <legend>Humains dans ma zone</legend>
                <?php echo $html_zone_citizens ?>
            </fieldset>
        </div>
        
        <?php
        // Displays the smartphone at the right of the map (GPS, health...)
        echo $html_smartphone;
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
    
    <?php echo $html_map_citizens ?>
    
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
    
<?php echo $html->page_footer(); ?>
