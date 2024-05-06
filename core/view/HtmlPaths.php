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
            <a class="close" onclick="hideIds(\'paths_panel\');unhideId(\'paths_bar\');unhideId(\'attack_bar\');">
                <i class="material-icons">close</i>
            </a>
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
        
        return '
        <div id="paths_bar">

            <div class="paths"></div>
            
            <div class="path">
                <div class="body">
                    <a class="new" title="Créer une nouvelle expédition pour explorer la carte (FONCTION A PROGRAMMER)">
                        <span class="plus">+</span>
                        <span class="label">Créer une<br>expédition</span>
                    </a>
                </div>
            </div>
        </div>';
    }
    
    
    /**
     * HTML template for an inactive card in the paths bar
     * (default view for a path card)
     * 
     * @return string HTML
     */
    function pathsbar_inactive_path_template() {
        
        return '
        <template id="tplPathsBarInactivePath">
            <div class="path inactive" onclick="activatePathsBarPath(event)">
                <h2>Expéd. <span class="path_id"></span></h2>
                <div class="body">
                    <span class="nbr_kilometers"></span> km<br>
                    <span class="nbr_members"></span> membres
                </div>
            </div>
        </template>';
    }
    
    
    /**
     * HTML template for the active card in the paths bar
     * (= the exepedtion that is currently manipulated by the player)
     * 
     * @return string HTML
     */
    function pathsbar_active_path_template() {
        
        $buttons = new HtmlButtons();
        
        return '
        <template id="tplPathsBarActivePath">
            <div class="path active hidden">
                <h2 onclick="hideIds(\'paths_bar\');hideIds(\'attack_bar\');unhideId(\'paths_panel\')">
                    Expédition <span class="path_id"></span> <a>&#x2699;&#xFE0F;</a>
                </h2>
                <div class="body">                    
                    '.$buttons->dig_path(null).'
                    '.$buttons->move_path(null).'
                </div>
            </div>
        </template>';
    }
}
