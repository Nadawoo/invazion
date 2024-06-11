<?php
require_once 'HtmlButtons.php';
require_once 'HtmlCollapsible.php';


class HtmlPopup
{
    
    
    // Always define a constructor, even empty, to avoid the method 
    // with the same name as the class being considered as a constructor. 
    // PHP has fixed this strange behavior since v. 7.0.
    function __construct() {}
    
    
    /**
     * The texts of the pop-ups
     * TODO: don't load all the pop-ups in the HTML on page load, only the useful one
     * 
     * @param string $msg_popup
     * @param int $map_id
     * @param int $citizen_id
     * @param array $configs_map
     * @param array $speciality_caracs
     * @param array $healing_items
     * @param string $html_smartphone
     * @param bool $is_custom_popup_visible
     * @return string HTML
     */
    public function allPopups($msg_popup, $map_id, $citizen_id, 
                    $configs_map, $speciality_caracs,
                    $healing_items, $html_smartphone, $is_custom_popup_visible) {
        
        return $this->predefined('poppresentation', '')
            . $this->predefined('poptasks', 'Objectifs')
            . $this->predefined('popdoor', '&#8505;&#65039; La porte de la ville')
            . $this->predefined('popdayclock', '', ['map_id'=>$map_id, 'current_cycle'=>$configs_map['current_cycle']])
            . $this->predefined('popvault',   '')
            . $this->predefined('popitems', '')
            . $this->predefined('popmycaracs', 'Mes caractéristiques', $speciality_caracs)
            . $this->predefined('popwounded', '', ['citizen_id'=>$citizen_id, 'healing_items'=>$healing_items])
            . $this->predefined('popcontrol', '&#8505;&#65039; Le contrôle de zone')
            . $this->predefined('popmove', '&#8505;&#65039; Les déplacements', 
                                    ['moving_cost_no_zombies' => $configs_map['moving_cost_no_zombies'], 
                                     'moving_cost_zombies'    => $configs_map['moving_cost_zombies']
                                    ])
            . $this->predefined('popattack', '&#8505;&#65039; L\'attaque zombie quotidienne')
            . $this->predefined('poppath', '')
            . $this->predefined('poppopulatepath', '')
            . $this->template_popbuilding($msg_popup)
            . $this->customised('popsmartphone', '', $html_smartphone)
            // Generic pop-up describing the result of an action
            . $this->customised('popsuccess', '', $msg_popup, $is_custom_popup_visible);
    }
    
    
    /**
     * Call this method to generate a pop-up with a predefined text.
     * The pop-up will stay hidden until the player clicks on the corresponding link 
     * generated by the method popup_link().
     * 
     * @param string $popup_alias  L'identifiant de la pop-up. Doit correspondre 
     *                             à celui utilisé par le lien d'appel (ex: href="#mypopup")
     *                             et à une méthode de la classe HtmlPopup (texte prédéfini)
     * @param string $title     Le titre de la pop-up (peut être vide)
     * @param array  $params    Paramètres additionnels pour certaines pop-up
     *                          (ex : le nom du citoyen)
     * @return string HTML
     */
    public function predefined($popup_alias, $title, $params=[])
    {
        
        // Puts the appropriate predefined text in the pop-up
        return $this->template($popup_alias, $title, $this->$popup_alias($params));
    }
    
    
    /**
     * Call this method to generate a pop-up with a customised text.
     * 
     * @param string $popup_alias
     * @param string $title
     * @param string $text  Free text to display in the pop-up
     * @return string HTML
     */
    public function customised($popup_alias, $title, $text, $is_popup_visible=false)
    {
        
        return $this->template($popup_alias, $title, $text, $is_popup_visible);  
    }
    
    
    /**
     * Call this to generate a link which opens a predefined pop-up
     * 
     * @param  string $popup_alias  L'identifiant de la pop-up
     * @param  string $text         Le texte du lien
     * @return string HTML
     */
    public function link($popup_alias, $text, $html_id="")
    {
        
        return '<a href="#'.$popup_alias.'"><input type="submit" value="'.$text.'" class="redbutton" id='.$html_id.'></a>';
    }
    
    
    private function poppresentation() {   
        
        $msg_popup = "
            <h3>Qu'est-ce qu'InvaZion ?</h3>
            <p>InvaZion est un <strong>jeu de survie</strong> collaboratif.
            Explorez le désert en solo ou en équipe, dénichez les précieuses ressources,
            construisez des défenses et survivez à l'attaque quotidienne !</p>
            <hr>
            <h3>D'où vient le concept ?</h3>
            <p>Invazion est un jeu indépendant inspiré de <strong>Hordes.fr</strong>,
            un jeu original du studio <em>Motion Twin</em> (connu aujourd'hui 
            pour son succès mondial <em>Dead Cells</em>). Motion Twin a autorisé
            la réutilisation du concept et des éléments graphiques, mais InvaZion
            n'est pas affilié à Motion Twin.<p>
            <hr>
            <h3>Pourquoi l'interface a-t-elle un aspect... rudimentaire ?</h3>
            <p>L'interface est volontairement minimaliste pour le moment. La priorité du projet 
            est de mettre à disposition le <strong>moteur central du jeu</strong>, à partir duquel 
            toute personne sachant programmer pourra développer sa propre interface graphique.</p>
            <hr>
            <h3>Quand pourra-t-on y jouer ?</h3>
            <p>Le jeu est en développement actif. Une première version
            alpha jouable est prévue pour <strong>la rentrée 2024</strong>.
            Rejoignez 
            <strong><a href=\"https://discord.gg/2GRPTyM\">le Discord d'InvaZion</a></strong>
            pour suivre son avancement !</p>";
            
            $button_close = '<p class="center"><a href="#" onclick="closePopup()">[Fermer]</a></p>';
        
        return $msg_popup.$button_close;
    }
    
    
    /**
     * Pop-up when we the player starts to create an expedition
     * 
     * @return string HTML
     */
    private function poppath() {   
        
        $msg_popup = 
              "<p>Une <strong>expédition</strong> est un chemin que vous tracez "
            . "sur la carte pour explorer les zones de votre choix. Vos citoyens "
            . "suivront ce chemin et ramasseront les objets trouvés automatiquement.</p>";
        
            $button_start = '<a href="#Outside" id="startPathCreation" class="bluebutton"'
                          . 'onclick="closePopup();startPathCreation()"'
                          . '>Tracer une expédition'
                          . '<i class="material-icons">chevron_right</i>'
                          . '</a>';
                    
        return $msg_popup . $button_start;
    }
    
    
    private function poppopulatepath() {   
        
        $msg_popup = 
              "<p>Avant de pouvoir lancer cette expédition, vous devez désigner "
            . "ses <strong>membres</strong>, c'est-à-dire les citoyens "
            . "qui suivront le chemin tracé sur la carte.</p>";
        
            $button_start = '<a href="#Outside" class="bluebutton" '
                          . 'onclick="closePopup();hideIds(\'paths_bar\');hideIds(\'attack_bar\');unhideId(\'paths_panel\')"'
                          . '>'
                          . 'Choisir les membres de l\'expédition'
                          . '<i class="material-icons">chevron_right</i>'
                          . '</a>';
            
        return $msg_popup . $button_start;
    }
    
    
    private function poptasks() {   
        
        $buttons = new HtmlButtons();
        
        $items = [
            [
            'icon'  => "&#x2753;",
            'title' => "Découvrir l'objectif du jeu",
            'text'  => "Les zombies attaquent la ville chaque soir. Vous devez
                        renforcer ses défenses en y construisant les chantiers disponibles.
                        Les matériaux nécessaires doivent être collectés hors de la ville,
                        dans le désert hostile.<br>
                        <a href=\"#popattack\">[En savoir plus...]</a>"
            ],
            [
            'icon'  => "&#x1F97E;",
            'title' => "Sortir de la ville",
            'text'  => "Sortez aux portes de la ville afin de préparer votre exploration 
                        du désert environnant."
            ],
            [
            'icon'  => "&#x1F9ED;",
            'title' => "Tracer une expédition",
            'text'  => "Tracez un itinéraire d'expédition qui vous permettra de déplacer
                        vos citoyens vers les zones du désert que vous voulez explorer."
            ],
            [
            'icon'  => "&#x1FAB5;",
            'title' => "Collecter des ressources",
            'text'  => "Déplacez-vous dans le désert et fouillez chaque zone sur votre chemin.
                        Ramassez les objets utiles que vous trouvez."
            ],
            [
            'icon'  => "&#x1F306;",
            'title' => "Ramener les ressources en ville",
            'text'  => "Ramenez au dépôt de la ville les objets que vous avez ramassés
                        au cours de votre exploration du désert."
            ],
            [
            'icon'  => "&#x1F9DF;",
            'title' => "Survivre à l'attaque du soir",
            'text'  => "Une fois que vous avez consommé 
                        les <a href=\"#popmove\"><strong>points d'action</strong></a>
                        de vos citoyens, vous pouvez déclencher
                        <a href=\"#popattack\"><strong>l'attaque zombie</strong></a>.
                        Si vous survivez, vos points d'action seront rechargés 
                        pour une nouvelle journée.
                        <p>".$buttons->button('end_cycle', false)."</p>"
            ],
        ];
        
        $htmlCollapsible = new HtmlCollapsible();
        
        return "
            <p class=\"aside\">
                [Note du développeur : ces tâches ne sont, pour le moment, 
                pas encore interactives (pas de filtrage en fonction du contexte,
                pas de liens d'aide à l'action, pas de masquage une fois terminées).
                Elles seront améliorées plus tard. En attendant, vous pouvez 
                les accomplir dans l'ordre de la liste.]
            </p>
            ".$htmlCollapsible->items($items);
    }
    
    
    /**
     * pop-up displayed when the citizen has been killed
     * 
     * @param string $unvalidated_death_cause The alias of the cause of death,
     *                                        as returned by the API
     * @return string HTML
     */
    public function popdeath($unvalidated_death_cause)
    {
        
        $buttons = new HtmlButtons();
        
        if($unvalidated_death_cause === 'outside') {
            $msg_popup = 
                  '<h2>Vous êtes mort !</h2>'
                . '<img src="resources/img/copyrighted/skull.png" alt="image crâne" style="height:120px">'
                . '<p>Les zombies vous ont dévoré dans le désert cette nuit !</p> '
                . '<p>Rappelez-vous que les villes et les tentes sont les seuls abris '
                . 'valables contre l\'attaques zombie quotidienne. '
                . 'La prochaine fois, pensez à rentrer en ville avant minuit...</p>';
        }
        elseif($unvalidated_death_cause === 'wound') {
            $msg_popup = 
                  '<h2>Vous êtes mort !</h2>'
                . '<img src="resources/img/copyrighted/skull.png" alt="image crâne" style="height:120px">'
                . '<p>Votre blessure s\'est infectée et la gangrène a eu raison de vous.</p> '
                . '<p>La prochaine fois, soignez vos blessures avant minuit...</p>';
        }
        elseif($unvalidated_death_cause === 'wingame') {
            $msg_popup = 
                '<h2>Félicitations, vous avez survécu !</h2>
                <img src="resources/img/copyrighted/map.png" alt="image carte" style="height:120px">
                <p>Votre ténacité et votre chance vous ont permis de vous enfuir
                de cette contrée hostile.</p>
                <p>Les payages désolés défilent lentement pendant que la voiture, 
                secouée par les chaos de la route, vous emmène vers un lieu
                plus sûr.</p>
                <p>Pourvu que le moteur tienne le coup...</p>';
        }
        else {
            $msg_popup .= '<p>[Bug: motif de mort inconnu]</p>';
        }
        
        $msg_popup .= $buttons->button('start_game', true, 'center') . '<br>'
                    . $buttons->button('validate_death', true, 'center');
        
        return $msg_popup;
    }
    
    
    public function popdoor()
    {
        
        $msg_popup = 
            '<p>La <strong>porte de la ville</strong> est un élément crucial du système 
            de défense. Si elle n\'est pas fermée au moment de l\'attaque zombie de minuit, 
            la ville sera <strong>sans défense</strong> !
            <ul class="expanded">
                <li><strong class="green-text">◄ Ouvrez ►</strong> la porte le matin, 
                    sortez explorer le désert et revenez en ville avec des ressources.</li>
                <li><strong class="darkred">►Fermez◄</strong> la porte le soir 
                    pour activer les défenses de la ville avant l\'attaque de minuit.
                    <a href="#popattack">[?]</a></li>
            </ul>';
        
        return $msg_popup;
    }
    
    
    /**
     * Pop-up to go into the car and win the game
     * 
     * @param string $error_message The evantual message returned by the API
     *                              when trying to win the game
     * @return string HTML
     */
    public function popcar($error_message)
    {
        
        $buttons = new HtmlButtons();
        
        $msg_popup = '<h2>Objectif final</h2>
            <img src="resources/img/copyrighted/tire.png" alt="image pneu">';
        
        $msg_popup .= 
            '<p>Vous avez découvert une voiture quasiment en état de marche. 
            Vous pourriez l\'utiliser pour vous enfuir et remporter la partie !</p>
            <p><strong>Cependant</strong>, le véhicule est endommagé et ne pourra démarrer qu\'après
            plusieurs réparations.</p>
            <p><strong>Trouvez les objets suivants</strong> en fouillant le désert,
            puis amenez-les ici :</strong></p>
            <ul>
                <li>1 pneu de voiture</li>
                <li>1 batterie</li>
                <li>1 jerrycan d\'essence</li>
            </ul>
            <p>'.$error_message.'</p>
            <p>'.$buttons->button('win_game', true, 'center').'</p>';
        
        return $msg_popup;
    }
    
    
    /**
     * Template for a pop-up when exploring the buildings in the desert
     * 
     * @param string $api_message The message returned by the API after the action
     */
    public function template_popbuilding($api_message)
    {
        
        $buttons = new HtmlButtons();
        
        return '
        <template id="tplPopupBuilding">
            <img src="resources/img/copyrighted/tiles/desert/10.png" alt="image bâtiment" height="128px">
            <p class="center">Vous avez découvert un bâtiment isolé :<br>
            <strong class="building_name" style="font-size:1.2em">{building_name}</strong></p>
            <p class="descr_ambiance">{descr_ambiance}</p>
            <p>Vous devriez l\'explorer. Avec un peu de chance, vous y trouverez 
            un de ces objets :</p>
            <ul class="items_list" style="justify-content:center"></ul>
            <br>
            <div style="background:green;color:white">'.$api_message.'</div>
            <br>
            '.$buttons->button('explore_building', true, 'center').'<br>
        </template>';
    }
    
    
    /**
     * Template for all the pop-ups. Don't call this directly.
     */
    private function template($popup_alias, $title, $text, $is_popup_visible=false)
    {
        
        // Fermer la pop-up ramènera au niveau de la carte de l'outre-monde.
        // Pour ramener en haut de la page, remplacer par "#".
        $anchor = '#Outside';
        
        $css_class = ($is_popup_visible === true) ? 'force_visibility' : '';
        
        return '
        <div id="'.$popup_alias.'" class="overlay '.$css_class.'">
            <div class="popup z-depth-2">
                <h2>'.$title.'</h2>
                <a class="close" href="'.$anchor.'" onclick="closePopup()">&times;</a>
                <div class="content">'.$text.'</div>
            </div>
        </div>';
    }
    
    
    /**
     * Help for the "Dig" button
     * 
     * @return string HTML
     */
    private function popitems()
    {
        
        return '
            <p>En <strong>fouillant le désert</strong>, vous collectez des objets 
                indispensables à votre survie :
            </p>
            <ul>
                <li><img src="/resources/img/copyrighted/items/1.png" width="32"> <strong>Ressources</strong> pour construire les défenses de votre ville<br><br></li>
                <li><img src="/resources/img/copyrighted/items/9.png" width="32"> <strong>Eau &amp; nourriture</strong> pour parcourir des longues distances<br><br></li>
                <li><img src="/resources/img/copyrighted/items/knife_2.png" width="32"> <strong>Armes</strong> pour vous frayer un chemin parmi les zombies<br><br></li>
                <li>Et d\'autres encore...</li>
            </ul>';
    }
    
    
    private function popmycaracs($speciality_caracs) {
        
        return '
            <p>La <strong>spécialité</strong> de votre citoyen est :
            <strong>'.ucfirst($speciality_caracs['name']).'</strong><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>('.$speciality_caracs['descr_purpose'].')</em>
            </p>
            <p>Cette spécialité vous confère ces caractéristiques :</p>
            <ul>
                <li>&#9889; Points d\'action par cycle : <strong>'.$speciality_caracs['action_points'].'</strong> &nbsp;<a href="#popmove">[?]</a></li>
                <li>&#127890; Places dans le sac : <strong>'.$speciality_caracs['bag_size'].'</strong></li>
                <li>&#128737;&#65039; Points de contrôle : <strong>'.$speciality_caracs['controlpoints_citizen'].'</strong> &nbsp;<a href="#popcontrol">[?]</a></li>'
//                &#x1F453; Vision niv. '.$citizen['vision'].'<br>
//                &#128374;&#65039; Camouflage niv. '.$citizen['camouflage'].'
            .'</ul>
            <br>';
    }
    
    
    /**
     * Text when you enter a crypt
     */
    private function popvault()
    {
        
        $buttons = new HtmlButtons();
        
        return '<img src="/resources/img/copyrighted/crypt.png" width="100px">
            <p class="descr_ambiance">Vous pénétrez dans la crypte obscure dont les murs irréguliers  
            suintent d\'un&nbsp;liquide indéterminé. L\'odeur des moisissures vous enveloppe 
            tandis que vous descendez les marches étroites...
            </p>
            <br>
            Cette découverte vous accorde une action à&nbsp;usage unique. Voulez-vous l\'utiliser 
            pour <strong>aider vos congénères humains...</strong><br>
            <ul>
                <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur les 7&nbsp;zones alentour</span></li>
                <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur 7&nbsp;zones aléatoires</span></li>
                <li>+ '.$buttons->button('reveal_zones', 'no_icon', 'formlink').'</li>
            </ul>
            <br>
            ...  ou bien pour <strong>propager davantage le chaos&nbsp;?</strong>
            <ul>
                <li>– '.$buttons->button('add_map_zombies', 'no_icon', 'formlink').'</li>
                <li>– <span style="color:grey;text-decoration:line-through">Obscurcir 10&nbsp;zones de la&nbsp;carte</span></li>
                <li>– <span style="color:grey;text-decoration:line-through">Détruire une&nbsp;ville aléatoire</span></li>
            </ul>';
    }
    
    
    /**
     * Text announcing you are wounded
     */
    private function popwounded($params)
    {
        
        $buttons = new HtmlButtons();
        
        if (empty($params['healing_items'])) {
            $html_healing = '<li><em>Votre sac ne contient aucun objet permettant de vous soigner pour le moment. 
                             Vous pourriez en trouver en explorant le désert...</em></li>';
        }
        else {
            $html_healing = '';
            foreach ($params['healing_items'] as $item_id=>$caracs) {
                 $html_healing .= '<li>'.$buttons->heal_citizen($params['citizen_id'], $item_id, true, 
                                                                'Me soigner avec '.$caracs['name']). '</li>';
            }
        }
        
        return '<img src="/resources/img/copyrighted/wound.png" width="100px">
            <p class="descr_ambiance">Une vilaine plaie ouverte parcourt votre cuisse droite, 
            ce n\'est pas beau à voir. La septicémie vous guette...</p>
            <br>
            <p><strong>Vous êtes blessé !</strong> Vous risquez de mourir 
            si vous ne vous soignez pas...</p>
            <ul>
                ' . $html_healing. '
            </ul>';
    }
    
    
    /**
     * Text to explain the points of control
     */
    private function popcontrol()
    {
        
        return "
            <div class=\"stageblock\">
                <div class=\"stageicon\">&#x1F465;</div>
                <div class=\"stagetext\">
                    Plus les humains sont nombreux dans votre zone, plus vous disposez 
                    de <strong>points de contrôle</strong> pour contenir les zombies.
                    <strong>Sortez groupés</strong> pour éviter les mésaventures...
                    <hr>
                </div>
            </div>
            <div class=\"stageblock\">
                <div class=\"stageicon\">&#x26A0;&#xFE0F;</div>
                <div class=\"stagetext\">
                    Si la somme des points de contrôle des zombies devient supérieure 
                    à celle des humains, <strong>vous ne pouvez plus quitter la zone !</strong>
                    <ul>
                        <li>• Chaque humain vaut 2 points</li>
                        <li>• Chaque zombie vaut 1 point.</li>
                    </ul>
                    <hr>
                </div>
            </div>
            
            <div class=\"stageblock\">
                <div class=\"stageicon\">&#x270A;&#x1F3FC;</div>
                <div class=\"stagetext\">
                    <strong>Si vous êtes bloqué</strong>, reprenez votre liberté en inversant le rapport de forces :
                    <ul>
                        <li>• soit en demandant à <strong>d'autres joueurs</strong> de vous rejoindre dans la zone 
                        (augmentera le contrôle des humains) ;</li>
                        <li>• soit en <strong>tuant des zombies</strong>
                        (réduira le contrôle des zombies).</li>
                    </ul>
                </div>
            </div>";
    }
    
    
    /**
     * Text to explain moving and action points
     */
    private function popmove($params)
    {
        
        return "<p>Explorer le désert exige de l'endurance.
            Vous avez besoin de &#9889;<strong>points d'action</strong> pour vous déplacer,
            en fonction du nombre de zombies dans la zone :
            </p>
            <ul>
                <li class=\"orange-text\">&#x1F9DF; <strong>1 zombie ou davantage</strong> &#x2794;
                    coût <strong>".$params['moving_cost_zombies']."</strong>&#9889;<br><br></li>
                <li class=\"green-text\">&#x2714;&#xFE0F; <strong>aucun zombie</strong> &#x2794; gratuit</li>
                </ul>
            <p>Surveillez vos points d'action ! Si vous restez bloqué dans le désert,
            une mort certaine vous attend cette nuit...</p>";
    }
    
    
    private function popattack()
    {
        
        return "<p>Chaque soir à minuit, la <strong>horde zombie</strong> déferle sur les villes.</p>
            <p>Si, à minuit, les <strong>défenses</strong> de votre ville 
            sont inférieures au nombre de zombies, vous et vos compagnons risquez
            d'être impitoyablement <strong>dévorés</strong> !</p>
            <ul class=\"expanded\">
                <li>&#x26CF;&#xFE0F; <strong>Fouillez</strong> le désert chaque jour ;</li>
                <li><img src=\"resources/img/free/city.svg\" alt=\"ville\" style=\"height:22px;\">
                    <strong>Rapportez</strong> en ville les objets utiles
                    que vous aurez trouvés ;</li>
                <li>&#x1F3D7;&#xFE0F; <strong>Construisez</strong> les défenses de la ville
                    en mettant en commun ces ressources.</li>
            </ul>";
    }
    
    private function popdayclock($params) {
        
        return "<p>&#x1F5FA;&#xFE0F; Vous êtes incarné sur la <strong>carte n° ".$params['map_id']."</strong>.<p>
                <p>&#x1F551; Vous y vivez actuellement votre <strong>".$params['current_cycle']."<sup>e</sup> jour</strong> de survie.</p>";
    }
    
    
    /**
     * Displays the in-game smartphone (which shows action points, radar, etc.)
     * 
     * @param string The HTML of the smartphone
     * @return string HTML
     */
    private function popsmartphone($html_smartphone)
    {
        
        return $html_smartphone;
    }
}
