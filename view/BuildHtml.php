<?php
require 'HtmlPage.php';
require 'plural.php';

/**
 * Génère les blocs HTML du jeu.
 * Certains éléments spécifiques (la carte, les boutons...) ne figurent pas ici 
 * car ils font l'objet d'une classe dédiée.
 */
class BuildHtml extends HtmlPage
{
    
    /**
     * Barre de connexion horizontale en haut de la page
     * 
     * @param int    $user_id
     * @param int    $citizen_id
     * @param string $citizen_pseudo
     * @return string HTML
     */
    function connection_bar($user_id, $citizen_id, $citizen_pseudo)
    {
        
        $buttons = new HtmlButtons;
        
        // Si le joueur n'est pas connecté
        if ($user_id === NULL) { 
            
            return $buttons->register() . $buttons->connect();
        }
        // Si le joueur est connecté mais n'a pas encore de créé de citoyen,
        // on affiche son nom de joueur
        elseif ($citizen_id === NULL) {
            
            return '<span class="bold">Connecté en tant que joueur n°' . $user_id . '</span>'
                    . $buttons->disconnect();
        }
        // Si le joueur est connecté et a déjà créé son citoyen,
        // on affiche le nom de ce citoyen
        else {
            
            return 'Vous êtes le citoyen <strong>'.$citizen_pseudo.'</strong>'
                   . $buttons->disconnect();
        } 
    }
    
    
    /**
     * Affiche le bloc pour se connecter (à droite de la carte)
     * 
     * @return string
     */
    function block_login()
    {
        
        return '
            <div id="identification_near_map">
                <br><br><br><br>
                Identifiez-vous pour commencer à&nbsp;jouer...
                <a href="register" id="register">Créer un&nbsp;compte</a>
                ou
                <a href="connect" id="connect">Connectez-vous</a> si vous êtes déjà inscrit
            </div>';
    }
    
    
    /**
     * Affiche le bloc pour créer son premier citoyen (à droite de la carte)
     * 
     * @return string
     */
    function block_create_citizen()
    {
        
        return '
            <div id="identification_near_map">
                <h3>Créer mon premier citoyen</h3>
                <form method="post" action="#popsuccess">
                    Nom de mon citoyen&nbsp;:
                    <input type="hidden" name="action" value="create_citizen"><br>
                    <input type="text" name="params[pseudo]"><br>
                    <input type="submit" value="Valider">
                </form>
            </div>';
    }
    
    
    /**
     * Génère en HTML l'encadré du rapport de force entre les citoyens et les zombies
     * (« Contrôle de la zone »)
     * 
     * @param int $nbr_citizens Le nombre de citoyens dans la zone
     * @param int $nbr_zombies  Le nombre de zombies dans la zone
     * @param int $total_citizens_pts   Nombre total de points de contrôle 
     *                                  de tous les citoyens dans la zone
     * @param int $total_zombies_pts   Nombre total de points de contrôle 
     *                                 de tous les zombies dans la zone
     * 
     * @return string HTML
     */
    function block_citizens_vs_zombies($nbr_citizens, $nbr_zombies, $total_citizens_pts, $total_zombies_pts)
    {
        
        $control_summary = '';
        
        if ($nbr_zombies === 0) {
            
            $control_summary = '<span style="color:darkgreen">'
                . 'Il n\'y a aucun zombie ici. Vous&nbsp;pouvez vous&nbsp;déplacer librement.'
                . '</span>';
        }
        elseif ($total_zombies_pts <= $total_citizens_pts) {
            
            $control_summary = '<span style="color:red">'
                . 'Il&nbsp;y&nbsp;a des&nbsp;zombies ici&nbsp;! Quitter la&nbsp;zone '
                . 'vous&nbsp;coûtera 1&nbsp;point&nbsp;d\'action.'
                . '</span>';
        }
        
        return 
          '<p>' . $control_summary . '</p>'
        . '<div style="display:flex;flex-direction:row;align-items:center;justify-content:center">'
        .   '<div class="green" style="text-align:center;cursor:help" '
                . 'title="Il y a '.plural($nbr_citizens, 'humain') . " sur cette case\n" 
                . '=> '.$total_citizens_pts.' points de contrôle pour les humains">'
                . '<span style="font-variant:small-caps">humains</span><br>'
                . '<strong style="font-size:2em">'.$total_citizens_pts.'</strong><br>'
                . 'points de contrôle<br>'
                . '<span class="small">('.plural($nbr_citizens, 'joueur').' x 5 pdc)</span>'
        .   '</div>'
        .   '<div style="text-align:center;margin:0 5%">'
        .       '&lt;vs&gt;'
        .   '</div>'
        .   '<div class="red" style="text-align:center;cursor:help" '
                . 'title="Il y a '.plural($nbr_zombies, 'zombie') . " sur cette case\n" 
                . '=> '.$total_zombies_pts.' points de contrôle pour les zombies">'
                . '<span style="font-variant:small-caps">zombies</span><br>'
                . '<strong style="font-size:2em">'.$total_zombies_pts.'</strong><br>'
                . 'points de contrôle<br>'
                . '<span class="small">('.plural($nbr_zombies, 'zombie').' x 1 pdc)</span>'
        .   '</div> '
        . '</div>';
    }
    
    
    /**
     * Boutons pour choisir sa spécialité citoyenne
     * (explorateur, fouineur, bâtisseur)
     * 
     * @param array $specialities Les caractéristiques de chaque spécialité, issues de l'API
     *                            (points d'action, temps de fouille..)
     * @return string
     */
    function block_speciality_choice($specialities)
    {
        
        $buttons = new HtmlButtons();
        
        return '
                <p class="redbutton center">
                    <button onclick="toggle(\'specialities\');return false">Choisir ma spécialité du jour</button>
                </p>

                <ul id="specialities">
                    <li>'.$buttons->button('specialize_digger', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['digger']['action_points']    .'&nbsp;&nbsp; |
                        Temps&nbsp;de&nbsp;fouille&nbsp;: '. $specialities['digger']['digging_duration'] .'&nbsp;mn]
                    </li>
                    <li>'.$buttons->button('specialize_explorer', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['explorer']['action_points']    .'&nbsp;&nbsp; |
                        Temps&nbsp;de&nbsp;fouille&nbsp;: '. $specialities['explorer']['digging_duration']/60 .'&nbsp;mn]
                    </li>
                    <li>'.$buttons->button('specialize_builder', '', 'inline').'&nbsp;
                        [Points d\'action&nbsp;:     '. $specialities['builder']['action_points']    .' |
                        Temps&nbsp;de&nbsp;fouille&nbsp;: '. $specialities['builder']['digging_duration']/60 .'&nbsp;mn]
                    </li>
                </ul>';
    }
    
    
    /**
     * Bloc contenant les caractéristiques du joueur (points d'action, temps de fouille...)
     * INUTILISÉ depuis que ces données sont affichés dans le smartphone à droite de la carte
     * 
     * @param array $speciality_caracs Les caractéristiques de la spécialité du citoyen,
     *                                 issues de l'API (points d'action, temps de fouille..)
     * @param int   $current_action_points Le nombre de points d'action dont dispose 
     *                                     actuellement le citoyen
     * @param int   $is_wounded Vaut 1 si le joueur est blessé
     * @return type
     */
    function block_health($speciality_caracs, $current_action_points, $is_wounded)
    {
        
        $html_wounded = '';
        
        if ($is_wounded !== 0) {
            
            $html_wounded = '<p><a href="#popwounded">
                    &#129656;<strong style="background:red;color:white">Vous êtes blessé !</strong>
                    </a></p>';
        }
        
        return '
                <p><strong style="color:lightgrey">Spécialité&nbsp;:</strong>
                    '.$speciality_caracs['name'].'</p>
                <p><strong style="color:lightgrey">Points d\'action&nbsp;:</strong>
                    '.$current_action_points.'&nbsp;/&nbsp;'.$speciality_caracs['action_points'].'</p>
                <p><strong style="color:lightgrey">Temps de fouille&nbsp;:</strong>
                    '.$speciality_caracs['digging_duration'].'&nbsp;mn</p>'
                . $html_wounded;
    }
    
    
    /**
     * Retourne la liste HTML des citoyens de la ville
     * 
     * @param array $citizens_caracs    Les données sur tous les citoyens
     *                                  issues de la base de données
     * 
     * @return string
     */
    function map_citizens($citizens_caracs)
    {
        
        $html = '';

        foreach ($citizens_caracs as $caracs) {

            $html.= '<li>'.$caracs['citizen_pseudo'].' (citoyen n°&nbsp;'.$caracs['citizen_id'].') '
                    . 'est en ['.$caracs['coord_x'].':'.$caracs['coord_y'].']</li>';
        }

        return '<ul>'.$html.'</ul>';
    }
    
    
    /**
     * Retourne la liste HTML des citoyens sur la case
     * 
     * @param array $citizens_caracs    Les données sur tous les citoyens de la zone
     *                                  (issues de la BDD et triées avec citizens_by_coords())
     * 
     * @return string
     */
    function block_zone_citizens($citizens_caracs, $citizen_id)
    {
        
        $buttons = new HtmlButtons;
        $html = '';
        
        if (count($citizens_caracs) === 1) {
            
            return '<p style="width:80%;margin-left:auto;margin-right:auto;font-size:0.9em;font-style:italic;color:grey">
                Personne à proximité. Vous êtes seul au milieu de cette zone désertique...
            </p>';
        }
        
        foreach ($citizens_caracs as $caracs) {
            
            $attack_button = '<span style="color:grey;font-size:0.8em"> [c\'est vous !]</span>';
            
            if ($caracs['citizen_id'] !== $citizen_id) {
                // Bouton pour agresser/soigner le citoyen
                $attack_button = ($caracs['is_wounded'] === 0)
                              ? $buttons->attack_citizen($caracs['citizen_id'], $caracs['citizen_pseudo'])
                              : $buttons->heal_citizen($caracs['citizen_id'], $caracs['citizen_pseudo']);
            }

            $html.= '<li><span class="userlabel"><span class="avatar">&#x1F464;</span> '.$caracs['citizen_pseudo'].'</span>'
                  . $attack_button
                  . '</li>';
        }
        
        return '<ol style="padding-left:0.9em">'.$html.'</ol>';
    }
    
    
    /**
     * Liste des objets sur une case de la carte
     * 
     * @param array $items_caracs Caractéristiques des objets disponibles en jeu (issu de l'API "config")
     * @param array $zone       Données de la zone où se trouve le joueur (telles que retournées par l'API)
     * @param int   $citizen_id L'id du citoyen connecté
     * 
     * @return string
     */
    function block_zone_items($items_caracs, $zone, $citizen_id)
    {
        
        $html_items = '';
        
        if (isset($zone['items'])) {
            
            foreach ($zone['items'] as $item_id=>$item_amount) {
                
                $html_items .= '<li>'
                    . '<button type="submit" name="params[item_id]" value="'.$item_id.'" class="drop_button" title="Ramasser cet objet">&wedgeq;</button> '
                    . '<var>' . $items_caracs[$item_id]['name'] . '</var> <span style="font-size:0.95em">×&nbsp;'.$item_amount .'<span>' 
                    . '</li>';
            }

            return '<form method="post" action="#Outside">'
                . '<input type="hidden" name="api_name" value="zone">'
                . '<input type="hidden" name="action" value="pickup">'
                . '<input type="hidden" name="params[citizen_id]" value="'.$citizen_id.'">'
                . '<ul class="items_list">'.$html_items.'</ul>'
                . '</form>';
        }
        else {
            
            return '<div style="margin-top:0.5rem;text-align:center;color:grey;font-size:0.95em">'
                 . '<em>Aucun objet au sol pour l\'instant. Vous allez devoir fouiller...</em></div>';
        }
    }
    
    
    /**
     * Actions block next to the map
     * 
     * @param int $city_size Size of the city in the zone where the player is.
     * @return string HTML
     */
    function block_actions_context($city_size, $zone_building)
    {
        
        $buttons = new HtmlButtons();
        $popup   = new HtmlPopup();
        $table   = '';
        
        // If there is a CRYPT in the zone, display the button to enter.
        if ($zone_building === 'vault') {

            $table .= '<tr>'
                    . '<td class="center">'
                    . '<span class="warning">Vous avez découvert une crypte&nbsp;!</span><br>'
                    . $popup->link('popvault', 'Pouvoir cryptique')
                    . '</td>'
                . '</tr>';
        }
        // If there is a TENT in the zone, display the button to enter.
        elseif ($city_size === 1) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_tent').'</td>
                <td>'.$buttons->button('enter_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td>'.$buttons->icon('attack_tent').'</td>
                <td>'.$buttons->button('attack_tent', 'no_icon').'</td>
            </tr>';
        }
        // If there is a CITY in the zone, display the button to enter.
        elseif ($city_size > 0) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_city').'</td>
                <td>'.$buttons->button('enter_city', 'no_icon').'</td>
            </tr>';
        }
                
        return '<table style="margin:1rem auto 0 auto">'.$table.'</table>';
    }
    
    
    function block_actions_build($city_size, $zone_building)
    {
        
        $buttons = new HtmlButtons;
        $popup   = new HtmlPopup;
        $table = '';
        
        // If there is a TENT in the zone, display the button to enter.
        if ($city_size === 1) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_tent').'</td>
                <td>'.$buttons->button('enter_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td>'.$buttons->icon('attack_tent').'</td>
                <td>'.$buttons->button('attack_tent', 'no_icon').'</td>
            </tr>';
        }
        // If there is a CITY in the zone, display the button to enter.
        elseif ($city_size > 0) {
            
            $table .= '<tr>
                <td>'.$buttons->icon('enter_city').'</td>
                <td>'.$buttons->button('enter_city', 'no_icon').'</td>
            </tr>';
        }
        else {
            
            $table .= '<tr>
                <td>'.$buttons->icon('build_tent').'</td>
                <td>'.$buttons->button('build_tent', 'no_icon').'</td>
            </tr>
            <tr>
                <td><img src="resources/img/city.png" alt="&#127751;"></td>
                <td>'.$buttons->build_city('no_icon').'</td>
            </tr>';               
        }
        
        
        // If there is a CRYPT in the zone, display the button to enter.
        if ($zone_building === 'vault') {

            $table .= '<tr>'
                    . '<td>'.$buttons->icon('add_vault').'</td>'
                    . '<td>'. $popup->link('popvault', 'Pouvoir cryptique').'</td>'
                . '</tr>';
        }
        else {
            $table .= '<tr>
                <td>'.$buttons->icon('add_vault').'</td>
                <td>'.$buttons->button('add_vault', 'no_icon').'</td>
            </tr>';
        }
        
        
        return '<table>'.$table.'</table>';
    }
    
    
    /**
     * Specific block to use the items in the bag
     * 
     * @param array $items_caracs   Les caractéristiques des objets du jeu, telles que 
     *                              retournées par l'API "configs"
     * @param array $bag_items      Liste des objets dans le sac du joueur
     * @return string HTML
     */
    function block_actions_bag($items_caracs, $bag_items)
    {
        
        $buttons    = new HtmlButtons();
        $html_items = null;
        
        foreach ($bag_items as $id=>$amount) {
            
            if ($items_caracs[$id]['ap_gain'] > 0) {
                $html_items .= '<li>'.$buttons->use_item('eat', $id, $items_caracs[$id]['name']).'</li>';
            }
            elseif ($items_caracs[$id]['killing_rate'] > 0) {
                $html_items .= '<li>'.$buttons->use_item('fight', $id, $items_caracs[$id]['name']).'</li>';
            }
            elseif ($items_caracs[$id]['healing_wound'] > 0) {
                $html_items .= '<li>'.$buttons->use_item('heal', $id, $items_caracs[$id]['name']).'</li>';
            }
        }
        
        if ($html_items === null) {
            $html_items = "<li><em style=\"font-size:0.9em\">Le sac ne contient pas d'objet utilisable ici...</em></li>";
        }            
        
        return '<div id="actions_bag">
                &#128188; <strong>Utiliser un objet de mon sac</strong>
                <ul>
                    '.$html_items.'
                </ul>
            </div>';
    }
    
    
    /**
     * Bloc rouge affiché quand le joueur est bloqué par les zombies
     * 
     * @param  int $zombies Le nombre de zombies sur la case
     * @return string HTML
     */
    function block_alert_control($zombies)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div id="alert_control">
                <div class="title">Bloqué par les zombies !</div>
                <div class="text">
                    Les <strong style="font-size:1.3em">'.$zombies.'</strong> zombies 
                    sont trop nombreux et vous encerclent ! Vous pouvez tenter 
                    d\'attaquer ces putrides afin de dégager le passage...
                    <p>
                        '.$buttons->kill_zombies($zombies).'<br>
                        '.$buttons->kill_zombies($zombies, 'kill_mass_zombies').'
                    </p>
                </div>
            </div>';
    }
    
    
    /**
     * Liste des objets dans le sac du citoyen
     * 
     * @param array $items_caracs   Les caractéristiques de tous les items existants dans le jeu
     * @param array $bag_items      Les objets dans le sac du citoyen, sous forme
     *                              de paires "id de l'objet => quantité"
     * @param int   $max_bag_slots  Nombre total d'emplacements dans le sac
     * 
     * @return string   La liste des objets sous forme de liste HTML
     */
    function block_bag_items($items_caracs, $citizen_id, $bag_items, $max_bag_slots)
    {
        
        $nbr_free_slots = $max_bag_slots - array_sum(array_values($bag_items));
        
        return '
            <form method="post" action="#Outside">
                <input type="hidden" name="api_name" value="zone">
                <input type="hidden" name="action" value="drop">
                <input type="hidden" name="params[citizen_id]" value="'.$citizen_id.'">
                <ul class="items_list">
                    ' . $this->bag_filled_slots($bag_items, $items_caracs) . '
                    ' . $this->bag_free_slots($nbr_free_slots) . '
                </ul>
            </form>';
    }
    
    
    /**
     * Emplacements vides du sac
     * 
     * @param type $nbr_free_slots Nombre d'emplacements libres dans le sac
     * @return string
     */
    private function bag_free_slots($nbr_free_slots)
    {
        
        $result = '';
        
        for ($i=0; $i<$nbr_free_slots; $i++) {
            
            $result.= "\n<li><var class=\"empty_slot\">-vide-</var></li>\n";
        }
        
        return $result;
    }
    
    
    /**
     * Emplacements occupés du sac
     * 
     * @param array $bag_items      Liste des id des objets dans le sac
     * @param array $items          Les caractéristiques de tous les items exitants dans le jeu
     * @return string
     */
    private function bag_filled_slots($bag_items, $items)
    {
        
        $result = '';
        
        foreach ($bag_items as $item_id=>$item_amount) {
            
            // Si le citoyen possède un objet en plusieurs exemplaires, on le fait 
            // apparaître autant de fois dans le sac.
            while ($item_amount > 0) {
                
                $result .= '
                    <li>
                        <button type="submit" name="params[item_id]" value="'.$item_id.'" class="drop_button" title="Déposer cet objet">&veeeq;</button>
                        <var>' . $items[$item_id]['name'] . '</var>
                    </li>';
                
                $item_amount--;
            }
        }
        
        return $result;
    }
}
