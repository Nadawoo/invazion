
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
                <a href="index">Rejoindre</a>
            </div>
            <div class="card-reveal">
                <span class="card-title grey-text text-darken-4">Détails<i class="material-icons right">close</i></span>
                <ul>
                    <li>&#x1F551; Jour 23</li>
                    <li>&#x1F5FA;&#xFE0F; <span class="dimensions"></span> zones</li>
                    <li>&#x1F465; Joueur1, Joueur2, Joueur3, Joueur4</li>
                </ul>
            </div>
        </div>
    </div>
</template>


<section id="games">
    <h2 style="font-size:2em;text-align:center;color:black;font-weight:bold">Rejoindre une partie</h2>
    <div id="games_list" class="row"></div>
</section>
