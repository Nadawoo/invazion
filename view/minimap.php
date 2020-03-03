<?php
/**
 * Génère le HTML de la mini carte (le GPS à droite de la carte)
 * 
 * @param int $map_cols Le nombre de colonnes de la carte réelle
 * @param int $map_rows Le nombre de lignes de la carte réelle
 * @param int $coord_x  La coordonnée X de la case où est le joueur
 * @param int $coord_y  La coordonnée Y de la case où est le joueur
 * @param array $zone   Les données de zone extraites de la BDD (nombre de zombies...)
 * 
 * @return string HTML
 */
function minimap($map_cols, $map_rows, $coord_x, $coord_y, $actionpoints, $zone)
{
    
    // L'emplacement du joueur sur l'axe horizontal de la mini carte sera 
    // en % de la largeur de la carte réelle. NB : on divise la coordonnée 
    // du joueur par 2 car on est dans un système de "coordonnées doublées".
    $x_percent = round(($coord_x/2) / ($map_cols+1)*100);
    // La coordonnée Y n'est pas divisée car pas de saut dans sa numérotation
    $y_percent = round($coord_y / ($map_rows+1)*100);
    
    // Affiche une notification si le déplacement coûte des PA
    $notif = '';
    
    if ($actionpoints > 0 and $zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {
        
        $notif = '<div class="notif">Vous êtes bloqué par les zombies !</div>';
    }
    elseif ($actionpoints === 0) {
        
        $notif = '<div class="notif">Vous n\'avez plus de PA pour bouger !</div>';
    }
    elseif ($zone['zombies'] > 0) {
        
        $notif = '<div class="notif">Partir vous coûtera 1 PA ('.$actionpoints.' restants)</div>';
    }
    elseif (is_int($zone['city_id']) and $zone['city_size'] === 1) {
        
        $notif = '<div class="notif">Une tente ! L\'occasion de s\'abriter...</div>';
    }
    elseif (is_int($zone['city_id']) and $zone['city_size'] >= 2) {
        
        $notif = '<div class="notif">Une ville ! L\'occasion de s\'abriter...</div>';
    }
    
    echo '<div id="gps">
            <div class="title">––</div>
            <div class="container">
                <div class="sideicons">
                    &#128205;<br>
                    <span style="color:red;font-variant:small-caps">gps</span><br>
                <!--                    
                    <br>
                    &#9888;&#65039;<br>
                    <span>action</span><br>
                    <br>
                    &#128276;
                    <span>notif</span><br>
                -->
                </div>
                <div class="minimap" onclick="toggle(\'my_zone\');setCookie(\'show_zone\', 1)">
                    <div style="position:relative;left:'.$x_percent.'%;top:'.$y_percent.'%">
                        <span class="dot">•</span>
                        <!-- Laisser ce texte APRES le point afin de ne pas décaler le point vers le bas -->
                        <span class="label">['.$coord_x.':'.$coord_y.']</span>
                    </div>
                </div>
            </div>
            '.$notif.'
        </div>';
}
