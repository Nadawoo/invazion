<?php
/**
 * HTML for the legends explaining the colors of the satellite views
 */
class HtmlMapLegends {
    
    public function all_legends() {
        
        return  $this->legend_zombies()
                .$this->legend_items()
                .$this->legend_cityframes();
    }
    
    
    private function legend_zombies() {
        
        return '
            <fieldset id="map_legend_zombies" class="map_legend" style="display:none">
                <legend>Légende</legend>
                <a href="#popcontrol" style="color:inherit">
                    <ul>
                        <li><span style="background:grey"></span> Zone sûre (aucun zombie)</li>
                        <li><span style="background:green"></span> Zone sûre (peu de zombies)</li>
                        <li><span style="background:orange"></span> 2 humains requis ou +</li>
                        <li><span style="background:red"></span> 3 humains requis ou +</li>
                        <li><span style="background:darkred"></span> 4 humains requis ou +</li>
                    </ul>
                </a>
            </fieldset>';
    }
    
    
    private function legend_items() {
        
        return '
            <fieldset id="map_legend_items" class="map_legend" style="display:none">
                <legend>Légende</legend>
                <a href="#Outside" style="color:inherit">
                    <ul>
                        <li>&#x1F97E; Zone visitée aujourd\'hui</li>
                        <li>&#x26CF;&#xFE0F; Zone fouillable</li>
                        <li><span style="background:black;border-radius:0"></span>&nbsp;Zone à découvrir</li>
                    </ul>
                    <hr>
                    <strong>Objets au sol :</strong>
                    <ul>
                        <li><span style="background:grey"></span> Aucun objet</li>
                        <li><span style="background:green"></span> 1-5 objets (1 sac)</li>
                        <li><span style="background:orange"></span> 6-10 objets (2 sacs)</li>
                        <li><span style="background:red"></span> 11-15 objets (3 sacs)</li>
                        <li><span style="background:darkred"></span> 16 objets ou +</li>
                    </ul>
                </a>
            </fieldset>';
    }
    
    
    private function legend_cityframes() {
        
        return '
            <fieldset id="map_legend_cityframes" class="map_legend hidden">
                <legend>Légende</legend>
                <ul class="undiscovered">
                    <li><span style="border-color:black">&#x1F50D;</span> Bâtiment non identifié</li>
                </ul>
                <ul class="defenses">
                    <li><span style="border-color:#F4D03F">&#x1F3E2;</span> Ville</li>
                    <li><span style="border-color:green">&#x1F6E1;&#xFE0F;</span> Bâtiment de défense</li>
                    <li><span style="border-color:red">&#x1F9DF;</span> Foyer de zombies</li>
                </ul>
                <ul class="explorables">
                    <li><span style="border-color:cornflowerblue">&#x1FAB5;</span> Bâtiment à ressources</li>
                    <li><span style="border-color:darkorange">&#x26A1;</span> Bâtiment à énergie</li>
                    <li><span style="border-color:purple">&#x2699;&#xFE0F;</span> Bâtiment technique</li>
                    <li>&#x2714;&#xFE0F; &nbsp;Bâtiment exploré</li>
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
