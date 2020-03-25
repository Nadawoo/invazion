<?php
require_once 'controller/autoload.php';
safely_require('view/BuildHtml.php');
safely_require('view/HtmlButtons.php');
safely_require('view/HtmlMap.php');
safely_require('view/HtmlMyZone.php');
safely_require('view/HtmlCityEnclosure.php');
safely_require('view/movement_paddle.php');
safely_require('view/minimap.php');
safely_require('controller/is_development_server.php');
safely_require('controller/sort_citizens_by_coord.php');
safely_require('controller/filter_citizens_by_city.php');
safely_require('ZombLib.php');


// TEMPORAIRE - Pour l'instant il n'y a qu'une seule carte
$map_id = 1;

$action_post     = filter_input(INPUT_POST, 'action',           FILTER_SANITIZE_STRING);
$direction       = filter_input(INPUT_POST, 'to',               FILTER_SANITIZE_STRING);
$pseudo          = filter_input(INPUT_POST, 'pseudo',           FILTER_SANITIZE_STRING);
$item_id         = filter_input(INPUT_POST, 'item_id',          FILTER_VALIDATE_INT);
$construction_id = filter_input(INPUT_POST, 'construction_id',  FILTER_VALIDATE_INT);
$city_size       = filter_input(INPUT_POST, 'city_size',        FILTER_VALIDATE_INT);
$target_id       = filter_input(INPUT_POST, 'target_id',        FILTER_VALIDATE_INT);

$action_get     = filter_input(INPUT_GET,  'action',  FILTER_SANITIZE_STRING);
$type           = filter_input(INPUT_GET,  'type',    FILTER_SANITIZE_STRING);

$api_local_url      = is_development_server() ? 'http://invazion.localhost/api' : '';
$api                = new ZombLib($api_local_url);
$html               = new BuildHtml();
$map                = new HtmlMap();
$my_zone            = new HtmlMyzone();
$enclosure          = new HtmlCityEnclosure();
$buttons            = new HtmlButtons();
$user_id            = NULL;
$citizen_id         = NULL;
$citizen_pseudo     = NULL;
$citizen            = NULL;
$city_data          = NULL;
$city_fellows       = NULL;
$msg_zombies_killed = NULL;
$msg_popup          = NULL;
$zone_citizens      = [];
$html_zone_items    = '';
$html_versus        = '';
$html_bag_items     = '';
$html_zone_citizens = '';
$msg_move           = '';
$msg_build          = '';
$invalid_pseudo_message = '';


/**
 * Exécution des actions demandées par le joueur (se déplacer, creuser...)
 */

// Définit l'id de l'objet pour certaines actions (ramasser...)
$api->set_item_id($item_id);

// Se déplacer
if ($action_post === 'move') {
    
    $api_result = $api->move($direction);
    $msg_move   = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
}
// Agresser un citoyen
elseif ($action_post === 'attack_citizen') {
    
    $api_result = $api->attack_citizen($target_id);
    $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
}
// Soigner un citoyen
elseif ($action_post === 'heal_citizen') {
    
    $api_result = $api->heal_citizen($target_id);
    $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
}
// Chercher une cypte
elseif ($action_post === 'vault') {
    
    $api_result = $api->add_stuff_on_map('vault');
    $msg_build  = '<p class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</p>';
}
// Repeuple la carte avec des zombies
elseif ($action_post === 'add_map_zombies') {
    
    $api_result = $api->add_stuff_on_map('zombies', 'noconditions');
    $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
}
// Dévoile des zones aléatoirement sur la carte
elseif ($action_post === 'reveal_zones') {
    
    $api_result = $api->reveal_zones('random7');
    $msg_build  = '<p class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</p>';
}
// Bâtir une ville sur la case
elseif ($action_post === 'build_city') {
    
    $api_result = $api->build_city($city_size);
    $msg_build  = '<p class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</p>';
}
// Investir des points d'action dans un chantier
elseif ($action_post === 'construct') {
    
    $api_result = $api->construct($construction_id);
    $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
}
// Attaquer un zombie à mains nues
elseif ($action_post === 'fight') {
    
    $api_result         = $api->fight();
    $msg_zombies_killed = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
}
// Attaquer un zombie à mains nues
elseif ($action_post === 'bigfight') {
    
    $api_result         = $api->fight('bigfight');
    $msg_zombies_killed = '<span class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</span>';
}
// Créer un citoyen
elseif ($action_post === 'create_citizen') {
    
    $api_result             = $api->create_citizen($pseudo);
    $invalid_pseudo_message = $api_result['metas']['error_message'];
}
// Actions normalisées, c'est-à-dire :
// - L'action n'a pas besoin de paramètre pour appeler l'API
// - Le message de retour s'affichera à l'emplacement $msg_build (pas en pop-up ou autre)
elseif (in_array($action_post, ['drop', 'pickup', 'attack_city', 'go_inout_city', 'open_city_door', 'close_city_door'])) {
    
    $api_result = $api->$action_post();
    $msg_build  = '<p class="'.$api_result['metas']['error_class'].'">'.$api_result['metas']['error_message'].'</p>';
}
// Actions normalisées mais dont le message de résultat doit s'afficher dans la pop-up
elseif (in_array($action_post, ['dig', 'craft_item'])) {
    
    $api_result = $api->$action_post();
    $msg_popup  = '<p>'.nl2br($api_result['metas']['error_message']).'</p>';
}

