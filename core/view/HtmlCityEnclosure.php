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
                <div class="row" style="gap:0.3em">
                    '.$this->city_submenu_item('city_storage', 'Dépôt').'
                    '.$this->city_submenu_item('city_constructions', 'Chantiers').'
                    '.$this->city_submenu_item('city_fellows', 'Habitants').'
                    '.$this->city_submenu_item('explore', 'Explorer').'
                </div>
                <div class="row" style="font-size:0.7em">
                    '.$this->city_submenu_item('city_defenses', 'Défenses').'
                    '.$this->city_submenu_item('city_well', 'Puits').'
                    '.$this->city_submenu_item('city_workshop', 'Atelier').'
                    '.$this->city_submenu_item('city_door', 'Porte').'
                    '.$this->city_submenu_item('empty', '').'
                    '.$this->city_submenu_item('empty', '').'
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
        
        // Special images
        if($item_alias === 'empty') {
            return '<div class="item" style="background:none;cursor:default"></div>';
        }
        
        $icon_path = ($item_alias === 'explore') ? 'free/map.png' : 'copyrighted/'.$item_alias.'.png';
        
        return '<div class="item" style="background-image:url(\'resources/img/'.$icon_path.'\')" '
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
    function block_well($well_construction_id, $well_current_water)
    {
        
        $buttons = new HtmlButtons;
        
        return '
            <div class="city_block">
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_well.png">
                <h2>Puits</h2>
                <div class="descr">Buvez régulièrement de l\'eau pour ne pas mourir de soif.</div>
                <br>
                <br>
                <strong style="font-size:1.8em;color:navy;">'.$well_current_water.'&nbsp;rations&nbsp;/&nbsp;50</strong>
                <br>
                <br>
                <br>
                '.$buttons->well_pickup($well_construction_id).'
                '.$buttons->well_drop($well_construction_id).'
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
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_storage.png">
                <h2>Dépôt</h2>
                <div class="descr">Les joueurs déposent ici les objets trouvés 
                    lors de leurs <a onclick="switchCityTab(\'city_door\')">expéditions</a>. Utilisez-les pour 
                    <a onclick="switchCityTab(\'city_constructions\')">construire la ville</a>... 
                    ou <a onclick="switchCityTab(\'city_perso\')">votre propre habitation</a>.
                </div>
                <ul class="items_list">
                    '. $html_storage_items .'
                </ul>';
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
        
        $buttons = new HtmlButtons();
        $html_citizens = '';
        
        foreach($fellows as $citizen) {
            
            $localization = ($citizen['distance_to_city'] === 0)
                ? 'en ville'
                : '<span class="highlight">à&nbsp;'.$citizen['distance_to_city'].'&nbsp;km</span>';
            
            $wound = ($citizen['is_wounded'] === 0) ? '' : '<li><strong class="red-text">est blessé !</strong></li>';
            
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
                    '.$buttons->switch_citizen('switch_citizen', $citizen['citizen_id']).'
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
    
    
    function block_constructions($config_buildings, $buildings_components, $items_caracs,  
                                  $completed_buildings_ids, $zone_items, $root_building_id)
    {
        
        $html_constructions = $this->block_constructions_list($config_buildings, $buildings_components, $items_caracs,  
                                  $completed_buildings_ids, $zone_items, $root_building_id);
        
        return '
            <div id="constructions_block" class="city_block" style="width:21.5em">
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_constructions.png">
                <h2>Tous les chantiers</h2>
                <p class="descr">Construisez les chantiers pour améliorer les défenses ou d\'autres équipements de la ville.<br>
                    <a href="#popattack">[En savoir plus...]</a>
                </p>
                
                <p>
                    <a class="bluebutton" onclick="display(\'tip_buildable\');display(\'to_constructions\');toggle(\'#constructions_block\')">&#x1F4A1; Conseil : Chantiers constructibles</a>
                    <a class="bluebutton" onclick="display(\'tip_resources\');display(\'to_constructions\');toggle(\'#constructions_block\')">&#x1F4A1; Conseil : Ressources à compléter</a>
                </p>
                
                <p style="margin-left:0.5em;text-align:left"><strong>Filtre :</strong>
                    <select id="constructionFilter" style="width:7em">
                        <option value="none" selected>Aucun</option>
                        <option value="components">Composants manquants</option>
                        <option value="effects">Effets du chantier</option>
                    </select>
                </p>

                <table id="constructions">
                    '.$html_constructions.'
                </table>
            </div>';
    }
    
    
    /**
     * Bloc à l'intérieur de la ville :
     * liste des chantiers construits ou constructibles en ville
     * 
     * @param array $config_buildings The characteristics of all the buildings (name...), 
     *                                as returned by the "configs" API
     * @param array $buildings_components The resources needed to build each construction,
     *                                    as returned by the "configs" API
     * @param array $completed_buildings_ids The IDs of the constructions achieved in this city
     * @param array $items_caracs         Les caractéristiques statiques des objets
     * @param array $zone_items The items available in the city storage (which are,
     *                          at the moment, the items on the ground)
     * @param int $root_building_id The ID of the parent building
     * @return string
     */
    public function block_constructions_list($config_buildings, $buildings_components, $items_caracs,  
                                             $completed_buildings_ids, $zone_items, $root_building_id,
                                             $child_level=0) {
        
        $sort = new SortGameData();
        $html_constructions = '';
        // Keep only the game's buildings related to the city (ID #12)
        $buildings_caracs = $sort->filter_buildings_by_parent($config_buildings, $root_building_id);
        
        foreach ($buildings_caracs as $building_id=>$building) {
            // ID of the "Action points" item in the database
            $ap_item_id = 23;
            // Set default building description if not defined
            $building_descr = ((string)$building['descr_ambiance'] !== '') ? $building['descr_ambiance'] : '<span class="grey-text">(Pas de description pour le moment)</span>';
            // Determine if the construction is completed
            $status = in_array($building_id, $completed_buildings_ids) ? 'achieved' : 'in_progress';
            
            $building_effects = ($building['defenses'] > 0)
                                ? '<ul><li>&#x1F6E1;&#xFE0F; Ce chantier apporte <strong>'.$building['defenses'].' défenses</strong> à la ville une fois construit.</li></ul>'
                                : '';
            
            // If the construction is achieved
            if($status === 'achieved') { 
                $html_components = '&#9989; <em>Ce chantier est déjà construit !</em>';
            }
            else {
                // Put the action points apart of the other resources
                $html_ap = $this->block_construction_actionpoints($building_id, $items_caracs, $buildings_components,
                                                                  $zone_items, $ap_item_id);
                $html_resources = $this->block_construction_resources($building_id, $items_caracs, $buildings_components, 
                                                                  $zone_items, $ap_item_id);
                $html_components = $html_resources . $html_ap;
            }
            
            $html_constructions .= 
                $this->block_construction_foldable($building_id, $building['name'], $building['defenses'],
                                                   $building['icon_path'], $building['icon_html'], 
                                                   $status, $child_level, $buildings_components, $items_caracs, $zone_items).'
                <tr id="building'.$building_id.'" class="folded">
                    <td>
                        <ul class="tabs">
                            <li class="tab"><a href="#tabDescr'.$building_id.'" class="active">Description</a></li>
                            <li class="tab"><a href="#tabResources'.$building_id.'">Composants</a></li>
                        </ul>
                        <ul class="items_list" id="tabDescr'.$building_id.'">
                            <li><em>'.$building_descr.'</em></li>
                            '.$building_effects.'
                        </ul>
                        <ul class="items_list" id="tabResources'.$building_id.'">
                            ' . $html_components . '
                        </ul>
                    </td>
                </tr>';
            
            // Call the method recursively to display all the child constructions
            if($status === 'achieved') { 
                $html_child_constructions = true;
                if($html_child_constructions !== '') {
                    $child_level++;
                    $html_child_constructions = $this->block_constructions_list($config_buildings, $buildings_components, $items_caracs,  
                                                                  $completed_buildings_ids, $zone_items, $building_id,
                                                                  $child_level);
                    $html_constructions .= $html_child_constructions;
                }
                $child_level--;
            }
        }
        
        return $html_constructions;
    }
    
    
    /**
     * The name of one construction. Clicking on it will unfold informations  
     * about the resources needed to build it.
     * 
     * @param int $building_id
     * @param string $building_name
     * @param int $building_defenses
     * @param string $building_icon_path
     * @param string $building_icon_html
     * @param array $status The progression of the construction: 
     *                      "in_progress" or "achieved"
     * @param int $child_level The number of parent constructions above the given construction
     *                         in the dependency tree
     * @param array $buildings_components
     * @return string HTML
     */
    private function block_construction_foldable($building_id, $building_name, $building_defenses, 
                                                 $building_icon_path, $building_icon_html, 
                                                 $status, $child_level, 
                                                 $buildings_components, $items_caracs, $zone_items) {
        
        $htmlItem = new HtmlItem();
        // The action points got the item ID #23 in the database
        $ap_item_id = 23;
        $components = isset($buildings_components[$building_id]) ? $buildings_components[$building_id] : [];
        arsort($components);
        $nbr_actionpoints_needed = isset($components[$ap_item_id]) ? $components[$ap_item_id] : 0;
        $nbr_components_needed = array_sum($components) - $nbr_actionpoints_needed;
        $nbr_components_available = array_sum(array_intersect_key($zone_items, $components));
        $nbr_components_gathered = min($nbr_components_needed, $nbr_components_available);
        $components_percent = $nbr_components_gathered/max(1,$nbr_components_needed)*100;
        // Quick fix: force minimal components amount to avoid progressbar overflow 
        // on the left when 0 component gathered
        $components_percent = max(10, $components_percent);
        
        if($status === 'achieved') {
            $progressbar_bg = '';
            $bg_color    = 'green';
            $text_color  = 'lightgreen';
            $html_status = '&check; Construit !';
            $html_resources = '<span class="components hidden" style="justify-content:center">.</span>';
        }
        elseif($nbr_components_gathered >= $nbr_components_needed) {
            $progressbar_bg = '';
            $bg_color    = 'darkred';
            $text_color  = 'white';
            $html_status = '<a style="font-size:1.3em;color:white">&#9889;Constructible&nbsp;<span class="arrow">&#65088;</span></a>';
            $html_resources = $this->block_construction_resources_column($components, $zone_items, $items_caracs);
        }
        else { // Status "in progress"
            $progressbar_bg = '#E67E22';
            $bg_color    = '';
            $text_color  = '#263238';
            $html_status = '<a>'.$nbr_components_gathered.'/'.$nbr_components_needed.' composants&nbsp;<span class="arrow">&#65088;</span></a>';
            $html_resources = $this->block_construction_resources_column($components, $zone_items, $items_caracs);
        }
        
        $html_defenses = ($building_defenses > 0) ? '+'.$building_defenses.'&#x1F6E1;&#xFE0F;' : '.';
        $building_image = $htmlItem->icon($building_icon_path, $building_icon_html);
        
        return '
            <tr>
                <td onclick="toggle(\'#building'.$building_id.'\');hideClasses([\'defenses\'])" class="foldable '.$status.'" style="margin-left:'.($child_level*1.4).'em;background:'.$bg_color.'">
                    '.str_repeat('<span class="hierarchy">├</span>', $child_level).'
                    '.$building_image.'
                    <div class="label">
                        <h3 style="color:'.$text_color.'">&nbsp;'.$building_name.'</h3>
                        <div class="unfold_button" style="color:'.$text_color.'">'.$html_status.' &nbsp;</div>
                    </div>
                    <div class="progressbar_filling" style="background-color:'.$progressbar_bg.';width:'.$components_percent.'%"></div>
                </td>
                <td class="defenses hidden" style="background:'.$bg_color.';color:'.$text_color.'"
                    onclick="toggle(\'#building'.$building_id.'\');hideClasses([\'defenses\'])">
                    '.$html_resources.'
                    <strong class="construction_defenses hidden" style="color:'.$text_color.'"
                        title="Ce chantier augmente les défenses de la ville lorsqu\'il est construit.">
                        '.$html_defenses.'
                    </strong>
                </td>
            </tr>';
    }
    
    
    /**
     * HTML for the components in the constructions tree (not to be confused with 
     * the components displayed inside the foldable details of the constructions)
     * 
     * @param array $building_components
     * @param array $zone_items
     * @param array $items_caracs
     * @return string HTML
     */
    private function block_construction_resources_column($building_components, $zone_items, $items_caracs) {
        
        $htmlItem = new HtmlItem();
        // The action points got the item ID #23 in the database
        $ap_item_id = 23;
        $are_all_components_stored = true;
        
        $html_resources = '';
        foreach($building_components as $item_id=>$required_amount) {
            // Handles the anormal case where the resource needed is not 
            // in the list of items set for the current game 
            if(!isset($items_caracs[$item_id])) {
                $items_caracs[$item_id] = set_default_variables('item');
            }
            
            $zone_item_amount = isset($zone_items[$item_id]) ? $zone_items[$item_id] : 0;
            $missing_item_amount = $required_amount - $zone_item_amount;
            
            if($missing_item_amount > 0 and $item_id !== $ap_item_id) {
                $are_all_components_stored = false;
            }
            
            $html_missing_amount = $missing_item_amount;
            $background = '';
            if($missing_item_amount <= 0) {
                $html_missing_amount = '&#x2705;';
                $background = 'lightgreen';
            }
            
            $item_icon = $htmlItem->icon($items_caracs[$item_id]['icon_path'], $items_caracs[$item_id]['icon_symbol']);
            
            $html_resources .= '<li class="item_label" style="background:'.$background.'">
                    '.$item_icon.'<span class="dot_number">'.$html_missing_amount.'</span>
                </li>';
        }
        
        if($are_all_components_stored === true) {
            return '<div class="components">
                    <button class="redbutton" style="min-width:auto">Construire...</button>
                </div>'; 
        }
        
        return '<ul class="items_list components">
                    '.$html_resources.'
                </ul>';
    }
    
    
    /**
     * Jauge de chaque ressource requise/disponible pour la construction
     * 
     * @param int $building_id
     * @param array $items_caracs
     * @param array $buildings_components
     * @param array $zone_items
     * @param int $ap_item_id
     * @return string HTML
     */
    private function block_construction_resources($building_id, $items_caracs, $buildings_components, $zone_items, $ap_item_id) {
        
        $html_resources = '';
        // If no components have been defined for this building
        $components = isset($buildings_components[$building_id])
                      ? $buildings_components[$building_id]
                      : [];
        
        // Remove the action points from the list of components
        unset($components[$ap_item_id]);
        
        foreach ($components as $item_id=>$required_amount) {
            // Handles the anormal case where the resource needed is not 
            // in the list of items set for the current game 
            if(!isset($items_caracs[$item_id])) {
                $items_caracs[$item_id] = set_default_variables('item');
            }

            $html_resources .= $this->html_progressbar( $items_caracs[$item_id],
                                                        $this->item_amount($zone_items, $item_id),
                                                        $required_amount,
                                                        'constructions');
        }
        
        return  $html_resources;
    }
    
    
    /**
     * Jauge des points d'actions investis dans la construction
     * 
     * @param int $building_id
     * @param array $items_caracs
     * @param array $buildings_components
     * @param array $zone_items
     * @param int $ap_item_id
     * @return string HTML
     */
    private function block_construction_actionpoints($building_id, $items_caracs, $buildings_components, $zone_items, $ap_item_id) {
        
        $required_ap = isset($buildings_components[$building_id][$ap_item_id])
                        ? $buildings_components[$building_id][$ap_item_id]
                        : 0;
        
        return $this->html_progressbar( $items_caracs[$ap_item_id],
                                        $this->item_amount($zone_items, $ap_item_id),
                                        $required_ap, 
                                        'constructions', $building_id);
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
            $door_status = 'Les portes de la ville sont <strong class="green-text">ouvertes</strong>';
            $door_button = $buttons->button('close_door');
        }
        
        return '
            <div class="city_block">
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_door.png">
                <h2>Grande porte</h2>
                <p class="descr">Fermez la porte de la ville chaque soir pour activer les défenses.
                    N\'oubliez personne dehors...</p>
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
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_defenses.png">
                <h2>Défenses</h2>
                <p class="descr">Objectif : construire suffisamment de défenses  
                    pour repousser l\'attaque zombie de ce soir.<br>
                    <a href="#popattack">[En savoir plus...]</a>
                </p>
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
                <img class="icon z-depth-1" src="resources/img/copyrighted/explore.png">
                <h2>Exploration</h2>
                <p class="descr">Les ressources indispensables à la survie se trouvent 
                    à l\'extérieur de la ville. Équipez-vous en eau, nourriture 
                    et armes avant de sortir...
                </p>
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
                    
                    $compo_list .= $this->html_progressbar( $items_caracs[$compo_id],
                                                            $this->item_amount($zone_items, $compo_id),
                                                            $required_amount,
                                                            'workshop');
                }
                
                $css_id = '#workshop'.$item_id;
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
                <img class="icon z-depth-1" src="resources/img/copyrighted/city_workshop.png">
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
     * Displays the amount of resources available to build the construction
     * 
     * @param  int $required_amount  The amount of this component required 
     *                               to build the construction
     * @param  int $available_amount The amount of this component available 
     *                               in the city storage
     * @return string
     */
    private function html_component_amount($required_amount, $available_amount)
    {
        
        return  ($available_amount >= $required_amount) 
                ? '<div class="amounts valign-wrapper" style="background:#D5F5E3;border-color:#81C784">
                        <span class="available" style="color:green">'.$required_amount.'</span>
                        <span class="required">&#9989;</span>
                    </div>'
                : '<div class="amounts valign-wrapper">
                        <span class="available" style="color:orangered">'.$available_amount.'</span>&nbsp;
                        <span class="required" style="font-size:0.9em">/'.$required_amount.'</span>
                    </div>';
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
     * @param  array $item_caracs      La liste des caractéristiques de l'objet, 
     *                                  telles que retournées par l'API
     * @param  int    $available_amount La quantité disponible dans le dépôt
     * @param  int    $required_amount  La quantité requise pour la fabrication
     * @param  string $comment_for      Code indiquant quels textes explicatifs devront 
     *                                  être affichés dans les infobulles
     * @param  int    $building_id
     * @return string
     */
    private function html_progressbar($item_caracs, $available_amount, $required_amount, 
                                      $comment_for, $building_id=null)
    {
        
        $htmlItem = new HtmlItem();
        $button_name = $item_caracs['name'];
        $disabled = 'disabled';
        $redbutton = '';
        $progressbar_unfilled_color = '#E67E22';
        // If the resource is a clickable button (useful to invest action points 
        // in the construction)
        if($building_id !== null) {
            $button_name = 'Participer au chantier [1&#9889;]';
            $disabled = '';
            $redbutton = 'redbutton';
            $progressbar_unfilled_color = '#CC0000';
        }
        
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
        
        $missing_amount = max(0, $required_amount-$available_amount);
        $title = ($missing_amount <= 0) ? $enough : $not_enough;
        $progress = round($available_amount/max(1, $required_amount) * 100);
        $progressbar_color = ($progress >= 100) ? "lightgreen" : $progressbar_unfilled_color;
        
        $item_icon = $htmlItem->icon($item_caracs['icon_path'], $item_caracs['icon_symbol'], 32);
        
        return '
            <li style="display:flex;position:relative;height:3em" title="'.$title.'">
                <form method="post" action="#popsuccess">
                    <input type="hidden" name="api_name" value="buildings">
                    <input type="hidden" name="action" value="build">
                    <input type="hidden" name="params[building_id]" value="'.$building_id.'">
                    <button type="submit" class="item_label z-depth-1 '.$redbutton.'" '.$disabled.'>
                        <span class="progressbar_filling" style="width:'.$progress.'%;background:'.$progressbar_color.'">
                        &nbsp;'.$item_icon.'&nbsp;'.$button_name.'
                        </span>
                    </button>
                </form>
                '.$this->html_component_amount($required_amount, $available_amount).'
            </li>';
        
//                <var>
//                    '.str_repeat('<span>'.$item_icon.'</span>', $available_amount).'
//                    '.str_repeat('<span style="opacity:0.3">'.$item_icon.'</span>', $missing_amount).'
//                </var>
    }
}

