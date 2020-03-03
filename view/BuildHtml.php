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
                <br><br><br>
                Identifiez-vous pour commencer à&nbsp;jouer...
                <p><a href="register" class="bold">Créer un&nbsp;compte</a></p>
                <p>ou</p>
                <p><a href="connect" class="bold">Me connecter</a></p>
            </div>';
    }
    
    
    /**
     * Affiche le bloc pour créer son premier citoyen (à droite de la carte)
     * 
     * @param  string $invalid_pseudo_message L'éventuel message d'erreur 
     * @return string
     */
    function block_citizen_creation($invalid_pseudo_message)
    {
        
        return '
            <div id="identification_near_map">
                <h3>Créer mon premier citoyen</h3>
                <form method="post" action="">
                    Nom de mon citoyen&nbsp;:
                    <input type="hidden" name="action" value="create_citizen"><br>
                    <input type="text" name="pseudo"><br>
                    <input type="submit" value="Valider">
                    <p class="red">'.$invalid_pseudo_message.'</p>
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
        return '
                <p class="center">
                    <button onclick="toggle(\'specialities\');return false" class="button red bold">Choisir ma spécialité du jour</button>
                </p>

                <ul id="specialities">
                    <li><a href="?action=specialize&amp;type=digger#Outside">Fouineur</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        (Points d\'action&nbsp;:     '. $specialities['digger']['action_points']    .' |
                        Temps de&nbsp;fouille&nbsp;: '. $specialities['digger']['digging_duration'] .'&nbsp;mn)
                    </li>
                    <li><a href="?action=specialize&amp;type=explorer#Outside">Explorateur</a>&nbsp;
                        (Points d\'action&nbsp;:     '. $specialities['explorer']['action_points']       .' |
                        Temps de&nbsp;fouille&nbsp;: '. $specialities['explorer']['digging_duration']/60 .'&nbsp;h)
                    </li>
                    <li><a href="?action=specialize&amp;type=builder#Outside">Bâtisseur</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        (Points d\'action&nbsp;:     '. $specialities['builder']['action_points']       .' |
                        Temps de&nbsp;fouille&nbsp;: '. $specialities['builder']['digging_duration']/60 .'&nbsp;h)
                    </li>
                </ul>';
    }
    
    
    /**
     * Barre contenant les caractéristiques du joueur
     * (points d'action, temps de fouillle...)
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
        
        // On n'affichera pas le joueur lui-même dans la liste de ses alliés
        unset($citizens_caracs[$citizen_id]);
        
        foreach ($citizens_caracs as $caracs) {
              
            // Bouton pour agresser/soigner le citoyen
            $attack_button = ($caracs['is_wounded'] === 0)
                              ? $buttons->attack_citizen($caracs['citizen_id'], $caracs['citizen_pseudo'])
                              : $buttons->heal_citizen($caracs['citizen_id'], $caracs['citizen_pseudo']);

            $html.= '<li>Le citoyen <strong style="color:green">'.$caracs['citizen_pseudo'].'</strong> est votre&nbsp;allié<br>'
                  . $attack_button
                  . '</li>';
        }
        
        return '<ul style="padding-left:0.9em">'.$html.'</ul>';
    }
    
    
    /**
     * Liste des objets sur une case de la carte
     * 
     * @param array $cells Toutes les cellules de la carte (issu de la BDD)
     * 
     * @return string
     */
    function block_zone_items($cells, $items, $citizen)
    {
        
        $html_items = '';
        $coord = $citizen['coord_x'].'_'.$citizen['coord_y'];
        
        if (isset($cells[$coord]['items'])) {
            
            foreach ($cells[$coord]['items'] as $item_id=>$item_amount) {
                
                $html_items .= '<li>'
                    . '<button type="submit" name="item_id" value="'.$item_id.'" class="drop_button" title="Ramasser cet objet">&wedgeq;</button> '
                    . '<var>' . $items[$item_id]['name'] . '</var> <span style="font-size:0.95em">×&nbsp;'.$item_amount .'<span>' 
                    . '</li>';
            }

            return '<form method="post" action="#Outside">'
                . '<input type="hidden" name="action" value="pickup">'
                . '<input type="hidden" name="citizen_id" value="'.$citizen['citizen_id'].'">'
                . '<ul class="items_list">'.$html_items.'</ul>'
                . '</form>';
        }
        else {
            
            return '<div style="color:lightgrey;text-align:center;margin-top:3em;">'
                 . 'Aucun objet au sol pour l\'instant. Il va falloir fouiller...</div>';
        }
    }
    
    
    /**
     * Bloc rouge affiché quand le joueur est bloqué par les zombies
     * 
     * @param  int $zombies Le nombre de zombies sur la case
     * @return string HTML
     */
    function block_alert_control($zombies, $msg_zombies_killed)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div id="alert_control">
                <div class="title">Bloqué par les zombies !</div>
                <div class="text">
                    Les <strong style="font-size:1.3em">'.$zombies.'</strong> zombies 
                    sont trop nombreux et vous encerclent ! Vous pouvez tenter 
                    d\'attaquer ces putrides afin de dégager le passage...
                    <p>'.$buttons->kill_zombie($zombies).'</p>
                    '.$msg_zombies_killed.'
                </div>
            </div>';
    }
    
    
    /**
     * Liste des objets dans le sac du citoyen
     * 
     * @param array $bag_items      Les objets dans le sac du citoyen, sous forme
     *                              de paires "id de l'objet => quantité"
     * @param array $items          Les caractéristiques de tous les items exitants dans le jeu
     * @param int   $max_bag_slots  Nombre total d'emplacements dans le sac
     * 
     * @return string   La liste des objets sous forme de liste HTML
     */
    function block_bag_items($citizen_id, $bag_items, $items, $max_bag_slots)
    {
        
        $nbr_free_slots = $max_bag_slots - array_sum(array_values($bag_items));
        
        return '
            <form method="post" action="#Outside">
                <input type="hidden" name="action" value="drop">
                <input type="hidden" name="citizen_id" value="'.$citizen_id.'">
                <ul class="items_list">
                    ' . $this->bag_filled_slots($bag_items, $items) . '
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
                        <button type="submit" name="item_id" value="'.$item_id.'" class="drop_button" title="Déposer cet objet">&veeeq;</button>
                        <var>' . $items[$item_id]['name'] . '</var>
                    </li>';
                
                $item_amount--;
            }
        }
        
        return $result;
    }
    
    

    
    
    
    /**
     * Génère un lien pour appeler une pop-up
     * 
     * @param  string $popup_alias  L'identifiant de la pop-up. Doit correspondre 
     *                              à celui utilisé par la méthode popup()
     * @param  string $text         Le texte du lien.
     * @return string
     */
    function popup_link($popup_alias, $text)
    {
        
        return '<a href="#'.$popup_alias.'"><input type="submit" value="'.$text.'"></a>';
    }
    
    
    /**
     * Génère une pop-up. Elle restera masquée tant que le visiteur n'aura pas cliqué
     * sur le lien généré par la méthode popup_link().
     * 
     * 
     * @param  string $popup_alias  L'identifiant de la pop-up. doit correspondre 
     *                              à celui utilisé par le lien d'appel (ex: href="#mypopup")
     * @param  string $text         Le contenu de la pop-up
     * @param  string $title        Le titre de la pop-up (facultatif)
     * @return string
     */
    function popup($popup_alias, $text, $title="")
    {
        
        // Fermer la pop-up ramènera au niveau de la carte de l'outre-monde.
        // Pour ramener en haut de la page, remplacer par "#".
        $anchor = '#Outside';
        
        return '
        <div id="'.$popup_alias.'" class="overlay">
            <div class="popup">
                <h2>'.$title.'</h2>
                <a class="close" href="'.$anchor.'">&times;</a>
                <div class="content">'.$text.'</div>
            </div>
        </div>';
    }
}
