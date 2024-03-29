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
     * @return string HTML
     */
    public function wall()
    {
        
        return $this->message_template().
            $this->events_templates().
            '<div id="wall" class="city_block">
                <h2 id="wallHeader">
                    <div class="arrow">&#8963;</div>
                    Communications
                </h2>
                <div class="contents">
                    
                    <div class="header">'.$this->tabs().'</div>
                    <div class="body">
                        <div id="wallDiscuss"></div>
                        <div id="wallNotifications"></div>
                        <div id="wallAttacks"></div>
                        <div id="wallEvents"></div>
                    </div>
                    <div class="footer"></div>'
//                    '.$this->event_construction_completed(date('c')).'                        
//                    '.$this->event_AP_invested(date('c'), 'Nadawoo', 'Mur de renfort', 3).'
                    
                .'</div>
            </div>';
    }
    
    
    private function tabs()
    {
        
        return '<ul class="tabs z-depth-3">
                    <li class="tab col s3"><a href="#wallDiscuss">Discussions</a></li>
                    <!--<li class="tab col s3"><a href="#wallNotifications">Notifications</a></li>-->
                    <li class="tab col s3"><a href="#wallEvents">Événements</a></li>
                    <li class="tab col s3"><a href="#wallAttacks">Attaques</a></li>
                </ul>';
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
     * The HTML template for a messsage in a wall thread 
     * (first message of a discussion, or reply, or event message)
     * 
     * @return string
     */
    private function message_template() {
        
        return '
            <template id="tplMessage">
                <div class="message">
                    <div class="reply_num"></div>
                    <div class="pseudo">&#x1F464; <strong></strong></div>
                    <div class="time" title="Fuseau horaire de Paris"></div>
                    <div class="text"></div>
                </div>
            </template>';
    }
    
    
    /**
     * All the HTML templates for the events in game (ex: a citizen attacked an other one)
     * 
     * @return string HTML
     */
    private function events_templates() {
        
        return '
            <div id="tplEvents">
                <template class="attack_citizen">
                    &#x1F44A;&#x1F3FC; <strong class="author_pseudo"></strong> a agressé
                    <strong  class="target_pseudo"></strong>
                    en zone <span class="coords"></span> !
                </template>
                
                <template class="heal_citizen">
                    &#x1F489; <strong class="author_pseudo"></strong> a soigné la blessure
                    de <strong  class="target_pseudo"></strong>
                    en zone <span class="coords"></span>.
                </template>
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
