<?php
safely_require('/core/view/elements/plural.php');
safely_require('/core/controller/get_coordx.php');


/**
 * Génère la carte du jeu, en HTML
 */
class HtmlMap
{
    
    
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
            'citizens_group' => '<div class="map_citizen">&#10010;</div>'."\n",
            'citizen_alone' => '<div class="map_citizen">'.substr($string1, 0, 2).'</div>',
            'citizen_me'    => '<div class="map_citizen" id="me"><img src="resources/img/free/human.png"></div>
                                <div class="halo">&nbsp;</div>',
            'city'          => '<div class="city"><img src="resources/img/free/city.png" alt="&#10224;"></div>'
                               . '<div class="city_nbr_def">'.$string1.'</div>',
            'car'           => '<div class="emoji">&#x1F697;</div>',
            'carwreck'      => '<div class="emoji">&#x1F693;</div>',
            'circus'        => '<div class="emoji">&#x1F3AA;</div>',
            'hut'           => '&nbsp;',
            'peeble'        => '&nbsp;',
            'pond'          => '&nbsp;',
            'pharmacy'      => '<div class="emoji">&#x1F3E5;</div>',
            'stonewall'     => '<div>&#x1F9F1;</div>',
            'tent'          => '<div class="emoji">&#9978;</div>',
            'vault'         => '<div class="emoji">&#9961;&#65039;</div>',
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
            'citizens_group' => '<div class="roleplay">Plusieurs citoyens se sont rassemblés ici... Complotent-ils quelque chose&nbsp;?</div>',
            'citizen_alone' => '<div class="roleplay">Le citoyen '.$string1.' est ici.</div>',
            'citizen_me'    => '<div class="roleplay">Vous êtes ici, '.$string1.'&nbsp;! Utilisez le volet à droite de la carte '
                                . 'pour vous déplacer, fouiller le sol, attaquer des zombies, ramasser des objets...</div>',
            'city'          => '<div class="roleplay">Cette ville offre '.$string1.' points de défense... '
                               . 'Peut-être pourrez-vous vous y réfugier&nbsp;?</div>',
            'player_home'   => '<div class="roleplay">Ceci est votre habitation, '.$string1.' ! Votre refuge contre les zombies...</div>',
            'car'           => '<div class="roleplay">Vous pouvez réparer cette voiture pour vous enfuir !</div>',
            'carwreck'      => '<div class="roleplay">Mieux vaut ne pas savoir ce qu\'est devenu le propriétaire de cette voiture embourbée. '
                             . 'Il a dû parvenir à s\'enfuir et coule des jours heureux quelque part... Oui, on va dire ça.</div>',
            'circus'        => '<div class="roleplay">Sous ce chapiteau déserté, plusieurs traces de zombies, d\'animaux '
                             . 'et de dresseurs. Difficile de déterminer qui a mangé qui et dans quel ordre...</div>',
            'hut'           => '<div class="roleplay">Bien que ce cabanon branlant soit détrempé par les pluies, vous parviendrez '
                             . 'sans doute à en tirer quelques planches utilisables.</div>',
            'pond'          => '<div class="roleplay">Une vieille mare d\'eau boueuse et parsemée de petites algues. '
                             . 'Ce sera meilleur que l\'eau du puits de la ville !</div>',
            'pharmacy'      => '<div class="roleplay">Un cabinet de médecin, quelle chance ! Vous pourrez emporter '
                             . 'quelques médicaments, à défaut de pouvoir ressusciter le praticien.</div>',
            'stonewall'     => '<div class="roleplay">A quel type de bâtiment appartenait donc ce mur effondré ? '
                             . 'Peu importe, il va être avantageusement recyclé en carrière de pierres.</div>',
            'tent'          => '<div class="roleplay">Un citoyen a planté sa tente ici.</div>',
            'vault'         => '<div class="roleplay">Une crypte se trouve dans la zone... Qui sait quels secrets elle renferme&nbsp;?</div>',
            'items'         => '<br>Il y a des objets dans cette zone... Mais lesquels&nbsp;?',
            'zombies'       => '<br>Il y a '.plural($string1, 'zombie').' dans cette zone&nbsp;!',
        ];
        
        return (isset($templates[$bubble_alias])) ? "    ".$templates[$bubble_alias]."\n" : null;
    }
    
     
    /**
     * Génère une carte HTML à cases hexagonales
     * 
     * @param type $nbr_cols    Le nombre de colonnes de la carte
     * @param type $nbr_rows    Le nombre de lignes de la carte
     * @param array $cells      Les zones de la carte, tel que retourné par l'API maps
     * @param array $citizens_by_coord  Les données des citoyens, indexées par leurs coordonnées
     *                                  (données de l'API citizens tirées par 
     *                                  la fonction sort_citizens_by_coord())
     * @param int $next_attack_hour L'heure de la prochaine attaque
     * 
     * @return string   Le HTML de la catte hexagonale
     */
    public function hexagonal_map($nbr_cols, $nbr_rows, $cells, $citizens_by_coord, $citizen, $next_attack_hour)
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
                // Pseudo d'un des citoyens présents sur la case
                $fellow_pseudo = (isset($citizens_by_coord[$coords])) ? $citizens_by_coord[$coords][0]['citizen_pseudo'] : null;
                
                $result .=  $this->hexagonal_zone($col, $row, $cell, $is_player_in_zone, 
                                                  $citizen['citizen_pseudo'], $citizen['city_id'],
                                                  $fellow_pseudo);
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
     * @param array $player_pseudo  Le pseudo du joueur connecté
     * @param int   $player_city_id L'ID de la vaille (habitation) du joueur
     * @param array $fellow_pseudo  Pseudo d'un des citoyens présents sur la case 
     *                              (autre celui du joueur actuel)
     * 
     * @return string   Le HTML de la case
     */
    public function hexagonal_zone($col, $row, $cell, $is_player_in_zone,
                                   $player_pseudo, $player_city_id, $fellow_pseudo)
    {
        
        // Important : la cellule doit toujours avoir un contenu, même 
        // un simple espace, sinon décalages si la cellule contient ou non 
        // des citoyens/zombies/objets
        $cell_content   = '&nbsp;';
        $cell_zombies   = '';
        $cell_me        = '';
        $bubble         = '';
        $bubble_zombies = '';
        $bubble_items   = '';
        $player_city_marker = '';
        
        if ($is_player_in_zone === true) {            
            $cell_me = $this->html_cell_content('citizen_me', $player_pseudo);
            $bubble  = $this->html_bubble('citizen_me', $player_pseudo);
        }
        
        if ($cell === null) {
            // Quand la zone est vide.
            // Cette condition ne sert qu'à éviter de répéter "if(isset($cells[$coords])..."
            // à chacune des condition suivantes.
            // TODO : revoir l'organisation de l'affichage afin d'éviter ce bricolage.
        }
        elseif ($cell['building'] !== null) {
            
            $cell_content = $this->html_cell_content($cell['building']);
            $bubble       = $this->html_bubble($cell['building'] );
        }
        elseif ($cell['city_type'] === 'home') {
            
            $cell_content = $this->html_cell_content('tent');
            $bubble       = $this->html_bubble('tent');
        }
        elseif ($cell['city_size'] > 0) {
            
            $cell_content = $this->html_cell_content('city', $cell['city_defenses']);
            $bubble       = $this->html_bubble('city', $cell['city_defenses']);
        }
        elseif ($cell['citizens'] > 1 and $is_player_in_zone === false) {

            $cell_content = $this->html_cell_content('citizens_group');
            $bubble       = $this->html_bubble('citizens_group');
        }
        elseif ($cell['citizens'] === 1 and $is_player_in_zone === false) {

            $cell_content = $this->html_cell_content('citizen_alone', $fellow_pseudo);
            $bubble       = $this->html_bubble('citizen_alone', $fellow_pseudo);
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
        elseif ($is_player_in_zone === true or $cell['building'] === 'vault') {
            $opacity = 1;
        }
        else {
            $opacity = $this->opacity_coeff($cell['date_last_visit']);
        }

        
        // Variable grounds (sand, peebles...)
        $ground = $this->ground_css_class($cell);
        // Put a marker with javascript if the zone contains items
        $has_items = (empty($cell['items'])) ? '' : ' hasItems';
        
        // Put a permanent marker on the player's habitation
        if($cell['city_id'] === $player_city_id and $player_city_id !== null) {
            $player_city_marker = '<img src="resources/img/free/map_location.svg" class="location">';
            $bubble             = $this->html_bubble('player_home', $player_pseudo);
            $bubble_items       = '';
        }
        
        // - La classe "hexagon" sert à tracer le fond hexgonal
        // - La classe "square_container" est un conteneur carré pour assurer la symétrie du contenu
        // (un hexagone ne peut pas, par définition, être inscrit dans un carré)
        return '<div id="zone'.$col.'_'.$row.'" class="hexagon '.$has_items.' '.$ground.'" style="opacity:'.$opacity.'">
                    <div class="square_container">'
                        . $cell_zombies . $cell_me . $cell_content . '
                        <div class="bubble">
                            [Zone '.$col.':'.$row.']'
                            . $bubble 
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
            $ground = 'ground_zombies_'.$cell['building'];
        }
        elseif ($cell['city_type'] === 'city' or $cell['parent_city_id'] !== null) {
            $ground = 'ground_city';
        }
        else {
            $ground = 'ground_'.$cell['building'];
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
        $max_days_diff = 5;

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