// Choisir sa spécialité quotidienne (bâtisseur....)
if ($action_get === 'specialize') {
    
    $api->specialize($type);
}


/**
 * Récupération des données
 * À laisser *après* l'exécution des actions, sinon l'affichage aura un retard 
 * d'actualisation (pseudo non modifié, citoyen non déplacé...)
 */
// Si le joueur est authentifié *et* que son jeton n'est pas expiré
if ($api->user_seems_connected() === true) {
    
    $user_id        = $api->get_token_data('user_id');
    $citizen_id     = $api->get_token_data('citizen_id');
    $citizen_pseudo = $api->get_token_data('citizen_pseudo');
}
// Récupère les données de jeu en appelant les API
$citizens           = $api->get_citizens($map_id)['datas'];
$citizens_by_coord  = sort_citizens_by_coord($citizens);
$get_map            = $api->get_map($map_id);
$configs            = $api->get_config()['datas'];
$constructions      = $configs['constructions'];
$specialities       = $configs['specialities'];
$items              = $configs['items'];
$map_cols           = $get_map['datas']['map_width'];
$map_rows           = $get_map['datas']['map_height'];
$next_attack_hour   = $get_map['datas']['next_attack_hour'];
$cells              = $get_map['datas']['zones'];
unset($get_map);

// Données du citoyen en train de jouer
if ($citizen_id !== NULL) {
    
    $citizen            = $citizens[$citizen_id];
    $zone_citizens      = $citizens_by_coord[$citizen['coord_x'].'_'.$citizen['coord_y']];
    $zone               = $cells[$citizen['coord_x'].'_'.$citizen['coord_y']];
    
    $html_zone_items    = $html->block_zone_items($cells, $items, $citizen);
    $html_versus        = $html->block_citizens_vs_zombies($zone['citizens'], $zone['zombies'], $zone['controlpoints_citizens'], $zone['controlpoints_zombies']);
    $html_bag_items     = $html->block_bag_items($citizen_id, $citizen['bag_items'], $items, $citizen['bag_size']);
    $html_zone_citizens = $html->block_zone_citizens($zone_citizens, $citizen_id);
    
    // Si une ville se trouve sur la case du citoyen, on récupère les caractéristiques 
    // de cette ville (puits, chantiers...)
    if ($zone['city_id'] !== NULL) {
        
        $city_data = $api->get_city($zone['city_id'])['datas'];
        $city_fellows = filter_citizens_by_city($citizens, $zone['city_id']);
    }
}

// Construction de la carte
$html_map_citizens = $html->map_citizens($citizens);
$html_map = $map->hexagonal_map($map_cols, $map_rows, $cells, $citizens_by_coord, $citizen, $next_attack_hour);
?>



<?php
/**
 * Début de la page HTML
 */
echo $html->page_header();


