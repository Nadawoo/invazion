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
        
        return $this->event($this->block_title($entry_type, $attack_data),
                            $this->visual_attack($entry_type, $attack_data),
                            $attack_data['datetime_utc']);
    }
    
    
    /**
     * Displays a visual summary of the attack (nbr zombies /nbr def / nbr dead)
     * 
     * @param array $attack_data The data of the log entry, as returned by the API
     * @return string HTML
     */
    private function visual_attack($entry_type, $attack_data) {
        
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
        
        if($nbr_dead > 0) {
            $icon_dead = str_repeat('&#x1F480;', min($nbr_dead, 3));
            $text_dead = $nbr_dead.' morts de l\'attaque';
        }
        else {
            $icon_dead = '&#x2714;&#xFE0F;';
            $text_dead = 'Aucun mort de l\'attaque';
        }
        
        return '<div class="visual_attack_log">
                    <div class="block '.$class_zombies_size.'" onclick="toggle(\'logDetailsHurd'.$event_id.'\')">
                        <img src="resources/img/motiontwin/zombie9.gif"><br>
                        '.$attack_data['zombies'].' zombies
                    </div>
                    <div class="arrow">►</div>
                    <div class="block '.$class_city_size.' '.$class_city_color.'" 
                        onclick="toggle(\'logDetailsDefenses'.$event_id.'\')">
                        <img src="resources/img/free/city.png" style="height:2em"><br>
                        '.$text_defenses.'
                    </div>
                    <div class="arrow">►</div>
                    <div class="block miniblock" onclick="toggle(\'logDetailsDead'.$event_id.'\')">
                        <span style="font-size:1.5em">'.$icon_dead.'</span><br>
                        '.$text_dead.'
                    </div>
                </div>'
                . $this->survivors_frieze($attack_data['event_id'], $attack_data['citizens_survivors'], $nbr_dead)
                . $this->details_hurd($attack_data)
                . $this->details_defenses($entry_type, $attack_data)
                . $this->details_deads($attack_data)
                . $this->details_frieze($attack_data);
    }
    
    
    /**
     * Visual representation of all the citizens of the city after the attack
     * (silouhettes for the alive ones, coffins for the dead ones)
     * 
     * @param int $nbr_alive The number of alive citizens after the attack
     * @param int $nbr_dead  The number of dead citizens from the beginning 
     *                       of the city until this attack (not only the citizens
     *                       killed during this attack)
     * @return string HTML
     */
    private function survivors_frieze($event_id, $nbr_alive, $nbr_dead) {
        
        $frieze_alive = str_repeat('&#129485;&#8205;&#9794;&#65039;', $nbr_alive);
        $frieze_dead  = str_repeat('&#x26B0;&#xFE0F;', $nbr_dead);
        
        return '<div class="survivors_frieze" onclick="toggle(\'logDetailsFrieze'.$event_id.'\')">
                '.$frieze_alive.
                '<span style="font-size:0.9em">'.$frieze_dead.'</span>
            </div>';
    }
    
    
    /**
     * Message if the cyclic attack has been repulsed by the defenses
     * 
     * @param array $attack_data The data as returned by the API "events"
     * @return array
     */
    private function block_title($entry_type, $attack_data) {
        
        $titles = [
            'attack_repulsed' =>
                '<span style="padding:0 0.2em;background:green;color:white">repoussée !</span> &#x2714;&#xFE0F;',
            'attack_not_repulsed' =>
                '<span style="padding:0 0.2em;background:red;color:white">submersion !</span> &#x274C;',
            'attack_door_open' =>
                '<span style="padding:0 0.2em;background:#6c3483;color:white">sans défense !</span> &#x274C;',
            ];
        
        return '&#x1F9DF; <strong>'.$attack_data['cycle_ended'].'<sup>e</sup> attaque zombie&nbsp;'
                . $titles[$entry_type].'</strong>';
    }
    
    
    /**
     * Display details about the number of attacking zombies
     * 
     * @param array $attack_data
     * @return string HTML
     */
    private function details_hurd($attack_data) {
        
        $event_id = $attack_data['event_id'];
        
        return '<div id="logDetailsHurd'.$event_id.'" class="log_details">
                    Lors de cette '.$attack_data['cycle_ended'].'<sup>e</sup> nuit, 
                    la ville n° '.$attack_data['city_id'].' a été attaquée
                    par une horde de <strong>'.$attack_data['zombies'].' zombies</strong> !
                </div>';
    }
    
    
    /**
     * Display the details of the city defenses
     * 
     * @param string $entry_type Chose the text you want to display (see the list)
     * @param array $attack_data
     * @return string HTML
     */
    private function details_defenses($entry_type, $attack_data) {
        
        $event_id = $attack_data['event_id'];
        
        $texts = [
            'attack_repulsed' =>
                'Les <strong>'.$attack_data['defenses'].' défenses</strong> 
                de la ville ont été suffisantes pour repousser la horde zombie.
                <p style="background:none">Bien joué ! Mais une <strong>nouvelle horde</strong> 
                plus nombreuse attaquera cette nuit. 
                Vous allez devoir renforcer les défenses de la ville...</p>',
            'attack_not_repulsed' =>
                '<strong class="red">'.($attack_data['zombies']-$attack_data['defenses']).' zombies 
                ont pénétré en ville !</strong>
                Les <strong>'.$attack_data['defenses'].'</strong> défenses 
                de la ville étaient insuffisantes...                        
                <p><strong>Construisez des défenses</strong> avant la prochaine attaque
                si vous ne voulez pas tous y laisser votre peau ! <br>
                <a href="#popattack">[Comment faire ?]</a></p>',
            'attack_door_open' =>
                '<strong class="red">Les portes de la ville n\'étaient pas fermées !</strong>
                Cette négligence a permis aux <strong>'.$attack_data['zombies'].'</strong> zombies 
                de pénétrer en contournant les <strong>'.$attack_data['defenses'].'</strong> défenses.
                <p>&#x26A0;&#xFE0F; <strong>Fermez la porte de la ville</strong> avant chaque attaque,
                sinon les défenses sont inutiles !</p>',
            ];
        
        return  '<div id="logDetailsDefenses'.$event_id.'" class="log_details">'
                    . $texts[$entry_type] . '
                </div>';
    }
    
    
    /**
     * Display the list of dead citizens after the attack
     * 
     * @param array $attack_data
     * @return string HTML
     */
    private function details_deads($attack_data) {
        
        $event_id = $attack_data['event_id'];
        
        return '<div id="logDetailsDead'.$event_id.'" class="log_details">
                    <strong>Sont morts cette nuit-là :</strong> 
                    <ul>
                        <li>nom1 (dévoré en ville)</li>
                        <li>nom2 (infection)</li>
                        <li>nom3 ('.$this->death_cause('desert').')</li>
                    </ul>
                </div>';
    }
    
    
    /**
     * Display the details of the whole city survivors/dead 
     * (not only the dead of the current attack)
     * 
     * @param array $attack_data
     * @return string HTML
     */
    private function details_frieze($attack_data) {
        
        $event_id = $attack_data['event_id'];
        
        return  '<div id="logDetailsFrieze'.$event_id.'" class="log_details">
                    <strong>Appel des citoyens au matin du '.($attack_data['cycle_ended']+1).'<sup>e</sup> jour :</strong> 
                    <ul>
                        <li>&#129485;&#8205;&#9794;&#65039; <strong>nom1</strong> est vivant</li>
                        <li>&#129485;&#8205;&#9794;&#65039; <strong>nom2</strong> est vivant</li>
                        <li>&#129485;&#8205;&#9794;&#65039; <strong>nom3</strong> est vivant</li>
                        <li>&#129485;&#8205;&#9794;&#65039; <strong>nom7</strong> est vivant</li>
                        <li>&#x26B0;&#xFE0F; <strong>nom4</strong> est mort pendant la 2<sup>e</sup> nuit (dévoré en ville)</li>
                        <li>&#x26B0;&#xFE0F; <strong>nom5</strong> est mort pendant la 1<sup>e</sup> nuit (infection)</li>
                        <li>&#x26B0;&#xFE0F; <strong>nom6</strong> est mort pendant la 1<sup>e</sup> nuit ('.$this->death_cause('desert').')</li>
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
