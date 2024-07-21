/**
 * Miscellaneous JS functions that doesn't fit the others JS scripts.
 * Don't put events listeners here (see events.js) nor actions executed 
 * on loading the page (see onPageLoad.js)
 */


/**
 * Show/hide the given element by clicking on a button
 * 
 * @param {string|array} elementsNames The list of HTML IDs or classes to show/hide.
 *                          Can be a string if only one ID or class to show/hide.
 *                          In both cases, don't forget the # or . before each ID or class name.
 */
function toggle(elementsNames) {
    
    if(typeof(elementsNames) === "object") {
        // If elementsNames is a list of IDs or classes, treat each one.
        for(let i=0; i<elementsNames.length; i++) {
            var occurrencies = document.querySelectorAll(elementsNames);
            // If the element is a class and not an ID, we need a loop to treat all the ocurrencies
            for(i=0;i<occurrencies.length;i++) {
                occurrencies[i].classList.toggle("hidden");
            }
        }
    }
    else {
        // If elementsNames is only one ID or class
        var occurrencies = document.querySelectorAll(elementsNames);
        // If the element is a class and not an ID, we need a loop to treat all the ocurrencies
        for(i=0;i<occurrencies.length;i++) {
            occurrencies[i].classList.toggle("hidden");
        }
    }
}


/**
 * Afficher ou masquer un élément à partir de son ID HTML
 * 
 * @param {string|list} htmlIds Les ID HTML des éléments à masquer. Peut être
 *                              un ID seul (string) ou une liste d'IDs.
 * @param {string} newDisplayValue Définir à "none" pour masquer l'élément, "block" pour l'afficher
 */
function changeDisplayValue(htmlIds, newDisplayValue) {
    
    if (typeof(htmlIds) === "object") {
        for (let i=0; i<htmlIds.length; i++) {
            document.getElementById(htmlIds[i]).style.display = newDisplayValue;
        }
    }
    else {
        document.getElementById(htmlIds).style.display = newDisplayValue;
    }
}


function display(htmlIds, visibleValue="block") {    
    changeDisplayValue(htmlIds, visibleValue);
}


function hide(htmlIds) {    
    changeDisplayValue(htmlIds, "none");
}


/**
 * Ajoute la classe .hidden sur les classes CSS demandées
 * 
 * @param   {array} classesNames Liste des noms des classes à masquer
 * @returns {undefined}
 */
function hideClasses(classesNames, parentId=null) {
    
    for (let i=0; i < classesNames.length; i++) {        
        var classes = (parentId === null) ? document.querySelectorAll(`.${classesNames[i]}`)
                                          : document.querySelectorAll(`#${parentId} .${classesNames[i]}`);
        
        for (let i=0; i < classes.length; i++) {
            classes[i].classList.add("hidden");
        }
    }
}


/**
 * Supprime la classe .hidden sur les classes CSS demandées
 * 
 * @param   {array} classNames The names of the classes to display
 * @returns {undefined}
 */
function unhideClasses(classesNames, parentId=null) {
    
    for (let i=0; i < classesNames.length; i++) { 
        var classes = (parentId === null) ? document.querySelectorAll(`.${classesNames[i]}`)
                                          : document.querySelectorAll(`#${parentId} .${classesNames[i]}`);
        
        for (let i = 0; i < classes.length; i++) {
            classes[i].classList.remove("hidden");
        }
    }
}


function hideIds(htmlIds) {
    
    if(typeof(htmlIds) === "string") {
        document.getElementById(htmlIds).classList.add("hidden");
    }
    else {
        for(i=0; i < htmlIds.length; i++){
            document.getElementById(htmlIds[i]).classList.add("hidden");
        }
    }
}


function unhideId(htmlId) {    
    document.getElementById(htmlId).classList.remove("hidden");
}


function toggleHide(htmlId) {
    document.getElementById(htmlId).classList.toggle("hidden");
}


/**
 * Affiche des éléments masqués à partir de noms de classes (pas d'id).
 * Inverse de hideClasses()
 * 
 * @param   {array} classesNames Liste des noms des classes à afficher
 * @returns {undefined}
 */
function displayClasses(classesNames) {
    
    for (let i=0; i < classesNames.length; i++) {
        
        var classes = document.getElementsByClassName(classesNames[i]);
        
        for (let i=0; i < classes.length; i++) {
            classes[i].style.display = "block";
        }
    }
}


/*
 * Button to enlarge/reduce the bag (hide the overflowing items)
 * @returns {undefined}
 */
function toggleBag() {
    
    if(_isBagVisible === true) {
        var newDisplayValue = null; // Back to the default defined in the CSS
        var newWidth = null;
        var newButtonContent = "chevron_right";
        _isBagVisible = false;
    } else {
        var newDisplayValue = "flex";
        var newWidth = "18em";
        var newButtonContent = "chevron_left";
        _isBagVisible = true;
    }
    
    // Show/hide the overflowing items
    var items = document.querySelectorAll("#bagbar .items_list li");
    for(i=0; i<items.length; i++) {
        items[i].style.display = newDisplayValue;
    }
    
    // Update the button +/- according to the action (show/hide)
    document.querySelector("#bagbar .show_more .material-icons").innerHTML = newButtonContent;
    document.querySelector("#bagbar").style.width = newWidth;
}


/**
 * Update the value of a paramter in the URL (ex: ?tab=constructions)
 * 
 * @param {string} name  The name of the parameter
 * @param {string} value The new value of the parameter. Set it to NULL if you want 
 *                       to simply remove the parameter frome the URL
 * @returns {undefined}
 */
function updateUrlParam(name, value) {

    var search_params = new URLSearchParams(window.location.search);
    search_params.delete(name);
    if(value !== null) {
        search_params.append(name, value);
    }
    // Met l'url à jour avec le nouveau paramètre
    window.history.pushState('', 'InvaZion - En ville', '?'+search_params);
}


