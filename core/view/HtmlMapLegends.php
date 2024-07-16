<?php
/**
 * HTML for the legends explaining the colors of the satellite views
 */
class HtmlMapLegends {
    
    public function all_legends() {
        
        return  $this->legend_zombies()
                .$this->legend_items();
    }
    
    
    private function legend_zombies() {
        
        return '
            <fieldset id="map_legend_zombies" class="map_legend">
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
            <fieldset id="map_legend_items" class="map_legend">
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
}