// Pop-up en entrant dans une crypte
echo $html->popup('popvault', 
                    '<p class="rp">Vous pénétrez dans la crypte obscure dont les murs irréguliers  
                    suintent d\'un&nbsp;liquide indéterminé. L\'odeur des moisissures vous enveloppe 
                    tandis que vous descendez les marches étroites...
                    </p>
                    Cette découverte vous accorde une action à&nbsp;usage unique. Voulez-vous l\'utiliser 
                    pour aider vos congénères humains...<br>
                    <ul>
                        <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur les 7&nbsp;zones alentour</span></li>
                        <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur 7&nbsp;zones aléatoires</span></li>
                        <li>+ '.$buttons->api_link('reveal_zones', 'Dévoiler 10&nbsp;zones de la carte').'</li>
                    </ul>
                    <br>
                    ...  ou bien pour propager davantage le chaos&nbsp;?
                    <ul>
                        <li>– '.$buttons->api_link('add_map_zombies', 'Ajouter des&nbsp;zombies sur toute la&nbsp;carte', '#popsuccess').'</li>
                        <li>– <span style="color:grey;text-decoration:line-through">Obscurcir 10&nbsp;zones de la&nbsp;carte</span></li>
                        <li>– <span style="color:grey;text-decoration:line-through">Détruire une&nbsp;ville aléatoire</span></li>
                    </ul>
                    ');

echo $html->popup('popwounded',
        '<p class="rp">Une vilaine plaie ouverte parcourt votre cuisse droite, 
        ce n\'est pas beau à voir. La septicémie vous guette...</p>
        <p>Vous êtes blessé ! Vous risquez de mourir 
        si vous ne vous soignez pas...</p>
        <ul>
            <li>' . $buttons->heal_citizen($citizen_id, null, true, 'Me soigner avec un bandage'). '</li>
        </ul>
        ');

// Pop-up indiquant le résultat d'une action
echo $html->popup('popsuccess', nl2br($msg_popup));
?>
    
    <!--
    Images en réserve (unicode, à ouvrir dans Firefox) :
        Tente :    &#9978;
        Cercueil : &#9904;
        Montagne : &#9968;
        Pioche :   &#9935;
        Eclair :   &#9889;
        Epées :    &#9876;
        Bonhomme symbolisé : &boxhD;
        Triple signe >>> :   &#8921;
        Circonflexe :        &Hat;
        Croix avec points : &#8251;
    -->
    
    <div id="connectionbar">
        
        <?php echo $html->connection_bar($user_id, $citizen_id, $citizen_pseudo); ?>
    
    </div>
    
    <p id="GameDemo" class="aside">L'interface est volontairement minimaliste pour le moment. 
        La priorité du projet est de mettre à disposition les API (le moteur du jeu) 
        à partir desquelles toute personne  sachant coder peut développer 
        sa propre interface graphique. <a href="#Project">[En savoir plus]</a>
    </p>
    
    <p style="float:right;margin-top:1.9em">
        <?php echo $buttons->refresh() ?>
    </p>
        
    <h3 id="Outside" style="margin-top:2em">
        <a href="#Outside">&Hat;</a>&nbsp;Outre-monde
    </h3>
    
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
    if ($citizen['city_id'] !== NULL AND $citizen['city_id'] !== 0) {
        
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
                    '. $enclosure->block_workshop($zone['items'], $items) .'
                </div>
                <div class="city_row city_build">
                    '. $enclosure->block_constructions($constructions, $city_data['constructions'], $city_data['total_defenses'], $items, $zone['items']) .'
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
        
        // Affiche la carte complète
        echo $html_map;
        ?>
    </div>
    
    
    <?php
    if ($user_id === NULL AND $citizen_id === NULL) {
        
        // Si le joueur n'est pas connecté, affiche le panneau de connexion
        echo $html->block_login();
    }
    elseif ($citizen_id === NULL) { 
        
        // Si le joueur est connecté mais n'a pas encore créé son citoyen, 
        // affiche le panneau de création de citoyen
        echo $html->block_citizen_creation($invalid_pseudo_message);
    }
    else {
        // Si le joueur est connecté et a déjà créé son citoyen,
        // on affiche l'interface de jeu
        ?>
    
        <div style="margin-bottom:3em;overflow:auto">
            
            <?php
            // Affiche le GPS (la mini carte)
            echo minimap($map_cols, $map_rows, $citizen['coord_x'], $citizen['coord_y'], $specialities[$citizen['speciality']], $citizen['action_points'], $zone);
            
            if ($zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {
            
                echo $html->block_alert_control($zone['zombies'], $msg_zombies_killed);
            }
            else {
                
                // Affiche les flèches de déplacement            
                echo movement_paddle($citizen['coord_x'], $citizen['coord_y']);
                
                echo '<div class="center" style="min-height:5em">' . $msg_move . '</div>';
            }
            
            // Affiche le bouton pour entrer dans la crypte s'il y en a une
            if ($zone['building'] === 'vault') {
                
                echo '<p class="center">'
                    . '<span class="red">Vous avez découvert une crypte&nbsp;!</span>'
                    . $html->popup_link('popvault', 'Pouvoir cryptique')
                    . '</p>';
            } ?>
            
                    
            <fieldset>
                <legend>Contrôle de la zone
                [<abbr title="Plus les citoyens sont nombreux dans la zone,
plus ils disposent de points de contrôle (PDC)
pour contrer les zombies.
Sortez groupés...">?</abbr>]
                </legend>
                
                <?php
                echo $html_versus;
                echo $html_zone_citizens;
                ?>
            </fieldset>
            
            
            <fieldset id="bag_panel">
                <legend>Dans mon sac</legend>
                <?php echo $html_bag_items ?>
            </fieldset>
            
            <fieldset id="state_panel" class="inactive_tab" onclick="switch_tab('state_panel', 'ground_panel')">
                <legend>Mon état</legend>
                <?php
                echo $html->block_health($specialities[$citizen['speciality']], $citizen['action_points'], $citizen['is_wounded']);
                ?>
            </fieldset>
            
            <fieldset id="ground_panel">
                <legend onclick="switch_tab('ground_panel', 'state_panel')">Objets au sol</legend>
                <?php 
                echo $html_zone_items 
                ?>
            </fieldset>
            
            <fieldset>
                <legend>Actions</legend>
                
                <?php
                // S'il n'y a pas de tente ni ville sur la case,
                // affiche les boutons pour en construire
                if ($zone['city_size'] === 0) {
                    
                    echo $buttons->build_tent();
                    echo $buttons->build_city();
                } 
                // Bouton pour générer une crypte
                echo $buttons->add_vault();
                ?>
                
            </fieldset>
        </div>
        <?php
    } ?>
    
</div>
    
    <form method="post" action="#popsuccess">
        <input type="hidden" name="action" value="add_map_zombies" />
        <input type="submit" value="Ajouter des zombies sur toute la carte" />
    </form>
    
    
    <h3 id="Citizens" style="margin-top:20em"><a href="#Citizens">&Hat;</a>&nbsp;Liste des citoyens</h3>
    
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
    
    
<?php echo $html->page_footer(); ?>
