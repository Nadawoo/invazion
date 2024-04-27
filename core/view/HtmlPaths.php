<?php
/**
 * Expeditions on the map
 */
class HtmlPaths {
    
    
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
                        <li style="display:flex">
                            &nbsp;<span class="humans"></span>
                            &nbsp;<span class="nbr_kilometers"></span> km à parcourir</li>
                        <li class="grey-text">&nbsp;&nbsp;|</li>
                        <li class="last_stage">
                            &#x1F3C1;&nbsp;&nbsp;Arrivée zone [<span class="coords"></span>]
                            <a class="localize"><i class="material-icons">my_location</i></a>
                        </li>
                    </ul>
                    <ul class="members expanded"></ul>
                </div>
                <div class="card-action">
                    '.$buttons->move_path(null).'
                </div>
            </div>
        </template>';
    }    
}
