<?php
require_once 'controller/autoload.php';
safely_require('view/plural.php');
safely_require('controller/get_coordx.php');


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
    private function html_cell_content($cell_alias, $string1='')
    {
        
        $templates = [
            'citizens_group' => '<div class="map_citizen">&#10010;</div>'."\n",
            'citizen_alone' => '<div class="map_citizen">'.substr($string1, 0, 2).'</div>',
            'citizen_me'    => '<div class="map_citizen" id="me">'.substr($string1, 0, 2).'</div>
                                <div class="halo">&nbsp;</div>',
            'city'          => '<div><img src="resources/img/city.png" alt="&#10224;"></div>'
                               . '<div class="city_nbr_def">'.$string1.'</div>',
            'tent'          => '<div class="tent">&#9978;</div>',
            'vault'         => '<div class="vault">&#9961;&#65039;</div>',
            'items'         => '',
            'zombies'       => '<div class="grey">'.$string1.'</div>',
        ];
        
        return "    ".$templates[$cell_alias]."\n";
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
            'citizens_group' => '<br>Plusieurs citoyens se sont rassemblés ici... Complotent-ils quelque chose&nbsp;?',
            'citizen_alone' => '<br>Le citoyen '.$string1.' est ici.',
            'citizen_me'    => '<br>Vous êtes ici, '.$string1.'&nbsp;! Utilisez le volet à droite de la carte '
                                . 'pour vous déplacer, fouiller le sol, attaquer des zombies, ramasser des objets...',
            'city'          => '<br>Cette ville offre '.$string1.' points de défense... '
                               . 'Peut-être pourrez-vous vous y réfugier&nbsp;?',
            'tent'          => '<br>Un citoyen a planté sa tente ici.',
            'vault'         => '<br>Une crypte se trouve dans la zone... Qui sait quels secrets elle renferme&nbsp;?',
            'items'         => '<br>Il y a des objets dans cette zone... Mais lesquels&nbsp;?',
            'zombies'       => '<br>Il y a '.plural($string1, 'zombie').' dans cette zone&nbsp;!',
        ];
        
        return $templates[$bubble_alias];
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
            
            // On décale 1 ligne sur 2 vers la droite pour disposer les hexagones en quinquonce
            $left = ($row%2 === 1) ? 'style="padding-left:1.25em"' : '';
            
            $result .= '<div class="row" '.$left.'>';
            
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
                $is_player_in_zone = ($citizen['coord_x'] === $col and $citizen['coord_y'] === $row) ? true : false;
                // Pseudo d'un des citoyens présents sur la case
                $fellow_pseudo = (isset($citizens_by_coord[$coords])) ? $citizens_by_coord[$coords][0]['citizen_pseudo'] : null;
                
                $result .=  $this->hexagonal_zone($col, $row, $cell, $is_player_in_zone, $citizen['citizen_pseudo'], $fellow_pseudo);
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
     * @param array $fellow_pseudo  Pseudo d'un des citoyens présents sur la case 
     *                              (autre celui du joueur actuel)
     * 
     * @return string   Le HTML de la case
     */
    private function hexagonal_zone($col, $row, $cell, $is_player_in_zone, $player_pseudo, $fellow_pseudo)
    {
        
        // Important : la cellule doit toujours avoir un contenu, même 
        // un simple espace, sinon décalages si la cellule contient ou non 
        // des citoyens/zombies/objets
        $cell_content   = '&nbsp;';
        $bubble         = '';
        $bubble_zombies = '';
        $bubble_items   = '';
        $id             = '';
        
        
        if ($is_player_in_zone === true) {

            // Mise en valeur du joueur actuel sur la carte
            $id           = 'id="my_hexagon"';
            $cell_content = $this->html_cell_content('citizen_me', $player_pseudo);
            $bubble       = $this->html_bubble('citizen_me', $player_pseudo);
        }
        elseif ($cell === null) {
            // Quand la zone est vide.
            // Cette condition ne sert qu'à éviter de répéter "if(isset($cells[$coords])..."
            // à chacune des condition suivantes.
            // TODO : revoir l'organisation de l'affichage afin d'éviter ce bricolage.
        }
        elseif ($cell['building'] === 'vault') {

            // Si la case contient une crypte, on l'affiche même si la case est inexplorée
            $cell_content = $this->html_cell_content('vault');
            $bubble       = $this->html_bubble('vault');
        }
        elseif ($cell['city_size'] === 1) {

            $cell_content = $this->html_cell_content('tent');
            $bubble       = $this->html_bubble('tent');
        }
        elseif ($cell['city_size'] > 0) {

            // Si la ville a des défenses, on affiche un fond triangulaire vert
            $city_bg = ($cell['city_defenses'] > 0) ? '    <span class="city_bg"></span>' : '';
            $cell_content = $this->html_cell_content('city', $cell['city_defenses']) . $city_bg . "\n";
            $bubble       = $this->html_bubble('city', $cell['city_defenses']);
        }
        elseif ($cell['citizens'] > 1) {

            $cell_content = $this->html_cell_content('citizens_group');
            $bubble       = $this->html_bubble('citizens_group');
        }
        elseif ($cell['citizens'] === 1) {

            $cell_content = $this->html_cell_content('citizen_alone', $fellow_pseudo);
            $bubble       = $this->html_bubble('citizen_alone', $fellow_pseudo);
        }
        else {

            if ($cell['zombies'] > 0) {

                $cell_content   = $this->html_cell_content('zombies', $cell['zombies']);
                $bubble_zombies = $this->html_bubble('zombies', $cell['zombies']);
            }

            if (!empty($cell['items'])) {
                
                $bubble_items = $this->html_bubble('items');
            }
        }


        // La case est plus ou moins opaque selon la date de dernière visite
        if($cell === null) {
            $opacity = 0;
        }
        elseif ($is_player_in_zone === true or $cell['building'] !== null) {
            $opacity = 1;
        }
        else {
            $opacity = $this->opacity_coeff($cell['date_last_visit']);
        }

        
        // Permettra d'ajouter un marqueur en javascript sur la case
        $has_items = (empty($cell['items'])) ? '' : ' hasItems';
        
        
        // - La classe "hexagon" sert à tracer le fond hexgonal
        // - La classe "square_container" est un conteneur carré pour assurer la symétrie du contenu
        // (un hexagone ne peut pas, par définition, être inscrit dans un carré)
        return '<div class="hexagon'.$has_items.'" '.$id.' style="opacity:'.$opacity.'">
                    <div class="square_container">'
                        . $cell_content . '
                        <div class="bubble">
                            [Zone '.$col.':'.$row.']'
                            . $bubble 
                            . $bubble_zombies 
                            . $bubble_items . '
                            <div class="triangle_down"></div>
                        </div>
                    </div>                    
                </div>';
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
            
            $triangles = '';
            for ($i=0; $i<$nbr_cols; $i++) {
                $triangles .= '<span class="icon">&#9760;</span>'
                            . '<span class="triangle"></span>';
            }
            
            $result .= '<div class="hurd" title="Une horde ravageuse est en train de progresser vers le sud ! '
                                              . 'Restez à distance ou vous mourrez...">'
                    . $triangles . "</div>\n";
        }
        
        return $result;
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