/**
 * Sanitize the HTML from a string by neutralizing critical characters
 * (doesn't remove the tags)
 * Source: https://stackoverflow.com/questions/1787322/what-is-the-htmlspecialchars-equivalent-in-javascript
 * 
 * @param {string} text
 * @returns {unresolved}
 */
function sanitizeHtml(text) {
    
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    // NB: more complex than a succession of replace() but faster
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


/**
 * Adds text formatting (links, bold...) on a raw string of text
 * 
 * @param {String} text
 * @returns {String}
 */
function text2HTML(text) {
    // **bold**
    text = text.replace(/\*\*([^\*]+)\*\*/g, '<strong>$1</strong>');
    // * list item
    text = text.replace(/\n\*/g, '\n•');
    // *italic*
    text = text.replace(/\*([^\*]+)\*/g, '<em>$1</em>');
    // Add links on the URLs
    text = text.replace(/([a-z]{3,5}:\/\/[^\s]+)/i, '<a href="$1" target="_blank">$1</a>');
    // Convert the textual newlines to HTML <br>
    text = nl2br(text);     
    
    return text;
}
        

/**
 * Wait for XX milliseconds before continuing the execution of the function
 * Ex: await sleep(1000);
 * Important: MUST be placed inside an "async" function to work
 * 
 * @param {int} milliseconds The duration of the pause, in milliseconds
 * @returns {Promise}
 */
function sleep(milliseconds) {
    
    return new Promise(resolve => setTimeout(resolve, milliseconds));
}


/**
 *  Displays the location icon on every zone which contains the specified element
 *  
 *  @param {string} objectToMark The alias of the object to mark on the map.
 *                               See the dictionary "markableObjects" in the present 
 *                               function to know the available aliases.
 */
function toggleMapMarker(objectToMark) {
    
    // Here are listed the DOM selectors to mark the zones you want (e.g. class name...)
    var markableObjects = {
        "items":    ".square_container:not([data-items='0'])",
        "citizens": ".square_container:not([data-citizens='0'])",
        "generic":  "#map [data-marker='1']"
        };

    if (window.areMapMarkersActive !== true) {        
        // Adds the HTML for the icons in the zones
        let classes = document.querySelectorAll(markableObjects[objectToMark]);
        
        for(let i=0; i < classes.length; i++) {
            classes[i].innerHTML += '<img src="resources/img/free/map_location.svg" class="location">';
        }
        window.areMapMarkersActive = true;
    }
    else {    
        // Hides the icons added by the previous call to the function
        let classes = document.querySelectorAll(".location");
        
        for(let i=0; i < classes.length; i++) {
            classes[i].style.display = 'none';
        }
        window.areMapMarkersActive = false;
    }
}


/**
 * Displays/hides the blocks of actions at the right of the map
 * (digging, zombies, citizens in zone, build tent...)
 */
function toggleActionBlock(buttonAlias) {
    
    let blockId = "block_"+buttonAlias;
    let roundId = "round_"+buttonAlias;
    
    // In case the parameter is not in the cookie yet, or not valid
    if (buttonAlias === undefined || document.getElementById(blockId) === null) {
        return false;
    }
        
    if (document.getElementById(blockId).style.display === "block") {
        // If the block is already displayed, the button hides it
        hide(blockId);
        document.getElementById(roundId).classList.remove("active");
        // Will memorize in the cookie to hide the block
        buttonAlias = undefined;
    }
    else {
        // Hides all the action blocks...
        let actionBlocks = document.getElementById("actions").getElementsByTagName("fieldset");
        for (let i=0; i<actionBlocks.length; i++) {
            document.getElementById("actions").getElementsByTagName("fieldset")[i].style.display = "none";
            document.getElementById("round_actions").getElementsByTagName("input")[i].parentNode.parentNode.classList.remove("active");
        }
        // ... Then displays the only action block we want
        document.getElementById(blockId).style.display = "block";
        // ... and hightlights the active button
        document.getElementById(roundId).classList.add("active");
    }
    
    setCookieConfig("round_button", buttonAlias);
}


/**
 * Displays/hides the items panels (bag and ground) at the bottom of the map 
 */
function toggleItemsPanel() {
    
    if (document.getElementById("bag_panel").style.height === "0px") {
        document.getElementById("bag_panel").style.height    = "10em";
        document.getElementById("ground_panel").style.height = "10em";
        setCookie('showitemspanel', 1);
    }
    else {
        document.getElementById("bag_panel").style.height    = 0;
        document.getElementById("ground_panel").style.height = 0;
        setCookie('showitemspanel', 0);
    }
}


/**
 * Gets the ID of the connected player
 * @returns {int}
 */
function getCitizenId() {

    return parseInt(document.getElementById("citizenId").innerHTML);
}
    

/*
 * Active l'onglet du smartphone à droite de la carte
 *
 * @param {str} tabId L'id HTML de l'onglet du smartphone à afficher (minimap, health...)
 *                    La valeur par défaut indique quel onglet sera chargé tant que
 *                    le joueur n'a cliqué sur aucun onglet.
 */
function activatePhoneTab(tabId=null) {
    
    // Définir ici l'onglet affiché par défaut dans le smartphone du jeu
    var defaultTab = "minimap";
    
    // Si clic sur un onglet du smartphone
    if (tabId !== null) {
        // Mémorise l'onglet actif pour le réafficher après actualisation de la page
        setCookieConfig('phonetab', tabId);
    }
    // Si c'est le chargement de la page
    else {
        // Récupère l'onglet actif
        tabId = getCookieConfig('phonetab');
        
        // Si le cookie n'existe pas encore, on fixe un onglet par défaut
        if (tabId === undefined) {
            
            tabId = defaultTab;
        }
    }
    
    // Par défaut, on cache tous les onglet du smartphone
    hide(["minimap", "health", "zone"]);
    // Puis on affiche le contenu de l'onglet actif
    display(tabId);
}


/**
 * Connects the user to his account (sends logins and gets the API result)
 */
async function connectUser() {
    
    let emailField  = document.getElementById("email"),
        email       = emailField.value,
        password    = document.getElementById("password").value;
    
    if (email === '') {
        
        document.getElementById("error").innerHTML = "Veuillez indiquer l'adresse mail que vous aviez utilisée "
                        +"lors de votre inscription.<br>"
                        +"<em>Pas encore inscrit ? <a href=\"register.php\">Créez votre compte maintenant !</a></em>";
    }
    else if (emailField.checkValidity() !== true) {
        
        document.getElementById("error").innerHTML = "L'email que vous avez saisi est invalide. Vérifiez qu'il ne contient pas une faute de frappe...";
    }
    else {
        // Calls the connection API
        let json = await callApi("POST", "user", `action=connect&email=${email}&password=${password}`);

        if (json.metas.error_code !== "success") {

            document.getElementById("error").innerHTML = json.metas.error_message;
        }
        else {
            // Stores the identification token in a cookie
            setCookie("token", json.datas.token);
            // Redirects to the main game page after the connction
            window.location.replace("index.php#Outside");
        }
    }
}


/**
 * Sends the data to create a new item in game
 */
async function createItem() {
    
    let token = getCookie('token'),
        formData = new FormData(document.querySelector('form')),
        request = {};
    
    for (var pair of formData.entries()) {
        request += "&"+pair[0]+"="+pair[1];
    }
    
    // Sends the characteristics of the new item to the API
    let json = await callApi("POST", "configs", `action=create&type=item&token=${token}&${request}`);
    
    document.getElementById("error").innerHTML = json.metas.error_message;
}


/**
 * Displays the discussions list in the notifications panel.
 * WARNING : don't call this function more than needed, because it makes a distant request 
 * to the InvaZion's API.
 */
async function updateDiscussionsNotifs() {
    
    // Gets the titles of the discussions, by calling the InvaZion's API
    var jsonTopics = await callDiscussionApiOnce("all", refresh=true);
    
    var length = jsonTopics.datas.length;
    var titles = "";
    
    for (let i=0; i<length; i++) {        
        let topic        = jsonTopics.datas[i];
        let topicUrl     = urlDiscussion(topic["topic_id"], topic.last_message.message_id);
        let authorPseudo = topic.last_message.author_pseudo;
        let authorId     = topic.last_message.author_id;
        let lastMessage  = topic.last_message.message;
        let localDate    = dateIsoToString(topic.last_message.datetime_utc);

        titles += htmlDiscussionNotif(topic.title, localDate, topicUrl, authorId, authorPseudo, lastMessage);
    }

    document.getElementById("notifsList").innerHTML = titles;
}


/**
 * Moves the player from the central city to his individual home.
 * 
 * @param {int} mapId
 * @param {int} cityId
 * @returns {undefined}
 */
async function teleportToCity(mapId, cityId) {
    
    // Moves the citizen from the main city to his indivdual home
    jsonTeleport = await callApi("GET", "zone", "action=teleport&to=city&target_id="+cityId+"&token="+getCookie('token'));
    
    if(jsonTeleport.metas.error_code === "success") {    
        coordX = jsonTeleport.datas.new_coord_x;
        coordY = jsonTeleport.datas.new_coord_y;

        // Refreshes the contents of the chest (replaces the contents of 
        // the city repository by the contents of the personal chest)
        let options = { method: "GET",
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        };            
        jsonCityEnclosure = await fetch("/generators/city_enclosure.php?city_id="+cityId+"&map_id="+mapId+"&coord_x="+coordX+"&coord_y="+coordY, options).then(toJson);
        document.getElementById("blockHomeStorage").innerHTML = jsonCityEnclosure.datas.html_home_storage;
        document.getElementById("blockCityStorage").innerHTML = jsonCityEnclosure.datas.html_city_storage;
    }
}


/**
 * Change the ground type of the zone (grass, peeble, lava...)
 */
async function updateLandType(landType, coordX, coordY, radius) {
    
    let token = getCookie('token'),
        mapId = document.getElementById("mapId").innerHTML;
    
    // Sends the characteristics of the new item to the API
    let json = await callApi("GET", "zone", `action=edit&stuff=${landType}&coord_x=${coordX}&coord_y=${coordY}&radius=${radius}&token=${token}`);
}


/**
 * Move the citizen on the map
 * 
 * @param {string} direction One of the allowed directions (north, east, northeast...)
 *                           See the documentation of the "zone" API
 */
async function moveCitizen(direction) {
    
    // Ask the API for moving the player
    let token = getCookie('token'); 
    let json = await callApi("GET", "zone", `action=move&to=${direction}&token=${token}`);
    
    let current_AP = (document.querySelector("#actionPoints").innerHTML),
        lost_AP    = json.datas.action_points_lost;
        new_AP     = current_AP - lost_AP;
        
    // Display the eventual error in a toast
    if(lost_AP > 0 || json.metas.error_code !== "success") {
        let error_message = (json.metas.error_code === "success")
                    ? `-${lost_AP} point d'action consommé<br>${new_AP}&#9889; restants`
                    : json.metas.error_message;
        displayToast(error_message, json.metas.error_class);
    }
    
    updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);
}


