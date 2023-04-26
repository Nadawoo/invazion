<?php
/**
 * Génère les éléments de l'intérieur de la ville
 */
class HtmlCityEnclosure
{
    
    
    /**
     * Generates the main menu inside the city (My home / The city / The desert)
     * 
     * @param int $map_id
     * @param int $city_id the ID of the main city, on which the home is connected
     * @param int $home_id The ID of the personal home of the player
     * @return string HTML
     */
    function city_menu($map_id, $city_id, $home_id)
    {
         return '
            <div id="city_tabs">
                <a onclick="switchCityMenu(\'cityMenuMyHome\');teleportToCity('.$map_id.', '.$home_id.')">Chez moi</a> &nbsp; 
                <a onclick="switchCityMenu(\'cityMenuCity\');  teleportToCity('.$map_id.', '.$city_id.')">La ville</a>
            </div>';
    }
    
    
    /**
     * Le menu horizontal pour les différentes parties de la ville
     * (dépôt, maison, porte...)
     * 
     * @param string $city_type
     * @param int $connected_city_id
     * @param bool $is_citizen_home_connected TRUE if the player has connected 
     *                                        his habitation to this city
     * @return string
     */
    function city_submenu($city_type, $connected_city_id, $is_citizen_home_connected)
    {
        
        $buttons = new HtmlButtons();
        
        // Content of the "Home" menu (ID #13 in the DB)
        if($city_type === 13 and $is_citizen_home_connected === false) {
            $home_menu = '<p style="max-width:25em;color:white">'
                       . 'Votre habitation n\'est pas reliée à cette ville.</p>';
        }
        else {            
            $home_menu = $this->city_submenu_item('home_house', 'Chez moi').'
                       '.$this->city_submenu_item('home_storage', 'Coffre').'
                       '.$this->city_submenu_item('home_build', 'Améliorer');
        }
        
        // Content of the "City" menu (ID #12 in the DB)
        if($city_type === 12 and $connected_city_id === null) {
            $city_menu = '<p style="max-width:25em;color:white">Une ville construite '
                . 'avec les autres joueurs offre des infrastructures précieuses '
                . 'pour la survie : défenses puissantes, réserves d\'eau...</p>'
                . $buttons->button('connect_tent');
        }
        else {
            $city_menu = '
                <div class="row">
                    '.$this->city_submenu_item('city_defenses', 'Défenses').'
                    '.$this->city_submenu_item('city_constructions', 'Chantiers').'
                    '.$this->city_submenu_item('city_fellows', 'Habitants').'
                    '.$this->city_submenu_item('explore', 'Sortir<br>Explorer').'
                </div>
                <div class="row">
                    '.$this->city_submenu_item('city_storage', 'Dépôt').'
                    '.$this->city_submenu_item('city_well', 'Puits').'
                    '.$this->city_submenu_item('city_workshop', 'Atelier').'
                    '.$this->city_submenu_item('city_door', 'Grande porte').'
                </div>';
        }
        
        
        return '
            <div id="city_submenus">
                <div class="row hidden" id="cityMenuMyHome">
                    '.$home_menu.'
                </div>
                <div class="hidden" id="cityMenuCity">
                    '.$city_menu.'
                </div>
            </div>';
    }
    
    
    private function city_submenu_item($item_alias, $item_name) {
        
        return '<div class="item" '
                  . 'style="background-image:url(\'resources/img/copyrighted/'.$item_alias.'.png\')" '
                  . 'onclick="switchCitySubmenu(\''.$item_alias.'\')">'
                  .'<span class="label">'. $item_name .'</span>'
                .'</div>';
    }
    
    
    function button_close_block() {
        
        return '<button class="close_city_blocks" onclick="hideCityBlocks();display(\'city_iso\')">&times;</button>';
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
    function block_city_storage($html_zone_items)
    {
        
        if ($html_zone_items === '') {
            
            $html_storage_items = '<div class="grey">
                    <br>Aucun objet dans le dépôt&nbsp;!
                    Vous devriez y déposer quelques objets personnels pour le remplir...
                </div>';
        }
        else {
            
            $html_storage_items = $html_zone_items;
        }
        
        return '
                <h2>Dépôt</h2>
                <div class="descr">Les joueurs déposent ici les objets trouvés 
                    lors de leurs <a onclick="switchCityTab(\'city_door\')">expéditions</a>. Utilisez-les pour 
                    <a onclick="switchCityTab(\'city_constructions\')">construire la ville</a>... 
                    ou <a onclick="switchCityTab(\'city_perso\')">votre propre habitation</a>.
                </div>
                <div class="contents">
                '. $html_storage_items .'
                </div>';
    }
    
    
    /**
     * Displays the personal chest of the player
     * 
     * @param  string $html_zone_items La liste HTML des objets de la zone, générée
     *                                 par la classe HtmlLayout->zone_items()
     * @return string
     */
    function block_home_storage($html_zone_items)
    {
        
        if ($html_zone_items === '') {
            
            $html_storage_items = '<div class="grey">
                    <br>Aucun objet dans votre coffre&nbsp;!
                    Explorez le désert pour le remplir d\'objet utiles...
                </div>';
        }
        else {
            
            $html_storage_items = $html_zone_items;
        }
        
        return '
                <h2>Mon coffre</h2>
                <div class="descr">Ce coffre vous permet de mettre à l\'abri les ressources 
                    ramenées de vos <a onclick="switchCityTab(\'city_door\')">expéditions</a>.
                    Utile pour améliorer <a onclick="switchCityTab(\'home_house\')">votre habitation</a>...
                </div>
                <div class="contents">
                '. $html_storage_items .'
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
        
        $html_citizens = '';
        
        foreach($fellows as $citizen) {
            
            $localization = ($citizen['distance_to_city'] === 0)
                ? 'en ville'
                : '<span class="highlight">à&nbsp;'.$citizen['distance_to_city'].'&nbsp;km</span>';
            
            $wound = ($citizen['is_wounded'] === 0) ? '' : '<li><strong class="red">est blessé</strong></li>';
            
            $html_citizens .= '
                <div class="city_block" onclick="toggleHouse(\'citizen'.$citizen['citizen_id'].'\')">
                    <h2>Joueur</h2>
                    <img src="resources/img/free/human.png" style="height:32px">&nbsp;
                    <a><strong style="font-size:1.8em">'.$citizen['citizen_pseudo'].'</strong></a>
                    <ul>
                        <li>est spécialisé <span class="highlight">'.$specialities[$citizen['speciality']]['name'].'</span></li>
                        <li>se trouve <span class="highlight">'.$localization.'</span></li>
                        '.$wound.'
                    </ul>
                    <div class="icons">
                        <img src="resources/img/copyrighted/waggon_45px.png">
                        <img src="resources/img/copyrighted/supplies_45px.png">
                        <img src="resources/img/copyrighted/paper_45px.png">
                        <img src="resources/img/copyrighted/disapproval_45px.png">
                    </div>
                </div>';
        }
        
        // Empty cards if the city has free places
        for($i=count($fellows);$i<6;$i++) {            
            $html_citizens .= '<div class="city_block empty_block"></div>';
        }
        
        return '<div id="citizens_list">'.$html_citizens.'</div>';
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
                    pour bâtir <a onclick="switchCityTab(\'city_constructions\')">des chantiers communs</a>... 
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
     * @param array $constructions_components 
     * @param array $items_caracs         Les caractéristiques statiques des objets
     * @param array $city_constructions   L'état des chantiers de la ville (avancement...)
     * @param array $zone_items           Les objets disponibles dans le dépôt de la ville
     *                                    (qui sont, à ce jour, les objets au sol)
     * @return string
     */
    function block_constructions($constructions_caracs, $constructions_components, $items_caracs,  
                                  $city_constructions, $completed_buildings_ids, $zone_items)
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
            foreach ($constructions_components[$id] as $item_id=>$required_amount) {
                
                $html_resources .= $this->html_progressbar( $item_id, $items_caracs[$item_id]['name'],
                                                            $this->item_amount($zone_items, $item_id),
                                                            $required_amount,
                                                            'constructions');
            }
            
            if (in_array($id, $completed_buildings_ids)) { 
                
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
                                ' . $buttons->construct($id, 'no_notif') . '
                            </ul>
                        </td>
                    </tr>';
            }

        }
        
        return '
            <div class="city_block" style="width:21.5em">
                <h2>Tous les chantiers</h2>
                <table id="constructions">
                    <tr style="font-size:0.9em">
                        <th></th>
                        <th>Défenses</th>
                    </tr>
                    '.$html_constructions.'
                </table>
            </div>';
    }
    
    
    /**
     * The two bars comparing the defenses and the zombies
     */
    function defenses_bar($total_defenses, $zombies_next_attack) {
        
        $zombies_overflow = max(0, $zombies_next_attack - $total_defenses);
        $total = ($total_defenses + $zombies_next_attack);
        
        $defense_percent = $total_defenses / $total * 100;
        $zombies_percent = $zombies_next_attack / $total * 100;
        
        return '
            <div id="defenses_bar">
                <div style="background:lightgreen">
                    <div style="padding-left:0.2em;background:darkgreen;width:'.$defense_percent.'%">
                        Nos défenses '.$total_defenses.'
                    </div>
                </div>
                <div style="background:salmon">                        
                    <div style="padding-left:0.2em;background:darkred;width:'.$zombies_percent.'%;">
                        Zombies attendus '.$zombies_next_attack.'
                    </div>
                </div>
            </div>
            <div style="color:red;font-weight:bold;padding:0.3em 0 1em 0;">
                &#9888;&#65039; '.$zombies_overflow.' défenses manquantes !
            </div>';
    }
    
    
    /**
     * The door of the city
     * 
     * @param int $is_door_closed   Is 1 if the door is closed (true)
     *                              Is 0 if the door is open (false)
     * @return string HTML
     */
    function block_city_door($is_door_closed)
    {
        
        $buttons = new HtmlButtons();
        
        if ((bool)$is_door_closed === true) {            
            $door_status = 'Les portes de la ville sont <strong style="color:red">fermées</strong>';
            $door_button = $buttons->button('open_door');
        }
        else {            
            $door_status = 'Les portes de la ville sont <strong style="color:green">ouvertes</strong>';
            $door_button = $buttons->button('close_door');
        }
        
        return '
            <div class="city_block">
                <h2>Porte de la ville</h2>
                <p><em>Fermez la porte le soir pour activer les défenses de la ville,
                    mais n\'oubliez personne dehors...</em></p>
                <p>' . $door_status . '</p>
                <p>' . $door_button . '</p>
            </div>';
    }
    
    
    /**
     * Summary of the number of defense points owned by the city
     * 
     * @param int $total_defenses Total des points de défense de la ville
     *                            (somme des points de tous les chantiers achevés)
     * @param int $zombies_next_attack Amount of zombies which will attack the city
     * @return string HTML
     */
    function block_defenses($total_defenses, $zombies_next_attack)
    {
        
        return '
            <div class="city_block">
                <h2>Défenses</h2>
                <p class="descr">Objectif : construire suffisamment de défenses  
                    pour repousser l\'attaque zombie de ce soir.</p>
                '.$this->defenses_bar($total_defenses, $zombies_next_attack).'
                <hr>
                <p style="font-size:0.9em"><em>Pour augmenter les défenses, 
                    construisez de nouveaux chantiers.</em></p>
                <strong><a onclick="switchCitySubmenu(\'city_constructions\')">Améliorer les défenses</a></strong>
            </div>';
    }
    
    
    /**
     * Block to get out of the city
     * 
     * @return string HTML
     */
    function block_explore()
    {
        
        $buttons = new HtmlButtons();
        
        return '
            <div class="city_block">
                <h2>Sortir</h2>
                <p>Partez en exploration pour récupérer les ressources
                    indispensables à la survie de la ville...</p>
                <p>' . $buttons->button('get_out_home') . '</p>
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
                    pour <a onclick="switchCityTab(\'city_constructions\')">les chantiers communs</a>...
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

