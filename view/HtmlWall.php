<?php
/**
 * Displays the wall gathering the discussions and the log of events happening 
 * in the city 
 */
class HtmlWall
{    
    
    /**
     * Mainn method. Call this to display the wall.
     * 
     * @param string $citizen_pseudo The pseudo of the currently connectd player
     * @return type
     */
    public function wall($citizen_pseudo)
    {
        
        return '<div id="wall" class="city_block">
                <h2 id="enlarge_wall">
                    <div class="arrow">&#8963;</div>
                    Communications
                </h2>
                <div class="contents">
                    
                    '.$this->tabs().'                    
                    '.$this->new_discussion_form($citizen_pseudo).'                    
                    '.$this->event_construction_completed().'                        
                    '.$this->event_AP_invested('Nadawoo', 'Mur de renfort', 3).'
                    
                    <div id="citizenPseudo" style="display:none">'.$citizen_pseudo.'</div>
                    <div id="discussions"></div>
                    
                </div>
            </div>';
    }
    
    
    private function tabs()
    {
        
        return '<nav style="margin-bottom:0.9em">
                    <a id="tabWallAll" class="active_tab">Tout</a> 
                    <a id="tabWallDiscuss">Discussions</a> 
                    <a id="tabWallEvents">Evénéments</a>
                </nav>';
    }
    
    
    /**
     * Form to open a new discussion thread
     * 
     * @param string $citizen_pseudo
     * @return string HTML
     */
    private function new_discussion_form($citizen_pseudo)
    {
        
        return '
            <div id="send" class="topic discuss">
                <div class="message">
                    <a href="#" style="font-weight:bold;font-size:0.9em;" onclick="display(\'sendform\');this.hidden=true;return false">
                        &#x270F;&#xFE0F; Ajouter un message...
                    </a>
                    <form id="sendform" style="display:none">
                        &#x1F464; <strong>'.$citizen_pseudo.'</strong><br>
                        <textarea placeholder="Donnez votre avis sur les chantiers à construire..."></textarea>
                        <input type="submit" value="Envoyer">
                    </form>
                </div>
            </div>';
    }
            
    
    /**
     * Automatic message describing an event (e.g. someone has built a construction)
     * Don't call this directly : call one of the methods prefixed by event_
     * e.g. : event_AP_invested() 
     * 
     * @param string $message   Text describing the event, as generated by 
     *                          the chosen method event_...()
     * @param string $comments  Temporary parameter to display fake comments for demo. 
     *                          Will be removed when real comments will be implemented.
     * @return string HTML
     */
    private function event($message, $comments='')
    {
        
        return '
            <div class="topic event">
                <div class="message">
                    <div class="text">
                        '.$message.'
                    </div>
                    <div class="time" title="Fuseau horaire de Paris">
                        <a href="#">Commenter</a> · Mardi 3 juin (2020) à 13h02
                    </div>
                </div>
                '.$comments.'
            </div>';
    }
    
    
    /**
     * Message when someone puts action points in a constructions
     * 
     * @param string $author_pseudo
     * @param string $construction_name
     * @param int    $AP_invested
     * @return string HTML
     */
    private function event_AP_invested($author_pseudo, $construction_name, $AP_invested)
    {
        
        $message =  '&#x1F528; '.$author_pseudo.' a investi 
                    '.$AP_invested.' <abbr title="points d\'action" style="font-variant:small-caps">pa</abbr> 
                    dans <strong>'.$construction_name.'</strong>';
        
        return $this->event($message);
    }
    
    
    /**
     * Message when a construction has been completed
     * 
     * @return string HTML
     */    
    private function event_construction_completed()
    {
        
        $message = '&#x2714;&#xFE0F; Le chantier <strong>Mur de renfort</strong> a été construit !';
        
        $comments = $this->comment_event('Nadawoo', 'Mais pourquoi vous avez construit ce chantier ? On avait dit qu\'on faisait la pompe !')
                  . $this->comment_event('Schmurtz', 'Il le fallait, on a une grosse attaque ce soir.');
        
        return $this->event($message, $comments);
    }
    
    
    /**
     * Comment of a player under an event
     * 
     * @param string $author_pseudo
     * @param string $message
     * @return string HTML
     */
    private function comment_event($author_pseudo, $message)
    {
        
        return '
            <div class="message comment">
                <div class="text">
                    <strong>'.$author_pseudo.'</strong> '.$message.'
                </div>
            </div>';
    }
}
