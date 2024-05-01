<?php
/**
 * Expeditions on the map
 */
class HtmlPaths {
    
    
    /**
     * The vertical panel which displays all the details of the expeditions
     * 
     * @return string HTML
     */
    function path_template() {
        
        $buttons = new HtmlButtons();
        
        return '
        <template id="tplPath">
            <div class="card">
                <h2 class="card-title"></h2>
                <div class="card-tabs">
                    <ul class="tabs tabs-fixed-width">
                        <li class="tab"><a href="#path1_path" class="active">Trajet</a></li>
                        <li class="tab"><a href="#path1_members">Membres (<span class="nbr_members"></span>)</a></li>
                    </ul>
                </div>
                <div class="card-content">
                    <ul class="course">
                        <li class="first_stage">
                            &nbsp;&#x1F6A9; Départ zone [<span class="coords"></span>]
                            <a class="localize"><i class="material-icons">my_location</i></a>
                        </li>
                        <li class="grey-text">&nbsp;&nbsp;|</li>
                        <li class="current_stage" style="display:flex;align-items:center">
                            &nbsp;<span class="humans"></span>
                            &nbsp;<span class="nbr_kilometers"></span> km à parcourir
                            <a class="localize"><i class="material-icons">my_location</i></a>
                        </li>
                        <li class="grey-text">&nbsp;&nbsp;|</li>
                        <li class="last_stage">
                            &#x1F3C1;&nbsp;&nbsp;Arrivée zone [<span class="coords"></span>]
                            <a class="localize"><i class="material-icons">my_location</i></a>
                        </li>
                    </ul>
                    <ul class="members expanded"></ul>
                </div>
                <div class="card-action">
                    '.$buttons->dig_path(null).'
                    '.$buttons->move_path(null).'
                </div>
            </div>
        </template>';
    }
    
    
    /**
     * Horizontal bar which displays small buttons to act with the expeditions
     * 
     * @return string HTML
     */
    function paths_bar() {
        
        // TODO: temporary for the tests
        $path_id = 1;
        
        $buttons = new HtmlButtons();
        
        return '
        <div id="paths_bar">
            <div class="path active">
                <h2 onclick="resetMapView();toggleMapPathsView()">
                    Expédition '.$path_id.' <a>&#x2699;&#xFE0F;</a>
                </h2>
                <div class="body">                    
                    '.$buttons->dig_path($path_id).'
                    '.$buttons->move_path($path_id).'
                </div>
            </div>
            <div class="path">
                <h2>Expéd. '.$path_id.'</h2>
                <div class="body">
                    12 km<br>
                    2 membres
                </div>
            </div>
            <div class="path">
                <h2>Expéd. 2</h2>
                <div class="body">
                    12 km<br>
                    2 membres
                </div>
            </div>
            <div class="path">
                <div class="body">
                    <a style="display:block;padding-top:0.5em" title="Créer une nouvelle expédition pour explorer la carte (FONCTION A PROGRAMMER)">
                        <span style="font-size:3em;line-height:50%">+</span>
                        <span style="display:block;line-height:100%">Créer une<br>expédition</span>
                    </a>
                </div>
            </div>
        </div>';
    }
}