/**
 * Update the coordinates of the player and other player-related data modified 
 * by the movement
 * 
 * @param {int} newCoordX
 * @param {int} newCoordY
 * @returns {undefined}
 */
function updateMeAfterMoving(newCoordX, newCoordY) {
        
    // Delete the informations about the previous zone (obsolete)
    _myZone = null;    
    
    // Update the stored coordinates of the player
    document.querySelector("#citizenCoordX").innerHTML = newCoordX;
    document.querySelector("#citizenCoordY").innerHTML = newCoordY;
    
    // Update the coordinates of the player in the movement paddle
    updateMovementPaddle(newCoordX, newCoordY);
    // Update the coordinates of the player in the land editor
    updateMapEditor(newCoordX, newCoordY);
    
    // Update the attribute "data-citizen" of the destination zone to add the player
    let htmlCoord = newCoordX+"_"+newCoordY;
        myZone = document.querySelector("#zone"+htmlCoord+" .square_container");
    myZone.dataset.citizens = parseInt(myZone.dataset.citizens, 10) + 1;
    
    updateRoundActionButtons(newCoordX, newCoordY);
    updateCityDistance(newCoordX, newCoordY);
    updateBlockLandType(myZone.dataset.landtype);
    updateEnterBuildingButton(myZone.dataset.citytypeid);
    updateMoveCost(parseInt(myZone.dataset.zombies));
    updateCardCitizensInZone(myZone.dataset.citizens);
    
    setTimeout(centerMapOnMe, 1000);
}


