<?php
/**
 * Displays the wall gathering the discussions and the log of events happening 
 * in the city 
 */
class HtmlWall
{    
    
    /**
     * Main method. Call this to display the wall.
     * 
     * @param int    $citizen_id     The ID of the currently connected player
     * @param string $citizen_pseudo The pseudo of the currently connected player
     * @return type
     */
    public function wall($citizen_id, $citizen_pseudo)
    {
        
        return '<div id="wall" class="city_block">
                <h2 id="enlarge_wall">
                    <div class="arrow">&#8963;</div>
                    Communications
                </h2>
                <div class="contents">
                    
                    '.$this->tabs().'
                    
                    <div id="citizenId" class="hidden">'.$citizen_id.'</div>
                    <div id="citizenPseudo" class="hidden">'.$citizen_pseudo.'</div>
                    <div id="discussions"></div>
                    <div id="notifications"></div>
                    <div id="attacks"></div>
                    <div id="events"></div>
                    
                    '.$this->event_construction_completed(date('c')).'                        
                    '.$this->event_AP_invested(date('c'), 'Nadawoo', 'Mur de renfort', 3).'
                    
                </div>
            </div>';
    }
    
    
    private function tabs()
    {
        
        return '<nav id="discussionTabs" style="margin-bottom:0.9em">
                    <a id="tabWallDiscuss" class="active_tab">Discussions</a> 
                    <!--<a id="tabWallNotifications">Notifications</a>-->
                    <a id="tabWallEvents">Événements</a> 
                    <a id="tabWallAttacks">Attaques</a> 
                </nav>';
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
    protected function event($title, $message, $iso_date, $comments='')
    {
        
        $date = strftime("%a %d %B %Y à %H:%M", strtotime($iso_date));
        
        return '
            <div class="topic event">
                <h3>'.$title.'</h3>
                <div class="message">
                    <div class="text">
                        '.$message.'
                    </div>
                    <div class="time" title="Fuseau horaire de Paris">
                        <a href="#">Commenter</a> · '.$date.'
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
    private function event_AP_invested($iso_date, $author_pseudo, $construction_name, $AP_invested)
    {
        
        $message =  '&#x1F528; '.$author_pseudo.' a investi 
                    '.$AP_invested.' <abbr title="points d\'action" style="font-variant:small-caps">pa</abbr> 
                    dans <strong>'.$construction_name.'</strong>';
        
        return $this->event($message, '', $iso_date);
    }
    
    
    /**
     * Message when a construction has been completed
     * 
     * @return string HTML
     */    
    private function event_construction_completed($iso_date)
    {
        
        $message = '&#x2714;&#xFE0F; Le chantier <strong>Mur de renfort</strong> a été construit !';
        
        $comments = $this->comment_event('Nadawoo', 'Mais pourquoi vous avez construit ce chantier ? On avait dit qu\'on faisait la pompe !')
                  . $this->comment_event('Schmurtz', 'Il le fallait, on a une grosse attaque ce soir.');
        
        return $this->event($message, '', $iso_date, $comments);
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
