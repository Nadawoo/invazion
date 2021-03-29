<?php
/**
 * HTML elements to display the log of zombies attacks
 */
class HtmlLogAttacks extends HtmlWall
{
    
    
    /**
     * Call this method to get the complete HTML of a log attack entry.
     * 
     * @param string $entry_type The name of a private method of this class
     *                         (e.g. 'attack_repulsed')
     * @param array $attack_data The data of the log entry, as returned by the API
     * @return string HTML
     */
    public function get_log_entry($entry_type, $attack_data) {
        
        $html_elements = $this->$entry_type($attack_data);
        
        return $this->event($html_elements['title'],
                            $this->visual_attack($attack_data) . $html_elements['message'],
                            $attack_data['datetime_utc']);
    }
    
    
    /**
     * Displays a visual summary of the attack (nbr zombies /nbr def / nbr dead)
     * 
     * @param array $attack_data The data of the log entry, as returned by the API
     * @return string HTML
     */
    private function visual_attack($attack_data) {
        
        $nbr_dead = count($attack_data['citizens_killed']);
        $event_id = $attack_data['event_id'];
        
        if($attack_data['zombies'] > $attack_data['defenses'] or $attack_data['is_door_closed'] === 0) {
            $class_zombies_size = '';
            $class_city_size    = 'miniblock';
        } else {
            $class_zombies_size = 'miniblock';
            $class_city_size    = '';
        }
        
        $text_defenses = ($attack_data['is_door_closed'] === 0)
                         ? '&#x274C; <span style="color:lightred;font-weight:bold">Porte ouverte !</span>'
                         : $attack_data['defenses'].' défenses';
        
        $class_city_color = ($attack_data['is_door_closed'] === 0 or $attack_data['zombies'] > $attack_data['defenses'])
                            ? 'bad'
                            : 'good';
        
        return '<div class="visual_attack_log">
                    <div class="block '.$class_zombies_size.'">
                        <img src="resources/img/motiontwin/zombie9.gif"><br>
                        '.$attack_data['zombies'].' zombies
                    </div>
                    <div class="arrow">►</div>
                    <div class="block '.$class_city_size.' '.$class_city_color.'">
                        <img src="resources/img/free/city.png" style="height:2em"><br>
                        '.$text_defenses.'
                    </div>
                    <div class="arrow">►</div>
                    <div class="block miniblock" onclick="toggle(\'logAttackDead'.$event_id.'\')">
                        <span style="font-size:1.5em">&#x1F480;</span><br>
                        '.$nbr_dead.' morts
                    </div>
                </div>'                    
                . $this->details_daily_deads($attack_data);
    }
    
    
    /**
     * Message if the cyclic attack has been repulsed by the defenses
     * 
     * @param array $attack_data The data as returned by the API "events"
     * @return array
     */
    private function attack_repulsed($attack_data) {
        
        return [
            'title'   => '&#x1F9DF; <strong>'.$attack_data['cycle_ended'].'<sup>e</sup> attaque zombie '
                        . '<span style="padding:0 0.2em;background:green;color:white">repoussée !</span> &#x2714;&#xFE0F;</strong>',
            'message' => 'La ville '.$attack_data['city_id'].' a été attaquée par une horde '
                        . 'de <strong>'.$attack_data['zombies'].' zombies</strong> ! '
                        . 'Heureusement, nos <strong>'.$attack_data['defenses'].' défenses</strong> '
                        . 'ont été suffisantes pour les repousser. '
                        . '<br>'
                        . 'Bien joué ! Mais une <strong>nouvelle horde</strong> '
                        . 'plus nombreuse attaquera cette nuit. '
                        . 'Vous allez devoir renforcer les défenses de la ville...'
            ];
    }
    
    
    /**
     * Message if the cyclic attack has NOT been repulsed by the defenses
     * 
     * @param array $attack_data The data as returned by the API "events"
     * @return array
     */
    private function attack_not_repulsed($attack_data) {

        return [
            'title'   => '&#x1F9DF; <strong>'.$attack_data['cycle_ended'].'<sup>e</sup> attaque zombie '
                       . '<span style="padding:0 0.2em;background:red;color:white">submersion !</span> &#x274C;</strong>',
            'message' => '<strong class="red">'.($attack_data['zombies']-$attack_data['defenses']).' zombies 
                        ont pénétré en ville !</strong>
                        Les <strong>'.$attack_data['defenses'].'</strong> défenses 
                        de la ville '.$attack_data['city_id'].' étaient insuffisantes 
                        pour contenir les <strong>'.$attack_data['zombies'].'</strong> morts-vivants...
                        <br>Bilan :
                            <ul>
                                <li>&#x26B0;&#xFE0F; <strong>'.$attack_data['citizens_killed'].' morts</strong> 
                                    (nom1, nom2)</li>
                                <li>&#x1F9CD;&nbsp; <strong>'.$attack_data['citizens_survivors'].' survivants</strong> 
                                    (nom3, nom4, nom5)</li>
                            </ul>
                        <strong>Construisez des défenses</strong> avant la prochaine attaque
                        si vous ne voulez pas tous y laisser votre peau !'
                        ];
    }
    
    
    /**
     * Message if the city doors were not closed during the cyclic attack
     * 
     * @param array $attack_data The data as returned by the API "events"
     * @return array
     */
    private function attack_door_open($attack_data) {
                
        return [
            'title'   => '&#x1F9DF; <strong>'.$attack_data['cycle_ended'].'<sup>e</sup> attaque zombie '
                       . '<span style="padding:0 0.2em;background:#6c3483;color:white">catastrophe !</span> &#x274C;</strong>',
            'message' => '<strong class="red">Les portes de la ville n\'étaient pas fermées !</strong>
                        Cette négligence a permis aux <strong>'.$attack_data['zombies'].'</strong> zombies 
                        de pénétrer en contournant les <strong>'.$attack_data['defenses'].'</strong> défenses.
                        <strong>Fermez la porte de la ville</strong> avant chaque attaque,
                        sinon les défenses sont inutiles !'
            ];
    }
    
    
    /**
     * Display the list of dead citizens after the attack
     * 
     * @param array $attack_data
     * @return string HTML
     */
    private function details_daily_deads($attack_data) {
        
        $event_id = $attack_data['event_id'];
        
        return '<div id="logAttackDead'.$event_id.'" style="display:none;background:lightgrey">
                    <strong>Bilan du matin :</strong> 
                    <ul>
                        <li>&#x26B0;&#xFE0F; <strong>'.$attack_data['citizens_killed'].' morts</strong>
                            <ul>
                                <li>nom1 (dévoré en ville)</li>
                                <li>nom2 (infection)</li>
                                <li>nom3 ('.$this->death_cause('desert').')</li>
                            </ul>
                        </li>
                        <li>&#x1F9CD;&nbsp; <strong>'.$attack_data['citizens_survivors'].' survivants</strong>
                            <ul>
                                <li>nom4</li>
                                <li>nom5</li>
                                <li>nom6</li>
                            </ul>
                        </li>
                    </ul>
                </div>';
    }
    
    
    /**
     * Displays the cause of the death for this citizen (attack, infection...)
     * 
     * @param string $cause The alias of the death cause (see the list) 
     * @return string HTML
     */
    public function death_cause($cause) {
        
        $causes = [
            'desert' => [
                'name'      => "disparu dans le désert",
                'tooltip'   => "Ce citoyen s'est laissé dévorer dans l'outre-monde\n"
                            . "cette nuit. Héroïsme ? Distraction fatale ? Suicide ?\n"
                            . "Nous ne le saurons jamais. Paix à ses moignons..."
                ],
            ];
        
        return '<abbr title="'.$causes[$cause]['tooltip'].'">'.$causes[$cause]['name'].'</abbr>';
    }
}