/**
 * Shows/hides the card under the movement paddle notifying the presence 
 * of other humans in the player's zone
 * 
 * @param {int} nbrCitizensInZone The number of citizens in the zone
 *                                (including the current player)
 * @returns {undefined}
 */
function updateCardCitizensInZone(nbrCitizensInZone) {
    
    let display = (nbrCitizensInZone > 1) ? "flex" : "none";    
    document.querySelector("#card_citizens").style.display = display;
}


/**
 * Displays/hides the button to enter the bulding or city in the player's zone. 
 * 
 * @param {int} cityTypeId The ID of the city type in the zone (not the ID of the city)
 */
function updateEnterBuildingButton(cityTypeId) {
    
    // Displays the building's name under the movement paddle
    if(cityTypeId === "") {
        document.querySelector("#block_move #card_building").style.display = "none";
    } else {
        document.querySelector("#block_move .building_name").innerHTML = _configsBuildings[cityTypeId]["name"];
        document.querySelector("#block_move #card_building").style.display = "block";
        document.querySelector("#card_building").classList.add("above_paddle");
        unhideClasses(["ignore_button"]);
        hideIds(["card_dig", "card_ap_cost", "card_citizens"]);
    }
    
    // Button to enter in the city
    let enterCity = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_enterable"] === 1) ? "block" : "none";
    document.querySelector(`#block_move form[name="enter_city"]`).style.display = enterCity;
    
    // Button to destroy the tent (building #13 in the database)
    let destroyCity = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_destroyable"] === 1) ? "block" : "none";
    document.querySelector('#block_move form[name="destroy_city"]').style.display = destroyCity;
    
    // Button to activate a crypt (building ID #2)
    let enterCrypt = (cityTypeId === "2") ? "block" : "none";
    document.querySelector('#button_crypt').style.display = enterCrypt;
    
    // Button to explore the building
    let buildingVisibility = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_explorable"]) ? "block" : "none";
    document.querySelector("#button_explore").style.display = buildingVisibility;
    
    if(cityTypeId !== "") {
        populateBuildingPopup(cityTypeId);
    }
}


/**
 * Create the pop-up describing a building on the map.
 * If you need to open the pop-up after having created it, see openBuildingPopup()
 * 
 * @param {int} cityTypeId The ID of the building, as returned by the Invazion's API
 * @returns {undefined}
 */
function populateBuildingPopup(cityTypeId) {
    
    let building = _configsBuildings[cityTypeId];
    let findableItems = (_configsBuildingsFindableItems[cityTypeId] !== undefined) ? _configsBuildingsFindableItems[cityTypeId] : [];

    // Update the content of the pop-up
    let tplPopupBuilding = document.querySelector('#tplPopupBuilding').content.cloneNode(true),
        popup = document.querySelector("#popsuccess .content");
    popup.innerHTML = "";
    popup.appendChild(tplPopupBuilding);
    popup.querySelector(".building_name").innerHTML = building["name"];
    popup.querySelector(".descr_ambiance").innerHTML = building["descr_ambiance"];
    // Add the list of items findable in this building
    for(let itemId of findableItems) {
        popup.querySelector(".items_list").prepend(htmlItem(itemId, _configsItems[itemId]));
    }
}


/**
 * Open the pop-up describing a building on the map
 * 
 * @param {object} event
 * @returns {undefined}
 */
function openBuildingPopup(event) {
    
    if(event.target.closest(".square_container") !== null) {
        let cityTypeId = event.target.closest(".square_container").dataset.citytypeid;
        if(cityTypeId !== "") {
            populateBuildingPopup(cityTypeId);
            window.location.href = "#popsuccess";
        }
    }
}


/**
 * Update the interface after killing a zombie
 * 
 * @param {string} apiAction The "action" parameter for the API url (e.g. "kill_zombies")
 */
async function killZombies(apiAction) {
    
    // Moves the citizen form the main city to his indivdual home
    json = await callApi("GET", "zone", "action="+apiAction+"&token="+getCookie('token'));
    
    // Display the explosion effect on the zone
    document.querySelector("#explosionMe").classList.add("scale-in");
    setTimeout(function() {
        document.querySelector("#explosionMe").classList.remove("scale-in");
        displayToast(json.metas.error_message, json.metas.error_class);
    }, 1500);
    
    if(json.metas.error_code === "success") {  
        // The main container of the zone where the player is
        let myZone = document.querySelector("#me").parentNode;
        let oldNbrZombies = myZone.dataset.zombies,
            newNbrZombies = Math.max(0, oldNbrZombies - json.datas.nbr_zombies_removed);
        let mapId = document.querySelector("#gameData #mapId").innerHTML;
        
        // Update the action blocks (round buttons next to the map)
        updateBlockActionZombies(newNbrZombies);
        updateMoveCost(newNbrZombies);
        updateBlockAlertControl(myZone.dataset.controlpointszombies, mapId, myZone.dataset.coordx, myZone.dataset.coordy);
        
        // Update the zombie silhouettes on the map zone
        if(newNbrZombies > 0) {
            myZone.querySelector(".zombies img").getAttribute("src").innerHTML = "resources/img/motiontwin/zombie"+newNbrZombies+".gif";
        }
        // Update the hidden data about the zone
        myZone.dataset.zombies = newNbrZombies;
    }
}


