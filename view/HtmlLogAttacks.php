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
                            $html_elements['message'].$this->other_deaths(),
                            $attack_data['datetime_utc']);
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
                        Cette négligence a permis aux <strong>'.($attack_data['zombies']-$attack_data['defenses']).'</strong> zombies 
                        de pénétrer en contournant les <strong>'.$attack_data['defenses'].'</strong> défenses.
                        <br>Bilan :
                            <ul>
                                <li>&#x26B0;&#xFE0F; <strong>'.$attack_data['citizens_killed'].' morts</strong>
                                    (nom1, nom2)</li>
                                <li>&#x1F9CD;&nbsp; <strong>'.$attack_data['citizens_survivors'].' survivants</strong>
                                    (nom3, nom4, nom5)</li>
                            </ul>
                        <strong>Fermez la porte de la ville</strong> avant chaque attaque,
                        sinon les défenses sont inutiles !'
            ];
    }
    
    
    /**
     * Additional message for citizens dead from other causes than the attack
     * (infection...)
     * 
     * @return string HTML
     */
    public function other_deaths() {
        
        return "<p>Par ailleurs, plusieurs citoyens se sont laissés 
                <strong>dévorer dans le désert</strong> cette nuit.
                Héroïsme ? Distraction fatale ? Suicide ? Nous ne le saurons jamais...
                Paix à leurs moignons :<br>
                nom1, nom2</p>";
    }
}
