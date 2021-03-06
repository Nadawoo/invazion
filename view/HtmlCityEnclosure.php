<?php
require_once 'controller/autoload.php';
safely_require('view/HtmlLayout.php');
safely_require('view/HtmlButtons.php');


/**
 * Génère les éléments de l'intérieur de la ville
 */
class HtmlCityEnclosure
{
    
    
    function city_menu()
    {
         return '
            <div id="city_tabs">
                <a onclick="switchCityMenu(\'cityMenuMyHome\')">Chez moi</a> &nbsp; 
                <a onclick="switchCityMenu(\'cityMenuFellows\')">Habitants</a> &nbsp; 
                <a onclick="switchCityMenu(\'cityMenuCity\')">La ville</a> &nbsp; 
                <a onclick="switchCityMenu(\'cityMenuDoor\')">Grande porte</a>
            </div>';
    }
    
    
    /**
     * Le menu horizontal pour les différentes parties de la ville
     * (dépôt, maison, porte...)
     * 
     * @return string
     */
    function city_submenu()
    {
        
        return '
            <div id="city_submenus">
                <div class="row hidden" id="cityMenuMyHome">
                    '.$this->city_submenu_item('city_perso', 'Chez moi').'
                </div>
                <div class="row hidden" id="cityMenuCity">
                    '.$this->city_submenu_item('city_storage', 'Dépôt').'
                    '.$this->city_submenu_item('city_build', 'Chantiers').'
                    '.$this->city_submenu_item('city_craft', 'Atelier').'
                    '.$this->city_submenu_item('city_well', 'Puits').'
                </div>
                <div class="row hidden" id="cityMenuFellows">
                    '.$this->city_submenu_item('city_fellows', 'Habitants').'
                </div>
                <div class="row hidden" id="cityMenuDoor">
                    '.$this->city_submenu_item('city_door', 'Grande porte').'
                </div>
            </div>';
    }
    
    
    private function city_submenu_item($item_alias, $item_name) {
        
        return '<div class="item"'
                  . 'style="background-image:url(\'resources/img/copyrighted/'.$item_alias.'.png\')" '
                  . 'onclick="switchCitySubmenu(\''.$item_alias.'\')">'
                  .'<span class="label">'. $item_name .'</span>'
                .'</div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * le puits
     * 
     * @param int $well_current_water le nombre de rations d'eau actuellement dans le puits
     * @return string
     */
    function block_well($well_current_water)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div class="city_block">
                <h2>Réserves d\'eau</h2>
                <br>
                <br>
                <strong style="font-size:1.8em;color:navy;">'.$well_current_water.'&nbsp;rations&nbsp;/&nbsp;50</strong>
                <br>
                <br>
                <br>
                '.$buttons->button('well_pickup').'
                '.$buttons->button('well_add').'
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * liste des objets dans le dépôt de la ville
     * 
     * @param  string $html_zone_items La liste HTML des objets de la zone, générée
     *                                 par la classe HtmlLayout->zone_items()
     * @return string
     */
    function block_bank($html_zone_items)
    {
        
        if ($html_zone_items === '') {
            
            $html_bank_items = '<div class="grey">
                    <br>Aucun objet dans le dépôt&nbsp;!
                    Vous devriez y déposer quelques objets personnels pour le remplir...
                </div>';
        }
        else {
            
            $html_bank_items = $html_zone_items;
        }
        
        return '
            <div class="city_block">
                <h2>Dépôt</h2>
                <div class="descr">Les joueurs déposent ici les objets trouvés 
                    lors de leurs <a onclick="switchCityTab(\'city_door\')">expéditions</a>. Utilisez-les pour 
                    <a onclick="switchCityTab(\'city_build\')">construire la ville</a>... 
                    ou <a onclick="switchCityTab(\'city_perso\')">votre propre habitation</a>.
                </div>
                <div class="contents">
                '. $html_bank_items .'
                </div>
            </div>';
    }
    
    
    function block_home()
    {
        
        return '
            <div class="city_block">
                <h2>Ma maison</h2>
                <br>
                <br>
                (à venir)
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * Liste des concitoyens
     * 
     * @param array $fellows        Les caractéristiques des citoyens de la ville
     * @param array $specialities   Les caractéristiques des spécialités (fouineur...)
     * @return string HTML
     */
    function block_fellows_list($fellows, $specialities)
    {
        
        $html_citizens  = '';
        
        foreach ($fellows as $citizen) {
            
            $localization = ($citizen['distance_to_city'] === 0)
                ? '<span class="discreet">[en ville]</span>'
                : '<span class="highlight">[à&nbsp;'.$citizen['distance_to_city'].'&nbsp;km]</span>';
            
            $html_citizens .= '
                <div onclick="toggleHouse(\'citizen'.$citizen['citizen_id'].'\')">
                    '.$localization.'
                    <a><strong>'.$citizen['citizen_pseudo'].'</strong></a>
                    ('.$specialities[$citizen['speciality']]['name'].')
                </div>';
        }
        
        return '
            <div id="citizens_list" class="city_block">
                <h2>Habitants de la ville</h2>
                '.$html_citizens.'
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * Toutes les maisons des concitoyens (masquées par défaut)
     * 
     * @param array $fellows        Les caractéristiques des citoyens de la ville
     * @param array $specialities   Les caractéristiques des spécialités (fouineur...)
     * @param int   $city_x         Coordonnée X de la ville
     * @param int   $city_y         Coordonnée Y de la ville      
     * @return string HTML
     */
    function block_fellows_homes($fellows, $specialities, $city_x, $city_y)
    {
        
        $html_houses = '';
        
        foreach ($fellows as $citizen) {
            
            $pseudo = $citizen['citizen_pseudo'];
            
            $html_houses .= '
                <div id="citizen'.$citizen['citizen_id'].'" style="display:none">
                    <div class="city_bandeau">
                        <div class="back_button" onclick="toggleHouse(\'citizen'.$citizen['citizen_id'].'\')"
                            title="Retourner à la liste des citoyens">
                            &#10096;&#10096;
                        </div>
                        <h3 style="margin:0.5em">Infos sur <span style="font-variant:small-caps">'.$pseudo.'</span></h3>
                    </div>
                    
                    <div class="city_row">
                        '.$this->block_fellow_situation($citizen, $specialities, $citizen['distance_to_city']).'
                        '.$this->block_fellow_home($pseudo).'
                    </div>
                    <div class="city_row">
                        '.$this->block_fellow_notes().'
                    </div>
                    
                </div>';
        }
        
        return $html_houses;
    }
    
    
    /**
     * Maison d'un concitoyen
     * 
     * @param string $pseudo Le nom du concitoyen propriétaire de la maison
     * @return string HTML
     */
    private function block_fellow_home($pseudo)
    {
        
        return '
            <div class="city_block">
                <h2>Sa maison</h2>
                <p style="text-align:left;margin-left:0.4em">
                    • <span class="highlight">Habitation :</span> Tente (niveau 2)<br>
                    • <span class="highlight">Défenses :</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2 points <abbr title="Le niveau de défense dépend du type d\'habitation et des objets stockés dans son coffre.">[?]</abbr><br>
                    • <span class="highlight">Décorations :</span> 3 points
                </p>
                <hr>
                <p><strong>'.$pseudo.'</strong> détient ces objets dans son coffre&nbsp;:</p>
                <p class="discreet" >(rien)</p>
                <p>[Voler un objet]<br>(si le citoyen est sorti)</p>
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * La situation générale du citoyen (spécialité, localisation...)
     * 
     * @param array $citizen        Les caractéristiques du citoyen
     * @param array $specialities   Les caractéristiques des spécialités (fouineur...)
     * @param int   $city_x         Coordonnée X de la ville
     * @param int   $city_y         Coordonnée Y de la ville      
     * @return string HTML
     */
    private function block_fellow_situation($citizen, $specialities, $distance_to_city)
    {
        
        $localization = ($distance_to_city === 0)
                        ? 'se trouve <span class="highlight">en ville</span>'
                        : 'se trouve à <span class="highlight">'.$distance_to_city.' km</span> de la ville,<br>
                           &nbsp;&nbsp;en zone <span class="highlight">['.$citizen['coord_x'].':'.$citizen['coord_y'].']</span>';
        
        return '
        <div class="city_block">
            <h2>Sa situation</h2>
            <div>
                <p style="text-align:left;margin-left:0.4em">
                    Le citoyen <strong>'.$citizen['citizen_pseudo'].'</strong>&nbsp;:<br>
                    • est spécialisé <span class="highlight">'.$specialities[$citizen['speciality']]['name'].'</span><br> 
                    • '.$localization.'<br>
                </p>
                <hr>
                <p>
                    <em class="discreet">Personne ne se plaint de '.$citizen['citizen_pseudo'].' pour le moment.</em>
                </p>
                <p>
                    [Déposer une plainte !]<br>
                    [Agresser '.$citizen['citizen_pseudo'].' !]
                </p>
            </div>
        </div>';
    }
    
    
    private function block_fellow_notes()
    {
        
        return '
            <div class="city_block">
                <h2>Mots sur sa porte</h2>
                <p>« Fonctionnalité à venir... » <em class="discreet">(Administrateur)</em></p>
                <p>[Ajouter un mot sur la porte]</p>
            </div>
            ';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * liste des objets dans le sac du joueur
     * 
     * @param  string $html_bag_items La liste HTML des objets du sac, générée
     *                                par la classe HtmlLayout->bag_items()
     * @return string
     */
    function block_bag($html_bag_items)
    {
        
        return '
            <div class="city_block">
                <h2>Mon sac</h2>
                <div class="descr">Déposez les objets de votre sac 
                    <a onclick="switchCityTab(\'city_storage\')">au dépôt de la ville</a>
                    pour bâtir <a onclick="switchCityTab(\'city_build\')">des chantiers communs</a>... 
                    ou gardez-les pour aménager
                    <a onclick="switchCityTab(\'city_perso\')">votre habitation</a>.
                </div>
                <div class="contents">
                '.$html_bag_items.'
                </div>
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * liste des chantiers construits ou constructibles en ville
     * 
     * @param array $constructions_caracs Les caractéristiques statiques des chantiers (nom...)
     * @param array $items_caracs         Les caractéristiques statiques des objets
     * @param array $city_constructions   L'état des chantiers de la ville (avancement...)
     * @param int   $total_defenses       Total des points de défense de la ville
     *                                    (somme des points de tous les chantiers achevés)
     * @param array $zone_items           Les objets disponibles dans le dépôt de la ville
     *                                    (qui sont, à ce jour, les objets au sol)
     * @return string
     */
    function block_constructions($constructions_caracs, $items_caracs, $city_constructions, $total_defenses, $zone_items)
    {
        
        $buttons = new HtmlButtons;
        $html_constructions = '';
        
        foreach ($constructions_caracs as $id=>$constr) {
            
            $html_resources = '';
            $css_id = 'building'.$id;
            
            // Valeurs par défaut si le chantier n'est pas du tout commencé
            if (!isset($city_constructions[$id])) {
                
                $city_constructions[$id] = ['AP_invested'   => 0,
                                            'is_completed'  => 0,
                                            ];
            }
            
            // Jauge des ressources requises/disponibles pour la construction
            foreach ($constr['resources'] as $item_id=>$required_amount) {

                $html_resources .= $this->html_progressbar( $item_id, $items_caracs[$item_id]['name'],
                                                            $this->item_amount($zone_items, $item_id),
                                                            $required_amount,
                                                            'constructions');
            }

            // Jauge des points d'action déjà investis dans le chantier
            $html_AP_invested = $this->html_progressbar('pa', 'Points d\'action',
                                                        $city_constructions[$id]['AP_invested'],
                                                        $constr['action_points'],
                                                        'action_points');
            
            if ($city_constructions[$id]['is_completed'] === 1) { 
                
                $html_constructions .= '
                    <tr>
                        <td onclick="toggle(\''.$css_id.'\')" class="foldable" style="background:darkgreen">
                            <h3 style="color:lightgreen">
                                <img src="../resources/img/copyrighted/buildings/'.$id.'.png" alt="icon_'.$id.'">&nbsp;'.$constr['name'].'
                            </h3>
                            <div class="unfold_button" style="color:lightgreen">&check; Fini ! &nbsp;</div>
                        </td>
                        <td style="cursor:help" 
                            title="Ce chantier augmente de '.$constr['defenses'].' points les défenses de la ville !">
                            <strong style="color:darkgreen">+&nbsp;'.$constr['defenses'].'</strong>
                        </td>
                    </tr>
                    <tr id="'.$css_id.'" class="folded">
                        <td style="font-size:0.85em;text-align:center">La construction de ce chantier est terminée !</td>
                    </tr>
                    ';
            }
            else {
                
                $html_constructions .= '
                    <tr>
                        <td onclick="toggle(\''.$css_id.'\')" class="foldable">
                            <h3 style="color:grey">
                                <img src="../resources/img/copyrighted/buildings/'.$id.'.png" alt="icon_'.$id.'">&nbsp;'.$constr['name'].'
                            </h3>
                            <div class="unfold_button">bâtir&nbsp;<div class="arrow">&#65088;</div></div>
                        </td>
                        <td style="color:grey;cursor:help"
                            title="Si vous le construisez, ce chantier augmentera de '.$constr['defenses'].' points les défenses de la ville.">
                            <strong>+&nbsp;'.$constr['defenses'].'</strong>
                        </td>
                    </tr>
                    <tr>
                        <td id="'.$css_id.'" class="folded">
                            <ul class="items_list">
                                ' . $html_resources . '
                                ' . $html_AP_invested . '
                                ' . $buttons->construct($id) . '
                            </ul>
                        </td>
                    </tr>
                    ';
            }

        }
        
        return '
            <div class="city_block" style="width:21.5em">
                <h2>Chantiers</h2>
                <div style="height:1.7em;vertical-align:center;font-weight:bold;background:lightgreen;margin-bottom:0.8em;line-height:250%;">
                    <div style="position:relative;top:-0.5em">
                        <span style="font-variant:small-caps">Défenses totales :</span>
                        <div style="display:inline-block;height:1.8em;width:1.8em;font-weight:normal;font-size:1.5em;color:white;background:green;border-radius:2em">'.$total_defenses.'</div> points
                    </div>
                </div>
                <table id="constructions">
                    <tr style="font-size:0.9em">
                        <td></td>
                        <td>Défenses</td>
                    </tr>
                    '.$html_constructions.'
                </table>
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * la porte de la ville
     * 
     * @param int $is_door_closed   Vaut 1 (TRUE)  si la porte est fermée
     *                              Vaut 0 (FALSE) si la porte est ouverte
     * @return string
     */
    function block_door($is_door_closed)
    {
        
        $buttons = new HtmlButtons();
        
        if ((bool)$is_door_closed === TRUE) {
            
            $door_status = 'Les portes de la ville sont <strong style="color:red">fermées</strong>';
            $door_button = $buttons->button('open_door');
        }
        else {
            
            $door_status = 'Les portes de la ville sont <strong style="color:green">ouvertes</strong>';
            $door_button = $buttons->button('close_door', true, 'formlink');
        }
        
        return '
            <div class="city_block">
                <h2>Porte de la ville</h2>
                <p>Partez en expédition dans le désert pour récuperer de précieuses ressources...</p>
                <p>' . $buttons->button('get_out_city') . '</p>
                <hr>
                <p>' . $door_status . '</p>
                <p>' . $door_button . '</p>
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * l'atelier (pour assembler des des objets)
     */
    function block_workshop($zone_items, $items_caracs)
    {
        
        $buttons = new HtmlButtons();
        $html_craftable_items = '';
        
        // Parcourt la liste des objets du jeu
        foreach($items_caracs as $item_id=>$caracs) {
            
            $compo_list = '';
            
            // empty et pas isset car un objet pourrait avoir l'attribut "craftable_from"
            // par défaut mais vide
            if (!empty($caracs['craftable_from'])) {
                
                foreach($caracs['craftable_from'] as $compo_id=>$required_amount) {
                    
                    $compo_list .= $this->html_progressbar( $compo_id, $items_caracs[$compo_id]['name'],
                                                            $this->item_amount($zone_items, $compo_id),
                                                            $required_amount,
                                                            'workshop');
                }
                
                $css_id = 'workshop'.$item_id;
                $html_craftable_items  .= '
                    <div onclick="toggle(\''.$css_id.'\')" class="foldable">
                        <h3>
                            <img src="../resources/img/copyrighted/items/'.$item_id.'.png" alt="icon_'.$item_id.'"> 
                            &nbsp;' . $caracs['name'] . '
                        </h3>
                        <div class="unfold_button">composants&nbsp;<div class="arrow">&#65088;</div></div>
                    </div>
                    <ul class="items_list folded" id="'.$css_id.'">
                    ' . $compo_list . '
                    <li>'.$buttons->craft($item_id).'</li>
                    </ul>';
            }
        }
        
        return '
            <div class="city_block" style="width:21.5em">
                <h2>Atelier</h2>
                <div class="descr">En assemblant des objets à l\'atelier, vous  
                    augmentez les défenses de la ville ou créez des ressources utiles 
                    pour <a onclick="switchCityTab(\'city_build\')">les chantiers communs</a>...
                </div>
                <div class="contents">
                '.$html_craftable_items.'
                </div>
            </div>';
    }
    
    
    /**
     * Affiche un ✓ ou ✘ devant une ressource pour montrer 
     * si on en a suffisamment ou non
     * 
     * @param  int $progress Le pourcentage de progression par rapport à la quantité 
     *                       nécessaire de ressources.
     *                       Si vaut 100, le signe sera ✓, sinon sera ✘.
     * @return string
     */
    private function html_check_sign($progress)
    {
        
        return  ($progress >= 100) 
                ? "<span style=\"font-size:1.1em;color:green;font-weight:bold;\">&check;</span>"
                : "<span style=\"font-size:1.1em;color:orangered;cursor:help\">&#x2718;</span>";
    }
    
    
   /**
    * Quantité disponible dans le dépôt de la ville pour un objet donné
    * 
    * @param  array $zone_items Liste des objets de la case (du dépôt)
    * @param  int   $item_id    L'ID de l'objet dont on veut connaître la quantité
    * @return int La quantié de l'objet
    */
    private function item_amount($zone_items, $item_id)
    {
        
        return  (isset($zone_items[$item_id])) ? ($zone_items[$item_id]) : 0;
    }
    
    /**
     * Affiche la barre de progression du stock d'un objet nécessaire 
     * pour fabriquer un objet ou construire un chantier
     * 
     * @param  string $item_name        Le nom de l'objet
     * @param  int    $available_amount La quantité disponible dans le dépôt
     * @param  int    $required_amount  La quantité requise pour la fabrication
     * @param  string $comment_for      Code indiquant quels textes explicatifs devront 
     *                                  être affichés dans les infobulles
     * @return string
     */
    private function html_progressbar($item_id, $item_name, $available_amount, $required_amount, $comment_for)
    {
        
        if ($comment_for === 'workshop') {
            
            $enough     = "Le dépôt de la ville contient suffisamment de ressources de ce type\n"
                        . "pour fabriquer l'objet.";
            $not_enough = "Vous devez accumuler davantage de ressources de ce type\n"
                        . "dans le dépôt de la ville avant de pouvoir fabriquer l'objet.";
        }
        elseif ($comment_for === 'constructions') {
            
            $enough     = "Le dépôt de la ville contient suffisamment de ressources de ce type\n"
                        . "pour envisager de construire ce chantier.";
            $not_enough = "Il n'y a pas assez de ressources de ce type\n"
                        . "dans le dépôt de la ville pour construire ce chantier.";
        }
        elseif ($comment_for === 'action_points') {
            
            $enough     = "";
            $not_enough = "Vous et les autres citoyens de la ville devez investir\n"
                        . "davantage de points d'action dans ce chantier\n "
                        . "pour achever sa construction et bénéficier de ses effets.";
        }
        
        // Calcule le taux de remplissage de la barre de progression
        $progress = round($available_amount/$required_amount * 100);
        
        $title        = ($progress >= 100) ? $enough : $not_enough;
        $bar_color    = ($progress >= 100) ? "lightgreen" : "sandybrown";
        $amount_color = ($progress >= 100) ? "green" : "orangered";
        
        // Quick fix sale pour un meilleur alignement selon le nombre de chiffres
        $nbsp = str_repeat('&nbsp;', 3-strlen($available_amount));
        
        return '
            <li class="item_label" style="cursor:help" title="'.$title.'">
                ' . $this->html_check_sign($progress, $comment_for) . '
                <var>
                    <img src="../resources/img/copyrighted/items/'.$item_id.'.png" alt="icon_'.$item_id.'"> 
                    <span class="progressbar_filling" style="width:'.$progress.'%;background:'.$bar_color.'">' . $item_name . '</span>
                </var>
                <span style="font-size:1.1em;color:'.$amount_color.'">'.$nbsp.$available_amount.'</span>&nbsp;<span style="color:grey;font-size:0.9em;">/&nbsp;'.$required_amount.'</span>
            </li>';
    }
}