/**
 * Display/hide the tootip of an item by clicking on its icon
 * 
 * @param {object} event
 * @returns {undefined}
 */
function toggleItem(event) {
    
    let itemLabel = event.target.closest(".item_label");
    var tooltip = itemLabel.querySelector(".details");
    // If the item's tooltip is already opened, we just hide it
    if(tooltip.style.display === "block") {
        tooltip.style.display = "none";
        itemLabel.style.border = null;
        // Avoids instant re-opening of the tooltip, as it is a click in .item_label too
        event.stopPropagation();
    }
    else {
        // If we want to open a new tooltip, first close all the other tooltips.
        // TODO: we should only treat the one previously opened, not checking 
        // all the tooltips each time
        let classes = document.querySelectorAll(".item_label .details");
        for (let i=0; i < classes.length; i++) {
            classes[i].style.display = "none";
            classes[i].closest(".item_label").style.border = null;
        }
        // Then, display the intended tooltip
        tooltip.style.display = "block";
        itemLabel.style.border = "2px solid darkred";
        
        // Avoid the item's tooltip to overflow the parent container
        let parentList = tooltip.closest(".items_list");
        let tooltipRect = tooltip.getBoundingClientRect();
        let parentListRect = parentList.getBoundingClientRect();
        // Avoid overflowing on the right
        let rightGap = parentListRect.right - tooltipRect.right;
        if(rightGap < 0) {
            tooltip.style.marginLeft = `${rightGap}px`;
        }
        // Avoid overflowing at the bottom
        let bottomGap = parentListRect.bottom - tooltipRect.bottom;
        if(bottomGap < 0) {
            tooltip.style.marginTop = `${bottomGap}px`;
        }
    }
}


/**
 * Converts a raw UTC date to a string text date
 * 
 * @param {string} utcDate  The date as returned by the Invazion's API (UTC time + ISO 8601 format)
 *                          Example : "2020-02-18T14:51:41+01:00"
 * @return {string} The human-readable date (e.g.: "lundi 6 juin 2020 à 13h40")
 */
function dateIsoToString(utcDate) {
    // Set here the presentation you want for the date
    // Available options : https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Objets_globaux/Intl/DateTimeFormat#Syntaxe
    var dateFormat  = { weekday:'long', year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'numeric' };
    
    return Intl.DateTimeFormat('fr-FR', dateFormat).format(new Date(utcDate));
}


/**
 * Converts newlines into <br> in a text to preserves them in HTML
 * Source : https://gist.github.com/yidas/41cc9272d3dff50f3c9560fb05e7255e
 *
 * @param {string}  text Input text
 * @return {string} Filtered text
 */
function nl2br (text) {
    
    return (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+'<br>');
}


/**
 * Gets the log of attacks with the API and write it in the communications panel
 */
async function getCyclicAttacks(nbrExecutions) {
    
    // Don't run the function more than once (it calls the API)
    if (nbrExecutions >= 1) {
        return false;
    }
    
    // Get the HTML elements to build the log of attacks
    let options = { method: "GET",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    };
    let htmlElements = await fetch("/generators/log_attacks.php?action=get&type=cyclicattack&sort=desc", options).then(toJson);
    
    // Display the log of attacks
    document.getElementById("wallAttacks").innerHTML += htmlElements.join("\n");
}


/**
 * Gets the log of the citizens actions (healing...)
 * @param {string} htmlContainerId L'ID du <div> dans lequel écrire la liste des événements 
 */
async function getLogEvents(htmlContainerId) {
    
    var json = await callApi("GET", "discuss/threads", "action=get&type=event"),
        html = "";
        
    for (let i in json.datas) {
        html += htmlEventTemplate(json.datas[i]);
    }
    
    document.getElementById(htmlContainerId).innerHTML = html;
}


/*
 * Get the citizens of the map by calling the Invazion's API
 */
async function getMapCitizensOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_citizens === null) {
        let json = await callApi("GET", "citizens", `action=get&map_id=${mapId}`);    
        _citizens = json.datas;
    }
    
    return _citizens;
}


/*
 * Get the cities of the map by calling the Invazion's API
 */
async function getMapCitiesOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_cities === null) {
        let json = await callApi("GET", "cities", `action=get&map_id=${mapId}`);    
        _cities = json.datas;
    }
    
    return _cities;
}


/*
 * Get all the zones of the map by calling the Invazion's API
 */
async function getMapZonesOnce(mapId) {
    
    if(_jsonMap === null) {
        let json = await callApi("GET", "maps", "action=get&map_id="+mapId);
        _jsonMap = json.datas.zones;
    }
    
    return _jsonMap;
}


/*
 * Get the data about the player's zone by calling the Invazion's API
 */
async function getMyZoneOnce(mapId, coordX, coordY) {
    
    // If the API has already be called before, don't re-call it
    if(_myZone === null) {
        let htmlCoord = coordX+"_"+coordY,
            token = getCookie('token'),
            json = await callApi("GET", "maps", `action=get&map_id=${mapId}&token=${token}&zones=${htmlCoord}`);    
        _myZone = json.datas.zones[htmlCoord];
    }
    
    return _myZone;
}


/**
 * Display the actions for fighting against zombies
 * 
 * @param {int} nbrZombies The number of zombies in the player's zone
 */
