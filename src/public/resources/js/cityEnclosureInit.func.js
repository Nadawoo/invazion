/**
 * Affiche le panneau de la ville correspondant au sous-menu de ville actif
 * (chantiers, maison, porte de la ville...)
 * 
 * @param {string} cityContentsId L'id HTML des éléments à afficher
 * @returns {undefined}
 */
function switchCitySubmenu(cityContentsId) {
    // On masque tous les blocs de la ville sans exception...
    hideCityBlocks();
    // ... puis on affiche celui qu'on veut voir
    display(`#${cityContentsId}`);
    // Modifie l'url pour mémoriser dans quel onglet de la ville on se trouve.    
    updateUrlParam('tab', cityContentsId);
    
    hide(["#city_submenus", "#city_defenses"]);
}


/**
 * Hides all the blocs inside the city (e.g. list of constructions)
 */
function hideCityBlocks() {
    
    var cityContents = document.getElementById("city_contents").children;
    for(i=0; i<cityContents.length; i++) {
        hide(`#${cityContents[i].id}`);
    }
}
