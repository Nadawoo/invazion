<?php
require 'signed_int.php';

/**
 * Génère le HTML de la mini carte (le GPS à droite de la carte)
 * 
 * @param int $map_cols Le nombre de colonnes de la carte réelle
 * @param int $map_rows Le nombre de lignes de la carte réelle
 * @param array $citizen Les caractéristiques du citoyen telles que retournées par l'API
 *                       (points d'action, coordonnées...)
 * @param array $speciality Caractéristiques de la spécialité du citoyen (nom, PA max...)
 * @param array $zone   Les données de zone extraites de la BDD (nombre de zombies...)
 * 
 * @return string HTML
 */
function smartphone($map_cols, $map_rows, $citizen, $speciality, $zone)
{
    
    $coord_x    = $citizen['coord_x'];
    $coord_y    = $citizen['coord_y'];
    $AP         = $citizen['action_points'];
    $is_wounded = $citizen['is_wounded'];
    $cp_zombies  = 0;
    $cp_citizens = 0;
    $cp_diff     = 0;
    $notif       = '';
    
    // N'existe que si le joueur est connecté
    if ($citizen !== null) {
        $notif = smartphone_notification($zone, $AP);
        $cp_zombies  = $zone['controlpoints_zombies'];
        $cp_citizens = $zone['controlpoints_citizens'];
        // Nombre de points de contrôle d'écart entre humains et zombies
        $cp_diff     = $cp_citizens-$cp_zombies;
    }
    
    // L'emplacement du joueur sur l'axe horizontal de la mini carte sera 
    // en % de la largeur de la carte réelle. NB : on divise la coordonnée 
    // du joueur par 2 car on est dans un système de "coordonnées doublées".
    $x_percent = round(($coord_x/2) / ($map_cols+1)*100);
    // La coordonnée Y n'est pas divisée car pas de saut dans sa numérotation
    $y_percent = round($coord_y / ($map_rows+1)*100);
    
    // Affiche si le citoyen est blessé
    $wound = 'Parfaite';
    
    if ($is_wounded === 1) {
    
        $wound = '<a href="#popwounded">
            <strong style="color:#f44336;border-bottom:2px dotted red;">Blessé !</strong>
            </a>';
    }
    
    // Affiche le contrôle de zone
    if ($cp_diff >= 0) {
        $control    = '<div style="background-color:lightgreen;color:black">Zone sûre</div>';
        $background = 'background:#145a32';
    }
    else {
        $control    = '<div style="background-color:#f5b7b1;color:black">Submergé !</div>';
        $background = 'background:#7b241c';
    }
    
    
    return '<div id="phone">
            <div class="title">––</div>
            <div class="container">
                <div class="sideicons">
                    <div onclick="activatePhoneTab(\'minimap\')">
                        &#128205;<br>
                        <span>gps</span>
                    </div>
                    <div onclick="activatePhoneTab(\'health\')">
                        &#x1F489;<br>
                        <span>santé</span>
                    </div>
                    <div onclick="activatePhoneTab(\'zone\')">
                        &#129503;<br>
                        <span>zone</span>
                    </div>
                <!--                    
                    <br>
                    &#9888;&#65039;<br>
                    <span>action</span><br>
                    <br>
                    &#128276;
                    <span>notif</span><br>
                -->
                </div>
                
                <div id="minimap" class="screen">
                    <div style="position:relative;left:'.$x_percent.'%;top:'.$y_percent.'%">
                        <span class="dot">•</span>
                        <!-- Laisser ce texte APRES le point afin de ne pas décaler le point vers le bas -->
                        <span class="label">['.$coord_x.':'.$coord_y.']</span>
                    </div>
                </div>
                
                <div id="health" class="screen blocktext">
                    <h4>Ma spécialité</h4>
                    '.ucfirst($speciality['name']).'
                    <h4>Points d\'action</h4>
                    '.$AP.' / '.$speciality['action_points'].'
                    <h4>Santé</h4>
                    '.$wound.'
                    <h4>Durée fouille</h4>
                    '.$speciality['digging_duration'].'&nbsp;mn
                </div>
                
                <div id="zone" class="screen blocktext" style="'.$background.'">
                <a href="#popcontrol" style="display:block;color:inherit;">
                    <h4 style="margin-top:0">Contrôle zone</h4>
                    <div style="color:lightgreen;margin:0.2em 0">
                        <div style="font-variant:small-caps">Humains</div>
                        + <span style="font-size:1.5em">'.$cp_citizens.'</span> pts
                    </div>
                    <div style="color:orange">
                        <div style="font-variant:small-caps">Zombies</div>
                        - <span style="font-size:1.5em">'.$cp_zombies.'</span> pts
                    </div>
                    <span style="font-size:1.5em;line-height:80%;color:#d6eaf8">=</span>
                    '.$control.'
                    ('.signed_int($cp_diff).' pts)
                     
                    <div style="margin-top:2em;color:#90a4ae">
                        Aide
                    </div>
                </a>
                </div>
                
            </div>
            '.$notif.'
        </div>';
}


/**
 * Displays a notification on the in-game smartphone (if movement costs AP, etc.)
 * 
 * @param array $zone The data of the zone, as returned by the API of Invazion
 * @param int   $AP   Amount of action points the citizen owns
 * @return string HTML
 */
function smartphone_notification($zone, $AP)
{
    
    $notif = '';
    
    if ($AP > 0 and $zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {

        $notif = '<div class="notif">Vous êtes bloqué par les zombies !</div>';
    }
    elseif ($AP === 0) {

        $notif = '<div class="notif">Vous n\'avez plus de PA pour bouger !</div>';
    }
    elseif ($zone['zombies'] > 0) {

        $notif = '<div class="notif">Partir vous coûtera 1 PA ('.$AP.' restants)</div>';
    }
    elseif (is_int($zone['city_id']) and $zone['city_size'] === 1) {

        $notif = '<div class="notif">Une tente ! L\'occasion de s\'abriter...</div>';
    }
    elseif (is_int($zone['city_id']) and $zone['city_size'] >= 2) {

        $notif = '<div class="notif">Une ville ! L\'occasion de s\'abriter...</div>';
    }

    return $notif;
}
