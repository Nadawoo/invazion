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
        
        return '
        <template id="tplPath">
            <div class="card">
                <h2 class="card-title"></h2>
                <div class="card-tabs">
                    <ul class="tabs tabs-fixed-width z-depth-1">
                        <li class="tab"><a href="#path1_path">Trajet</a></li>
                        <li class="tab"><a href="#path1_members" class="active">Membres (<span class="nbr_members"></span>)</a></li>
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
                    
                    <div class="members">
                        <ul class="current_members expanded"></ul>

                        <form name="available_members" method="GET" action="#">
                            <!--
                            <p class="hidden alert_no_member">
                                <em class="greytext">Les membres de l\'expédition ne sont pas encore définis.</em>
                                <button class="bluebutton" onclick="">Ajouter des membres &gt;</button>
                            </p>
                            -->
                            <input type="hidden" name="path_id" value="">
                            <div class="hidden choose_members z-depth-1">
                                <p class="header">Sélectionnez les citoyens 
                                    qui participeront à l\'expédition</p>
                                <ul class="body"></ul>
                                <button type="submit" class="redbutton"><i class="material-icons">fact_check</i>&nbsp;Enregistrer</button>
                            </div>
                        </form>
                    </div>
                    
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
        <div id="paths_bar" class="bottom_bar animate__animated animate__slideInUp hidden">

            <div class="paths"></div>
            
            <div class="path">
                <div class="body">
                    <a href="#poppath" class="new" 
                       title="Créer une nouvelle expédition pour explorer la carte">
                        <span class="plus">+</span>
                        <span class="label">Créer une<br>expédition</span>
                    </a>
                </div>
            </div>
            
            <a class="close z-depth-2" onclick="togglePathsBar()">
               <i class="material-icons">close</i>
            </a>
        </div>';
    }
    
    
    function cityframes_bar() {
        
        return '
        <div id="cityframes_bar" class="bottom_bar animate__animated animate__slideInDown hidden">
            <div class="path defenses" onclick="switchCityframesType(\'defenses\')">
                <span class="icon">&#x1F6E1;&#xFE0F;</span>
                <span class="label">Défenses</span>
            </div>
            <div class="path resources"
                 onclick="switchCityframesType(\'resources\')">
                <span class="icon">&#x26CF;&#xFE0F;</span>
                <span class="label">Ressources</span>
            </div>
            <div class="path transportations" onclick="switchCityframesType(\'transportations\')">
                <span class="icon">&#x1F681;</span>
                <span class="label">Transports</span>
            </div>
            <div class="path weather" onclick="switchCityframesType(\'weather\')">
                <span class="icon">&#x1F327;&#xFE0F;</span>
                <span class="label">Météo</span>
            </div>
            <div class="path undiscovered" onclick="switchCityframesType(\'undiscovered\')">
                <span class="icon">&#x2753;</span>
                <span class="label">Inexploré</span>
            </div>
            <a class="close z-depth-2"><i class="material-icons">close</i></a>
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
                <h2>Expéd. <span class="path_name"></span></h2>
                <div class="body">
                    <span class="nbr_kilometers"></span> km<br>
                    <div class="nbr_members"></div>
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
                <h2>
                    Expédition <span class="path_name"></span>
                </h2>
                <div class="body">                    
                    '.$buttons->move_path(null).'
                    '.$buttons->dig_path(null).'
                    '.$buttons->expert_path(null).'
                    '.$buttons->populate_path(null).'
                </div>
            </div>
        </template>';
    }
}
