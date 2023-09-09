<?php
require 'signed_int.php';

/**
 * Generates the HTML for the smartphone at the right of the map.
 * (No relation with handling the game on mobile screens !)
 */
class HtmlSmartphone
{
    
    /**
     * Main method: generates the smartphone
     * 
     * @param int   $map_cols Le nombre de colonnes de la carte réelle
     * @param int   $map_rows Le nombre de lignes de la carte réelle
     * @param array $citizen Les caractéristiques du citoyen telles que retournées par l'API
     *                       (points d'action, coordonnées...)
     * @param array $speciality Caractéristiques de la spécialité du citoyen (nom, PA max...)
     * @param array $zone   Les données de zone extraites de la BDD (nombre de zombies...)
     * 
     * @return string HTML
     */
    public function smartphone($map_cols, $map_rows, $citizen, $speciality, $zone)
    {
        
        return '<div id="phone">
                <div class="title">––</div>
                <div class="body">
                    <div class="sideicons">
                        '.$this->side_icons().'
                    </div>

                    <div id="minimap">
                        '.$this->screen_minimap($map_cols, $map_rows, $citizen['coord_x'], $citizen['coord_y']).'
                    </div>
                    <div id="health" class="blocktext">
                        '.$this->screen_health($speciality, $citizen['action_points'], (bool)$citizen['is_wounded']).'
                    </div>
                    <div id="zone" class="blocktext">
                        '.$this->screen_zone_control($zone['controlpoints_citizens'], $zone['controlpoints_zombies']).'
                    </div>
                </div>
                '.$this->notification($zone, $citizen['action_points']).'
            </div>';
    }

    
    /**
     * Generates the vertical menu at the left of the smartphone
     * 
     * @return string HTML
     */
    private function side_icons()
    {
        
        return '
            <div onclick="activatePhoneTab(\'minimap\')">
                &#128205;
                <span>gps</span>
            </div>
            <div onclick="activatePhoneTab(\'health\')">
                &#x1F489;
                <span>santé</span>
            </div>
            <div onclick="activatePhoneTab(\'zone\')">
                &#129503;
                <span>zone</span>
            </div>';
    }
    
    
    /**
     * Generates the green GPS (mini map)
     * 
     * @param int $map_cols The number of columns of the real map
     * @param int $map_rows The number of rows of the real map
     * @param int $coord_x  The column where the player is located
     * @param int $coord_y  The row where the player is located
     * @return string HTML
     */
    private function screen_minimap($map_cols, $map_rows, $coord_x, $coord_y)
    {
        
        // L'emplacement du joueur sur l'axe horizontal de la mini carte sera 
        // en % de la largeur de la carte réelle. NB : on divise la coordonnée 
        // du joueur par 2 car on est dans un système de "coordonnées doublées".
        $x_percent = round(($coord_x/2) / ($map_cols+1)*100);
        // La coordonnée Y n'est pas divisée car pas de saut dans sa numérotation
        $y_percent = round($coord_y / ($map_rows+1)*100);
        
        return '
            <div style="position:relative;left:'.$x_percent.'%;top:'.$y_percent.'%">
                <span class="dot">•</span>
                <!-- Laisser ce texte APRES le point afin de ne pas décaler le point vers le bas -->
                <span class="label">['.$coord_x.':'.$coord_y.']</span>
            </div>';
    }
    
    
    /**
     * Generates the screen which shows health informations
     * 
     * @param array $speciality Characteristics of the speciality of the citizen
     *                          (name of the job, maximum action points...)
     * @param int $AP Amount of action points the citizen owns
     * @param bool $is_wounded "True" if the citizen is wounded
     * @return string HTML
     */
    private function screen_health($speciality, $AP, $is_wounded)
    {
        
        // Displays if the citizen is wounded
        $wound = 'Parfaite';

        if ($is_wounded === true) {
            $wound = '<a href="#popwounded">
                <strong style="color:#f44336;border-bottom:2px dotted red;">Blessé !</strong>
                </a>';
        }
        
        return '
            <h4>Ma spécialité</h4>
            '.ucfirst($speciality['name']).'
            <h4>Points d\'action</h4>
            '.$AP.' / '.$speciality['action_points'].'
            <h4>Santé</h4>
            '.$wound.'
            <h4>Durée fouille</h4>
            '.$speciality['digging_duration'].'&nbsp;mn';
    }
    
    
    /**
     * Generates the screen which displays who controls the zone (humans or zombies)
     * 
     * @return string HTML
     */
    private function screen_zone_control($cp_citizens, $cp_zombies) 
    {
        // Difference of control point between humans and zombies in the zone
        $cp_diff = $cp_citizens-$cp_zombies;
        // Color of the screen (for zone control )red if controlled by zombies)
        $background = ($cp_diff >= 0) ? '#145a32' : '#7b241c';
        
        // Tells whether the zone is safe or not
        $control = ($cp_diff >= 0)
                    ? '<div style="background-color:lightgreen;color:black">Zone sûre</div>'
                    : '<div style="background-color:#f5b7b1;color:black">Submergé !</div>';
        
        return '
            <a href="#popcontrol" style="display:block;color:inherit;background:'.$background.'">
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
            </a>';
    }
    
    
    /**
     * Displays a notification on the in-game smartphone (if movement costs AP, etc.)
     * 
     * @param array $zone The data of the zone, as returned by the API of Invazion
     * @param int   $AP   Amount of action points the citizen owns
     * @return string HTML
     */
    private function notification($zone, $AP)
    {
        
        $notifs = [
            'blocked'   => 'Vous êtes bloqué par les zombies !',
            'AP_zero'   => 'Vous n\'avez plus de PA pour bouger !',
            'AP_cost'   => 'Partir vous coûtera 1 PA ('.$AP.' restants)',
            'tent'      => 'Une tente ! L\'occasion de s\'abriter...',
            'city'      => 'Une ville ! L\'occasion de s\'abriter...',
            ];
        
        if ($AP > 0 and $zone['controlpoints_citizens'] < $zone['controlpoints_zombies']) {
            $notif = 'blocked';
        }
        elseif ($AP === 0) {
            $notif = 'AP_zero';
        }
        elseif ($zone['zombies'] > 0) {
            $notif = 'AP_cost';
        }
        elseif (is_int($zone['city_id']) and $zone['city_size'] === 1) {
            $notif = 'tent';
        }
        elseif (is_int($zone['city_id']) and $zone['city_size'] >= 2) {
            $notif = 'city';
        }

        return isset($notif) ? '<div class="notif">'.$notifs[$notif].'</div>' : '';
    }
}
