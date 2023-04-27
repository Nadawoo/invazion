<?php
safely_require('/core/view/plural.php');
safely_require('/core/controller/get_coordx.php');


/**
 * Génère la carte du jeu, en HTML
 */
class HtmlMap
{
    
    
    function set_config_buildings($config_buildings)
    {
        
        $this->config_buildings = $config_buildings;
    }

    
    /**
     * Returns a property of a building (name, icon, description...)
     * 
     * @param int $building_id The ID of the building, as returned by the API "zone"
     * @param string $field The name of the property needed. Must be a key 
     *                      returned by the API "configs"
     * @return string|int If the building ID exists, returns the asked property (name...)
     *                    If doesn't exist, returns a placeholer containing
     *                    the building ID. Useful to replace it asynchronously with javascript.
     */
    function building($building_id, $field) {
            
        return (isset($this->config_buildings[$building_id]))
                ? $this->config_buildings[$building_id][$field]
                : '<div class="buildingId">'.$building_id.'</div>';
    }
    
    
    /**
     * Templates for the zone contents
     * 
     * @param string $cell_alias   A name picked in the list of cells in this method
     * @param string $string1      A free string to display a variable text (a pseudo...)
     * @return string HTML
     */
    private function html_cell_content($cell_alias, $string1='', $string2='')
    {
        
        $templates = [
            'citizen_alone' => '<div class="map_citizen">'.substr($string1, 0, 2).'</div>',
            'items'         => '&nbsp;',
            'zombies'       => '<div class="zombies"><img src="resources/img/motiontwin/zombie'.$string2.'.gif" alt="Zx'.$string1.'"></div>',
        ];
        
        return (isset($templates[$cell_alias])) ? "    ".$templates[$cell_alias]."\n" : '{'.$cell_alias.'}';
    }
    
    
    /**
     * Templates for the tooltip contents, according to what is in the zone
     * 
     * @param string $bubble_alias A name picked in the list of tooltips in this method
     * @param string $string1      A free string to display a variable text (a pseudo...)
     * @return string HTML
     */
    private function html_bubble($bubble_alias, $string1='')
    {
        
        $templates = [
            'citizen_alone' => 'Le citoyen '.$string1.' est ici.',
            'items'         => '<br>Il y a des objets dans cette zone... Mais lesquels&nbsp;?',
            'zombies'       => '<br>Il y a '.plural($string1, 'zombie').' dans cette zone&nbsp;!',
        ];
        
        return (isset($templates[$bubble_alias])) ? "    ".$templates[$bubble_alias]."\n" : null;
    }
    
    
    /**
     * Specific tooltip for the buildings in the desert
     * 
     * @param int $building_id
     * @return string HTML
     */
    private function html_bubble_building($building_id)
    {
        
        // TODO: make a generic class to get the config of the buildings
        // e.g.: Config()->building(5)->descr_ambiance;
        $name           = $this->building($building_id, 'name');
        $descr_ambiance = $this->building($building_id, 'descr_ambiance');
        
        return '<h5 class="name">'.$name.'</h5>
                <hr>
                <div class="descr_ambiance">'.$descr_ambiance.'</div>';
    }
    
    
    /**
     * Displays the icon of the buildings in the desert
     * 
     * @param int $building_id
     * @return string HTML
     */
    private function html_icon_building($building_id)
    {
        
        // Placeholder. The real icon will be placed by javascript.
        return '<div class="icon_placeholder"><div class="buildingId">'.$building_id.'</div></div>';
    }
    
    
    /**
     * Génère une carte HTML à cases hexagonales
     * 
     * @param type $nbr_cols    Le nombre de colonnes de la carte
     * @param type $nbr_rows    Le nombre de lignes de la carte
     * @param array $cells      Les zones de la carte, tel que retourné par l'API maps
     * @param int $next_attack_hour L'heure de la prochaine attaque
     * 
     * @return string   Le HTML de la catte hexagonale
     */
    public function hexagonal_map($nbr_cols, $nbr_rows, $cells, $citizen, $next_attack_hour)
    {
        
        $result = '';
        
        // Pour chaque ligne de la carte
        for ($row=0; $row<$nbr_rows; $row++) {
            
            $result .= '<div class="row">';
            
            // Ligne de la horde zombie sur la carte (triangles rouges)
            $result .= $this->html_hurd($nbr_cols, $row, $next_attack_hour);
            
            // Crée les cases de la ligne en cours
            for($i=0; $i<$nbr_cols; $i++) {
                
                // Important : on utilise le système des « coordonnées doublées » 
                // afin de simplifier les algorithmes de déplacement, distance, etc.
                // Explications détaillées sur le site d'Invazion.
                $col    = get_coordx($i, $row);
                $coords = $col.'_'.$row;
                $cell   = (isset($cells[$coords])) ? $cells[$coords] : null;
                // Définit si le joueur connecté se trouve dans cette zone
                $is_player_in_zone = $this->is_player_in_zone([$col, $row], [$citizen['coord_x'], $citizen['coord_y']]);
                
                $result .=  $this->hexagonal_zone($col, $row, $cell, $is_player_in_zone);
            }
            
            $result .=  "</div>\n";
        }
        
        return $result;
    }
    
    
    /**
     * Génère une case de la carte
     * 
     * @param int $col  La coordonnée X de la zone
     * @param int $row  La coordonnée Y de la zone (le n° de la ligne)
     *                  Ex : 3 si c'est la 3e ligne de la carte
     * @param array $cell   Le contenu de la zone, tel que retourné par l'API maps
     * @param bool  $is_player_in_zone Définit si le joueur connecté se trouve dans cette zone
     * 
     * @return string   Le HTML de la case
     */
    public function hexagonal_zone($col, $row, $cell, $is_player_in_zone)
    {
        
        // Important : la cellule doit toujours avoir un contenu, même 
        // un simple espace, sinon décalages si la cellule contient ou non 
        // des citoyens/zombies/objets
        $cell_content   = '<span class="empty">&nbsp;</span>';
        $cell_zombies   = '';
        $elevate        = '';
        $opacity        = '';
        $bubble_roleplay = '';
        $bubble_zombies = '';
        $bubble_items   = '';
        $player_city_marker = '';
        
        if ($cell === null) {
            // Quand la zone est vide.
            // Cette condition ne sert qu'à éviter de répéter "if(isset($cells[$coords])..."
            // à chacune des condition suivantes.
            // TODO : revoir l'organisation de l'affichage afin d'éviter ce bricolage.
        }
        elseif ($cell['city_type_id'] !== null) {
            // The appropriate icon will be added by javascript
            $cell_content = $this->html_icon_building($cell['city_type_id']);
            $bubble_roleplay = $this->html_bubble_building($cell['city_type_id']);
            $elevate      = 'elevate';
        }
        

        if ($cell['zombies'] > 0) {
            $cell_zombies   = $this->html_cell_content('zombies', $cell['zombies'], min (9, $cell['zombies']));
            $bubble_zombies = $this->html_bubble('zombies', $cell['zombies']);
        }

        if (!empty($cell['items'])) {
            $bubble_items = $this->html_bubble('items');
        }        


        // La case est plus ou moins opaque selon la date de dernière visite
        if($cell === null) {
            $opacity = 0;
        }
        // TODO: get the building config in a cleaner way
        elseif ($is_player_in_zone === true 
//                or ($cell['building_id'] !== null and
//                   (bool)$this->building($cell['building_id'], 'is_always_visible') === true)
                ) {
            $opacity = 1;
        }
        else {
            $opacity = $this->opacity_coeff($cell['date_last_visit']);
        }

        
        // Variable grounds (sand, peebles...)
        $ground = $this->ground_css_class($cell);
        // Put a marker with javascript if the zone contains items
        $has_items = (empty($cell['items'])) ? '' : ' hasItems';
        
        
        // - La classe "hexagon" sert à tracer le fond hexgonal
        // - La classe "square_container" est un conteneur carré pour assurer la symétrie du contenu
        // (un hexagone ne peut pas, par définition, être inscrit dans un carré)
        return '<div id="zone'.$col.'_'.$row.'" class="hexagon '.$has_items.' '.$ground.' '.$elevate.'" style="opacity:'.$opacity.'">
                    <div class="square_container"
                        data-coordx="'.$col.'"
                        data-coordy="'.$row.'"
                        data-zombies="'.$cell['zombies'].'"
                        data-citizens="'.$cell['citizens'].'"
                        data-items="'.count($cell['items']).'"
                        data-controlPointsZombies="'.$cell['controlpoints_zombies'].'"
                        data-controlPointsCitizens="'.$cell['controlpoints_citizens'].'"
                        data-cityid="'.$cell['city_id'].'"
                        data-citytypeid="'.$cell['city_type_id'].'"
                        >'
                        . $cell_zombies . $cell_content . '
                        <div class="bubble">
                            <div class="coords">[Zone '.$col.':'.$row.']</div>
                            <div class="roleplay">'.$bubble_roleplay.'</div>'
                            . $bubble_zombies 
                            . $bubble_items . '
                            <div class="triangle_down"></div>
                        </div>
                    </div>
                    '.$player_city_marker.'
                </div>';
    }
    
    
    /**
     * Gives the CSS class to display the appropriate tile in a zone
     */
    function ground_css_class($cell) {
        
        if ($cell['zombies'] > 0) {
            // If there are 1 zombies or more, the ground is always the same for map clarity
            $ground = 'ground_zombies';
        }
        // City = ID #12 in the DB
        elseif ($cell['city_type_id'] === 12 or $cell['connected_city_id'] !== null) {
            // City build by the players
            $ground = 'ground_city';
        }
        else {
            // Simple visual tiles (grass, sand...)
            $ground = 'ground_'.$cell['land'];
        }
        
        return $ground;
    }
    
    
    /**
     * Draws the hurd line on the map (red triangles)
     * 
     * @param int $row The number of the current line of the map
     * @param int $next_attack_hour 
     * @return string HTML
     */
    private function html_hurd($nbr_cols, $row, $next_attack_hour)
    {
        
        $result = '';
        if ($row === $next_attack_hour) {
            
            $triangle = '<span class="icon">&#9760;</span>'
                      . '<span class="triangle"></span>';
            
            $result .= '<div class="hurd" title="Une horde ravageuse est en train de progresser vers le sud ! '
                                              . 'Restez à distance ou vous mourrez...">'
                    . str_repeat($triangle, $nbr_cols-1) 
                    . "</div>\n";
        }
        
        return $result;
    }
    
    
    /**
     * Determines if the connected player is in the given zone
     * 
     * @param array $zone_coords    The coords of the zone (X,Y)
     * @param array $citizen_coords The coords of the citizen (X,Y)
     * @return bool Returns "true" if the player is in the zone
     */
    public function is_player_in_zone($zone_coords, $citizen_coords)
    {
        
        return ($citizen_coords[0] === $zone_coords[0] and $citizen_coords[1] === $zone_coords[1]) 
                ? true : false;
    }
    
    
    /**
     * Calcule le coefficient d'opacité CSS (opacity) d'une case.
     * Plus la case a été visitée il y a longtemps, plus elle sera estompée.
     * 
     * @param string $date_last_visit Date à laquelle la case a été visitée pour 
     *                                la dernière fois, au format '2019-06-28'
     *
     * @return int Le coefficient d'opacité, entre 0 et 1
     */
    private function opacity_coeff($date_last_visit)
    {
        
        // On limite à X jours d'écart. Plus X est élevé, moins 
        // la différence d'opacité entre deux jours sera perceptible.
        $max_days_diff = 60;

        // On calcule le nombre de jours depuis la dernière visite
        $seconds_diff = time() - strtotime($date_last_visit);
        // NB : 86400 = nombre de secondes dans une journée (60 * 60 * 24)
        $days_diff = floor($seconds_diff / 86400);
        $days_diff = min($days_diff, $max_days_diff);
        // On soustrait afin que PLUS il y a de jours d'écart, plus 
        // le coefficient d'opacité soit PETIT. Puis on divise car
        // en CSS le coefficent d'opacité est en dixièmes (0.1, 0.2, etc.)
        $opacity = ($max_days_diff - $days_diff) / $max_days_diff;
        // Toute case doit rester un peu visible (pas d'opacité à 0)
//            $opacity = max($opacity, 0.2);  
        
        return $opacity;
    }
}