function showFightingZombiesButtons(nbrZombies) {
    
    if(nbrZombies > 0) {
        document.querySelector("#action_zombies").style.display = "block";
    }
}


/**
 * Digs to find an item in the zone
 */
async function dig() {
    
    let token = getCookie('token');
    
    // Calls the API to dig
    let json = await callApi("GET", "zone", `action=dig&token=${token}`);
    
    // Displays the result of the digging in pop-up
    document.querySelector("#popsuccess").classList.add("force_visibility");
    document.querySelector("#popsuccess .content").innerHTML = nl2br(json.metas.error_message);
    
    if(json.metas.error_code === "success") {
        // Hides the message "There are no items on the ground..."
        document.querySelector('#items_ground .greytext').style.display = "none";
        // Adds an HTML entry in the ground items list
        for(let itemId of Object.values(json.datas.found_items_ids)) {
            htmlAddGroundItem(itemId, _configsItems[itemId], 1);
        }
        // Makes the digging button inactive
        updateDigButtons(1);
    }
}


/**
 * Picks up an item from the ground and puts it in the player's bag
 * 
 * @param {object} eventSubmitter The event.submitter returned by the eventListener
 */
async function pickupItem(eventSubmitter) {
    
    let token = getCookie('token'),
        itemId = eventSubmitter.value;
    
    // Calls the API to pick up the item
    let json = await callApi("GET", "zone", `action=pickup&item_id=${itemId}&token=${token}`);
    
    if(json.metas.error_code === "success") {
        // HTML: moves the item from the ground list to the bag list
        let itemNode = eventSubmitter.closest("li");
        document.querySelector('#bagbar .items_list').prepend(itemNode);
        // Removes 1 empty slot in the bag
        document.querySelector('#bagbar .empty_slot').remove();
        // Decreases the counter for the ground items
        let myZone = document.querySelector("#me").parentNode.dataset;
        myZone.items = parseInt(myZone.items, 10) - 1;
        document.querySelector("#round_dig .dot_number").innerHTML = myZone.items;
    } else {
        // Displays the eventual error message in a pop-up
        document.querySelector("#popsuccess").classList.add("force_visibility");
        document.querySelector("#popsuccess .content").innerHTML = json.metas.error_message;
    }
}


/**
 * Drops an item from the player's bag and puts it on the ground
 * 
 * @param {object} eventSubmitter The event.submitter returned by the eventListener
 */
async function dropItem(eventSubmitter) {
    
    let token = getCookie('token'),
        itemId = eventSubmitter.value;
    
    // Calls the API to pick up the item
    let json = await callApi("GET", "zone", `action=drop&item_id=${itemId}&token=${token}`);
    
    if(json.metas.error_code === "success") {
        // HTML: moves the item from the ground list to the bag list
        let itemNode = eventSubmitter.closest("li");
        document.querySelector('#items_ground ul').prepend(itemNode);
        // Adds 1 empty slot in the bag
        let tplEmptySlot = document.querySelector('#tplEmptySlot').content.cloneNode(true);
        document.querySelector('#bagbar .items_list').appendChild(tplEmptySlot);
        // Replaces the "drop" icon by the "pick-up" icon for this item
        itemNode.querySelector('.form_drop').style.display = "none";
        itemNode.querySelector('.form_pickup').style.display = "block";
        // Hides the message "There are no items on the ground..."
        document.querySelector('#items_ground .greytext').style.display = "none";
        // Increases the counter for the ground items
        let myZone = document.querySelector("#me").parentNode.dataset;
        myZone.items = parseInt(myZone.items, 10) + 1;
        document.querySelector("#round_dig .dot_number").innerHTML = myZone.items;
        
    } else {
        // Displays the eventual error message in a pop-up
        document.querySelector("#popsuccess").classList.add("force_visibility");
        document.querySelector("#popsuccess .content").innerHTML = json.metas.error_message;
    }
}


/**
 * Hides the pop-up over the map
 */
function closePopup() {
    
    document.querySelector("#popsuccess").classList.remove("force_visibility");
}


///**
// * Place the cities on the map
// * TODO: merge this function with replaceBuildingsPlaceholders(). Requires that
// * the desert buildings are treated as cities in the API and the database.
// * 
// */
//function replaceCitiesPlaceholders() {
//    
//    let zonesWithCity = document.querySelectorAll('#map [data-citytypeid]:not([data-citytypeid=""])');
//    
//    for(let zone of zonesWithCity) {
//        let buildingTypeId = zone.dataset.citytypeid;
//        let config = _configsBuildings[buildingTypeId];
//        // Adds the text in the bubble of the zone
//        zone.querySelector(".bubble .roleplay").innerHTML = '<h5 class="name">'+config.name+'</h5><hr>'
//                                                            +config.descr_ambiance;
//    }
//}


/**
 * Countdown to escape once the humans have lost the control of the zone
 */
function controlCountdown() {
    // Get the number of seconds from now to the end of the countdown
    let timestamp = document.getElementById("controlTimestamp").innerHTML,
        diff = (timestamp*1000-Date.now()) / 1000;
    diff = Math.max(0, diff);
    // Converts the difference to a manipulable date object    
    let date = new Date(1970, 0, 1);
    date.setSeconds(diff);
    
    document.getElementById("controlCountdown").innerHTML = date.getMinutes()+"mn "+date.getSeconds()+"s";
}


/**
 * Countdown before the midnight attack
 * Partially based on https://stackoverflow.com/questions/54256629/countdown-to-midnight-refresh-every-day/54257213
 * Feel free to optimize it...
 */
