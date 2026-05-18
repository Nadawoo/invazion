
<template id="tplGame">
    <div class="col s12 m6">
        <div class="card blue-grey darken-1">
            <div class="card-content white-text">
                <span class="card-title">
                    #<span class="map_id"></span>. <span class="map_name"></span>
                    <a class="activator" style="color:inherit"><i class="material-icons right">more_vert</i></a>
                </span>
                <p class="description"></p>
            </div>
            <div class="card-action" style="text-align:center">
                <form method="get" action="" name="join_game">
                    <input type="hidden" name="api_name" value="games">
                    <input type="hidden" name="action" value="join">
                    <input type="hidden" name="params[map_id]" value="">
                    <button type="submit">Rejoindre</button>
                </form>                
            </div>
            <div class="card-reveal">
                <span class="card-title grey-text text-darken-4"><i class="material-icons right">close</i></span>
                <ul style="margin-bottom:0">
                    <li>&#x1F551; Jour 23</li>
                    <li>&#x1F5FA;&#xFE0F; <span class="dimensions"></span> zones</li>
                    <li style="display:flex;gap:4px">&#x1F465; <ul class="citizens" style="display:flex;gap:4px;flex-wrap:wrap;">(En attente de joueurs)</ul></li>
                </ul>
            </div>
        </div>
    </div>
</template>


<section id="games">
    <h2>Mes parties en cours</h2>
    <div id="my_games_list" class="row"></div>
    
    <h2>Rejoindre une nouvelle partie</h2>
    <div id="games_list" class="row">
        <div class="col s12 m6">
            <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                    <span class="card-title">
                        <i class="material-icons create_button">add</i>
                    </span>
                    <p class="description"></p>
                </div>
                <div class="card-action" style="text-align:center">
                    <button data-action="createGame">Générer une nouvelle carte</button>               
                </div>
                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4"><i class="material-icons right">close</i></span>
                    <ul style="margin-bottom:0">
                        <li>&#x1F551; Jour 23</li>
                        <li>&#x1F5FA;&#xFE0F; <span class="dimensions"></span> zones</li>
                        <li style="display:flex;gap:4px">&#x1F465; <ul class="citizens" style="display:flex;gap:4px;flex-wrap:wrap;">(En attente de joueurs)</ul></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="games_list" class="row"></div>
</section>
