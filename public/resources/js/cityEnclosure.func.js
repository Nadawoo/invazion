/**
 * This script gathers all the functions related to the city enclosure (once 
 * the citizen is inside it).
 * Put only functions here, no immediatly executable code or event listeners.
 */



/**
 * Hides all the blocs inside the city (e.g. list of constructions)
 */
function hideCityBlocks() {
    
    var cityContents = document.getElementById("city_contents").children;
    for(i=0; i<cityContents.length; i++) {
        hideIds(cityContents[i].id);
    }
}


/**
 * Affiche le sous-menu de la ville correspondant à l'onglet de ville actif
 * (Chez moi, Ville, Habitant, Porte)
 * 
 * @param {string} cityMenuId L'id HTML du menu à afficher
 * @returns {undefined}
 */
function switchCityMenu(cityMenuId) {
    
    var tabsList = ['cityMenuMyHome', 'cityMenuCity'];
    
    // On masque tous les sous-menus de ville sans exception...
    hideIds(tabsList);        
    // ... puis on affiche celui qu'on veut voir
    unhideId(cityMenuId);
    // On masque tous les blocs de la ville sans exception...
    hideCityBlocks();
}


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
    unhideId(cityContentsId);
    // Modifie l'url pour mémoriser dans quel onglet de la ville on se trouve.    
    updateUrlParam('tab', cityContentsId);
}


/*
 * Passe de la liste des citoyens à l'intérieur d'une maison et vice-versa 
 *  
 * @param {string} idName   L'ID HTML de la maison du citoyen à afficher/masquer
 * @returns {undefined}
 */
function toggleHouse(idName) {
    
    // Masque la liste de citoyens si elle est affichée et affiche la maison du citoyen
    // OU : affiche la liste de citoyens si elle est masquée et masque la maison du citoyen
    toggle('#citizens_list');
    toggle('#'+idName);
}


/**
 * Open or close the city door
 * 
 * @param {boolean} value Value of the switch button: true to open the door,
 *                        false to close the door
 * @returns {undefined}
 */
async function changeCityDoor(value) {
    
    let action = (value === true) ? "open_door" : "close_door";    
    let token = getCookie('token');
    // Execute the action of opening or closing
    let json = await callApi("GET", "city", `action=${action}&token=${token}`);
    // Update the GUI after the openng or closing
    if(json.metas.error_code === "success") {
        let oldDoorStatus = (value === true) ? "door_closed" : "door_open";
        let newDoorStatus = (value === true) ? "door_open" : "door_closed";
        let doorBlock = document.querySelector("#city_door .city_block");
        doorBlock.classList.remove(oldDoorStatus);
        doorBlock.classList.add(newDoorStatus);
    }
}