function attackCountdown() {
    
    // Get the number of seconds from now to midnight
    let now = new Date();
    let nextMidnight = new Date();
    nextMidnight.setHours(24,0,0,0);    
    let remainingSeconds = (nextMidnight.getTime() - now.getTime())/1000;
    
    // Convert the difference to a manipulable date object 
    let date = new Date(1970, 0, 1);
    date.setSeconds(remainingSeconds);
    
    document.getElementById("attackCountdown").innerHTML = date.getHours()+"h "+date.getMinutes()+"mn "+date.getSeconds()+"s";
}


/**
 * Updates the distance to the city displayed under the movement paddle
 * 
 * @param {int} citizenCoordX
 * @param {int} citizenCoordY
 */
async function updateCityDistance(citizenCoordX, citizenCoordY) {
    
    let myCityId = document.querySelector("#gameData #cityId").innerHTML,
        myCityNode = document.querySelector(`[data-cityid="${myCityId}"]`),
        distance = null;   
    
    if(myCityNode !== null) {
        let myCityZone = myCityNode.parentNode.querySelector(".square_container").dataset;
            distance = getDistance(myCityZone.coordx, myCityZone.coordy, citizenCoordX, citizenCoordY);
    }
    // Reduces the image of the city as we move away
    let biggestImageSize = 42,
        smallestImageSize = 16;
    document.querySelector("#block_distance img").height = Math.max(smallestImageSize, biggestImageSize-distance*2);
    // Updates the number of kilometers
    document.querySelector("#block_distance .distance").innerHTML = distance;
}


/**
 * Calculates the number of cells between a citizen and its city
 * 
 * @param {int} cityX    The X coordinate of the city
 * @param {int} cityY    The Y coordinate of the city
 * @param {int} citizenX The X coordinate of the citizen
 * @param {int} citizenY The Y coordinate of the citizen
 * @return {int} The number of cells between the citizen and the city
 */
function getDistance(cityX, cityY, citizenX, citizenY) {

    // We calculate the relative coordinates of the citizen as if the city was in [0:0].
    // And we remove the eventual negative sign, because the orientation (N/S/W/E)
    // has no influence on the distance.
    let distanceX = Math.abs(citizenX - cityX),
        distanceY = Math.abs(citizenY - cityY);

    // Formula provided by https://www.redblobgames.com/grids/hexagons/#distances
    return distanceY + Math.max(0, (distanceX-distanceY)/2);
}


/**
 * Generates the HTML of a citizen with his name in a chips + the actions buttons
 * (to heal, to attack...)
 * 
 * @param {dict} citizen The citzen's data, as returned by the Invazion's API
 *                       (citizen_id, citizen_pseudo...) 
 * @param {boolean} bigChips Set to "true" to make bigger the chips of the user
 * @param {boolean} displayActionButtons Set to "false" if you want to hide the buttons 
 *                                    for healing/attacking the citizen (useful 
 *                                    when the citizen is not in the same zone)
 * @param {boolean} displayItsMe Set to "true" to display a mention "It's me"
 *                               next to the user chips
 * @returns {string} HTML
 */
function getHtmlActionBlockFellow(citizen, bigChips=false, displayActionButtons=true, displayItsMe=false) {
    
    // The model for the HTML is located in a <template> tag
    template = document.querySelector("#tplActionBlockFellow").content.cloneNode(true);
    
    // Populates the template with the citizen's data
    template.querySelector(".pseudo").innerHTML = citizen.citizen_pseudo;
    template.querySelector('form[name="attack"] input[name="params[target_id]"]').value = citizen.citizen_id;
    template.querySelector('form[name="heal"] input[name="params[target_id]"]').value = citizen.citizen_id;
    
    if(bigChips === true) {
        template.querySelector(".userListItem").classList.add("big");
    }
    if(displayItsMe === false) {
        template.querySelector('.itsMe').style.display = "none";
    }
    
    // Displays/hides the buttons attack/heal according to the wounds of the player
    if(displayActionButtons === false) {
        template.querySelector('.actionButtons').style.display = "none";
    }
    else if(citizen.is_wounded === 0) {
        template.querySelector('form[name="heal"]').style.display = "none";
    } else {
        template.querySelector('form[name="attack"]').style.display = "none";
    }
    
    return template;
}


/**
 * Generates the image of an item
 * 
 * @param {int} itemId The ID of the item, as returned by the "items" API
 * @param {int} height The dimensions to resize the image
 * @returns {String} HTML
 */
function image(itemId, height) {
    
    let icon_path   = _configsItems[itemId]["icon_path"],
        icon_symbol = _configsItems[itemId]["icon_symbol"];

    return (icon_path !== null) ? `<img src="resources/img/${icon_path}" alt="icon_symbol" height="${height}"  width="${height}">` : icon_symbol;
}


/**
 * Displays the stages constituting the midnight attack 
 * (kill the citizens outside, etc.)
 * 
 * @returns {undefined}
 */
async function displayMessageEndCycle() {

   let textZoneId = "messageEndCycle";
   let stages = [  {"icon": '&#127751;',
                    "descr": "Fin du cycle. Le soleil se couche..."},
                   {"icon": '&#127964;&#65039;',
                    "descr": "Mort des citoyens non-abrités..."},
                   {"icon": '&#129440;',
                    "descr": "Mort des citoyens blessés..."},
                   {"icon": '<img src="resources/img/motiontwin/zombie4.gif" alt="&#129503;">',
                            "descr": "Apparition de nouveaux zombies sur la carte..."},
                   {"icon": '<img src="resources/img/thirdparty/notoemoji/sunrise-512.webp" alt="&#127748;" height="52">',
                            "descr": "Une nouvelle journée commence ! Bonne chance..."}
                   ];

//   hide("timer");
   display(textZoneId);

   for(let stage of stages) {
       document.getElementById(textZoneId).innerHTML = `<span style="font-size:2.6em;">${stage.icon}</span>${stage.descr}`;
       await sleep(1500);
   }
   await sleep(1500);

   hide(textZoneId);
//   display("timer");
}


