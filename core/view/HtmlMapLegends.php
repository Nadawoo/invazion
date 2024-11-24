<?php
/**
 * HTML for the legends explaining the colors of the satellite views
 */
class HtmlMapLegends {
    
    public function all_legends() {
        
        return  $this->legend_zombies()
                .$this->legend_items()
                .$this->legend_explorations()
                .$this->legend_cityframes();
    }
    
    
    private function legend_zombies() {
        
        return '
            <fieldset id="map_legend_zombies" class="hidden map_legend animate__animated animate__slideInUp">
                <legend>Légende</legend>
                <a href="#popcontrol" style="color:inherit">
                    <ul>
                        <li><span class="legend_color" style="background:green"></span> Zone sûre (peu de zombies)</li>
                        <li><span class="legend_color" style="background:orange"></span> 2 humains requis ou +</li>
                        <li><span class="legend_color" style="background:red"></span> 3 humains requis ou +</li>
                        <li><span class="legend_color" style="background:darkred"></span> 4 humains requis ou +</li>
                    </ul>
                </a>
            </fieldset>';
    }
    
    
    private function legend_items() {
        
        return '
            <fieldset id="map_legend_items" class="hidden map_legend animate__animated animate__slideInUp">
                <legend>Légende</legend>
                <a href="#Outside" style="color:inherit">
                    <ul>
                        <!-- <li><span style="background:grey"></span> Aucun objet au sol</li> -->
                        <li><span class="legend_color" style="background:green"></span> 1-5 objets (1 sac)</li>
                        <li><span class="legend_color" style="background:orange"></span> 6-10 objets (2 sacs)</li>
                        <li><span class="legend_color" style="background:red"></span> 11-15 objets (3 sacs)</li>
                        <li><span class="legend_color" style="background:darkred"></span> 16 objets ou +</li>
                        <hr>
                        <strong>Localiser des objets au sol :</strong>
                        <li class="switch"
                            title="Localiser sur la carte les objets donnant des points d\'action">
                            <label>
                                <input type="checkbox">
                                <span class="lever" onclick="toggleMapItemMarker(\'boost\')"></span>
                                &#x26A1;Regain d\'énergie
                            </label>
                        </li>
                        <li class="switch"
                            title="Localiser sur la carte les objets utiles pour les constructions">
                            <label>
                                <input type="checkbox">
                                <span class="lever" onclick="toggleMapItemMarker(\'resource\')"></span>
                                &#x1FAB5;Ressources
                            </label>
                        </li>
                    </ul>
                </a>
            </fieldset>';
    }
    
    
    private function legend_explorations() {
        
        return '
            <fieldset id="map_legend_explorations" class="hidden map_legend animate__animated animate__slideInUp">
                <legend>Légende</legend>
                <a href="#Outside" style="color:inherit">
                    <ul>
                        <li><span class="legend_color" style="background:darkred">&#x1F97E;</span> Zone visitée aujourd\'hui</li>
                        <li><span class="legend_color" style="background:green">&#x26CF;&#xFE0F;</span> Zone fouillable</li>
                        <li><span class="legend_color" style="background:grey">...</span> Zone épuisée</li>
                        <li><span class="legend_color" style="background:black"></span>Zone à découvrir</li>
                    </ul>
                </a>
            </fieldset>';
    }
    
    
    private function legend_cityframes() {
        
        return '
            <fieldset id="map_legend_cityframes" class="hidden map_legend animate__animated animate__slideInUp">
                <legend>Légende</legend>
                <ul class="undiscovered">
                    <li><span style="border-color:black">&#x1F50D;</span> Bâtiment non identifié</li>
                </ul>
                <ul class="defenses">
                    <li><span style="border-color:#F4D03F">&#x1F3E2;</span> Ville</li>
                    <li><span style="border-color:green">&#x1F6E1;&#xFE0F;</span> Bâtiment de défense</li>
                    <li><span style="border-color:red">&#x1F9DF;</span> Foyer de zombies</li>
                </ul>
                <ul class="resources">
                    <li><span style="border-color:cornflowerblue">&#x1FAB5;</span> Bâtiment à ressources</li>
                    <li><span style="border-color:darkorange">&#x26A1;</span> Bâtiment à énergie</li>
                    <li><span style="border-color:purple;transform:rotate(45deg) scale(0.85)">&#x2699;&#xFE0F;</span> Bâtiment technique</li>
                    <li>&nbsp;&#x2705; &nbsp;Bâtiment exploré</li>
                </ul>
                <ul class="transportations">
                    <li><span style="border-color:black">&#x1F681;</span> Héliport</li>
                </ul>
                <ul class="weather">
                    <li>&#x1F32B;&#xFE0F; &nbsp;Brouillard</li>
                    <li>&#x1F327;&#xFE0F; &nbsp;Pluie</li>
                    <li>&#x1F329;&#xFE0F; &nbsp;Orage</li>
                    <li>&#x1F32A;&#xFE0F; &nbsp;Tornade</li>
                    <li>&#x2668;&#xFE0F; &nbsp;Sécheresse</li>
                    <li>&#x1F525; &nbsp;Incendie</li>
                </ul>
            </fieldset>';
    }
}
