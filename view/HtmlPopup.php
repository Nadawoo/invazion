<?php
require_once 'HtmlButtons.php';


class Popup
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
     *                             et à une méthode de la classe Popup (texte prédéfini)
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
    public function customised($popup_alias, $title, $text)
    {
        
        return $this->template($popup_alias, $title, $text);  
    }
    
    
    /**
     * Call this to generate a link which opens a predefined pop-up
     * 
     * @param  string $popup_alias  L'identifiant de la pop-up
     * @param  string $text         Le texte du lien
     * @return string HTML
     */
    public function link($popup_alias, $text)
    {
        
        return '<a href="#'.$popup_alias.'"><input type="submit" value="'.$text.'"></a>';
    }
    
    
    /**
     * Template for all the pop-ups. Don't call this directly.
     */
    private function template($popup_alias, $title, $text)
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
    
    
    /**
     * Text when you enter a crypt
     */
    private function popvault()
    {
        
        $buttons = new HtmlButtons();
        
        return '<p class="rp">Vous pénétrez dans la crypte obscure dont les murs irréguliers  
            suintent d\'un&nbsp;liquide indéterminé. L\'odeur des moisissures vous enveloppe 
            tandis que vous descendez les marches étroites...
            </p>
            Cette découverte vous accorde une action à&nbsp;usage unique. Voulez-vous l\'utiliser 
            pour aider vos congénères humains...<br>
            <ul>
                <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur les 7&nbsp;zones alentour</span></li>
                <li>+ <span style="color:grey;text-decoration:line-through">Exterminer les&nbsp;zombies sur 7&nbsp;zones aléatoires</span></li>
                <li>+ '.$buttons->api_link('reveal_zones', 'Dévoiler 10&nbsp;zones de la carte', '#popsuccess').'</li>
            </ul>
            <br>
            ...  ou bien pour propager davantage le chaos&nbsp;?
            <ul>
                <li>– '.$buttons->api_link('add_map_zombies', 'Ajouter des&nbsp;zombies sur toute la&nbsp;carte', '#popsuccess').'</li>
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
        
        return '<p class="rp">Une vilaine plaie ouverte parcourt votre cuisse droite, 
            ce n\'est pas beau à voir. La septicémie vous guette...</p>
            <p>Vous êtes blessé ! Vous risquez de mourir 
            si vous ne vous soignez pas...</p>
            <ul>
                <li>' . $buttons->heal_citizen($params['citizen_id'], null, true, 'Me soigner avec un bandage'). '</li>
            </ul>';
    }
    
    
    /**
     * Text to explain the points of control
     */
    private function popcontrol()
    {
        
        return "<p>&#x1F465; Plus les citoyens sont nombreux dans votre zone, plus vous disposez 
                de <strong>points de contrôle</strong> pour contenir les zombies.
                <strong>Sortez groupés</strong> pour éviter les mésaventures...
            </p>
            <hr/>
            <p>&#x26A0;&#xFE0F; Si la somme des points de contrôle des zombies devient supérieure 
                à celle des humains, <strong>vous ne pouvez plus quitter la zone !</strong>
            <p>
            <ul>
                <li>• Chaque humain vaut 10 points</li>
                <li>• Chaque zombie vaut 1 point.</li>
            </ul>
            <hr/>
            <p>&#x270A;&#x1F3FC; <strong>Si vous êtes bloqué</strong>, reprenez votre liberté en inversant le rapport de forces :</p>
            <ul>
                <li>• soit en demandant à <strong>d'autres joueurs</strong> de vous rejoindre dans la zone 
                (augmentera le contrôle des humains) ;</li>
                <li>• soit en <strong>tuant des zombies</strong>
                (réduira le contrôle des zombies).</li>
            </ul>";
    }
}