/**
 * Highlights the features of the game, by using the "feature discovery" function 
 * of Materialize.css.
 * See their documentation: https://materializecss.com/feature-discovery.html
 * 
 */
function launchTutorial(elems, instances, step) {

    // ---- FIX FOR MATERIALIZE.CSS ----//
    // 
//    // Fix #1: missing image of the feature
//    // The "Feature discovery" of Materialize seems to have a big bug: if more than
//    // one feature is defined (with <div class="tap-target">), the image of the feature
//    // is not displayed, excepted for the first feature. Cause: it seems like 
//    // Materialize fails to inject a required piece of HTML. The code below adds it. 
//    
//    // The missing bunch of HTML that contains the image of the feature, and that
//    // Materialize.css mysteriously doesn't insert.
//    let tapTargetWaveNode = document.querySelector("#tplTapTargetWave").content.cloneNode(true);
//    // Clones the HTML of image of the feature (ex: the round button for digging)
//    let tapTargetNode = document.querySelector(`#${elems[step].dataset.target}`).cloneNode(true);
//    // Add the missing bunch of HTML after the text of the feature 
//    // (= after the appropriate .tap-target)
//    elems[step].appendChild(tapTargetWaveNode);
//    // Add the image of the feature inside the newly inserted HTML
//    elems[step].querySelector(".tap-target-origin").appendChild(tapTargetNode);
//    
//    // Fix #2 (part 2/2): the features placed in absolute-positioned block enlarge the page
//    // For the features we have marked as needing the fix (marked by the homemade "fix-position" class),
//    // go back to the .tap-target-wrapper parent (created on-the-fly by Materialize)
//    // and modify its position to keep it inside the width of the page.
//    if(elems[step].querySelector(".tap-target-origin").parentNode.parentNode.classList.contains("fix-position") === true) {
//        let tapTargetWrapper = elems[step].querySelector(".tap-target-origin").parentNode.parentNode.parentNode;
//        tapTargetWrapper.style.left = 0;
//        // Fix #3: the feature placed in absolute-positioned block shift the attack bar
//        // out of the screen
//        // Remove the "abolute position", because it shifts the attack bar
//        // (normally fixed at the bottom) ou of the screen
//        tapTargetWrapper.style.position = "";
//    }    
    //
    // ---- END OF THE FIX ----//

    // Open the Materialize's feature highlighter
    // NB: this condition ensures that the user can't open simultaneous occurrences
    // of the tutorial
    if((step === 0 && instances[0].isOpen === false) || step !== 0) {
        instances[step].open();
    }
    // When the users closes a feature, open the next one
    instances[step].options.onClose = function() {
        // Remove the fix previously added, to avoid cumultating multiple occurrences of it
        // if the user reloads tutorial later
//        elems[step].querySelector(".tap-target-origin").remove();
        // Open the next step of the tutorial
        if(step+1 < instances.length) {
            launchTutorial(elems, instances, step+1);
        }
    };
}


/**
 * Calculate the number of action points of the citizens in a zone
 * 
 * @param {array} citizens The datas of the "citizens" API
 * @param {int} mapId
 * @param {int} coordX
 * @param {int} coordY
 */
async function sumControlpoints(citizens, mapId, coordX, coordY) {
    
    var sumControlpoints = 0;
    
    for(let citizen of Object.values(citizens)) {
        if(citizen.coord_x === parseInt(coordX) && citizen.coord_y === parseInt(coordY)) {
            sumControlpoints += citizen.control_points;
        }
    }

    return sumControlpoints;
}


/**
 * Updates the content of the block descriptibing the type of land,
 * under the movement paddle.
 * 
 * @param {string} landType The alias of the type of land (peeble, sand, water...)
 * @returns {undefined}
 */
function updateBlockLandType(landType) {
    
    let lands = {
        "peeble": {"name": "Cailloux épars",
                   "descr": "La présence de ces cailloux indique qu'un bâtiment\n"
                            +"se trouve non loin, dans un rayon de 2 zones.\n"
                            +"Poursuivez vos recherches..."
                   },
        "bigpeeble": {"name": "Rocaille",
                      "descr": "On trouve généralement un bâtiment dans une zone\n"
                               +"adjacente à ce type de terrain. Encore un effort..."
                      }
        };

    document.querySelector("#block_landtype").innerHTML = (lands[landType] !== undefined ? '<span title="'+lands[landType]["descr"]+'">&#128065;&#65039; '+lands[landType]["name"]+'</span>' : '');
}


/**
 * To control a bot citizen
 * 
 * @param {int} targetCitizenId the ID of the citizen you want to take control over.
 * @returns {undefined}
 */
async function switchToCitizen(targetCitizenId) {
    
    let token = getCookie('token');
    let json = await callApi("GET", "me", `action=switch_citizen&target_id=${targetCitizenId}&token=${token}`);
    
    // Update the cookie to write the token corresponding to the now-controlled citizen 
    if(json.metas.error_code === "success") {
        setCookie("token", json.datas.token);
    }
    
    displayToast(json.metas.error_message, json.metas.error_class);
}


/**
 * Short way to display a toast with Materialize.css
 * 
 * @param {string} message The text of the message to put in the toast
 * @param {type} error_class One of the standardized class returned by the InvZion's API
 *                           ("info", "warning", "critical"). This allow changing 
 *                           the background of the toast according to the gravity 
 *                           of the alert.
 * @returns {undefined}
 */
function displayToast(message, error_class) {
    
    M.toast({
        html: message,
        classes: error_class,
        displayLength: 2500,
        outDuration: 800
        });
}


/**
 * 
 * Returns the number of the current cycle (day XX)
 * @returns {int}
 */
function getCurrentCycle() {
    
    return parseInt(document.querySelector("#tuto_dayclock span").innerHTML);
}
