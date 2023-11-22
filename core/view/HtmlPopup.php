<?php
require_once 'HtmlButtons.php';


class HtmlPopup
{
    
    
    // Always define a constructor, even empty, to avoid the method 
    // with the same name as the class being considered as a constructor. 
    // PHP has fixed this strange behavior since v. 7.0.
    function __construct() {}
    
    
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
            alpha jouable est prévue pour <strong>la rentrée 2023</strong>.
            Rejoignez 
            <strong><a href=\"https://discord.gg/2GRPTyM\">le Discord d'InvaZion</a></strong>
            pour suivre son avancement !</p>";
            
            $button_close = '<p class="center"><a href="#" onclick="closePopup()">[Fermer]</a></p>';
        
        return $msg_popup.$button_close;
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
            <h2>Bâtiment du désert</h2>
            <img src="resources/img/copyrighted/tiles/desert/10.png" alt="image bâtiment" height="128px">
    <p>Vous avez découvert un bâtiment isolé : <strong class="building_name">{building_name}</strong></p>
            <p class="descr_ambiance">{descr_ambiance}</p>
            <p>Vous devriez le fouiller. Avec une peu de chance, vous y découvrirez 
            un objet rare...</p>
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
                        <li>• Chaque humain vaut 10 points</li>
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
                <p>&#x1F551; Vous y vivez actuellement votre <strong>".$params['current_cycle']."<sup>e</sup> jour</strong> de survie.</p>                
                <br>
                <fieldset><legend> Votre objectif </legend><br>
                Les zombies vont attaquer la ville ce soir. 
                Collectez des ressources et construisez des défenses afin de leur résister !<br>
                <p class=\"center\"><a href=\"#popattack\">[En savoir plus...]</a></p>
                </fieldset>";
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
