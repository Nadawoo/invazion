/**
 * Miscellaneous JS functions that doesn't fit the others JS scripts.
 * Don't put events listeners here (see events.js) nor actions executed 
 * on loading the page (see onPageLoad.js)
 */


/**
 * Afficher/masquer l'élement indiqué en cliquant sur un lien
 * 
 * @param {string} element_name     L'id HTML de l'élément à afficher/masquer
 */
function toggle(element_name) {

    // getComputedStyle(...) récupère la propriété en tenant compte de la CSS.
    // Un simple getElementById(...).style ne tiendrait compte que des
    // styles en ligne dans le HTML.
    var current_display = window.getComputedStyle(document.getElementById(element_name)).display;
        
    document.getElementById(element_name).style.display = (current_display === "none") ? "block" : "none";
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


function display(htmlIds) {    
    changeDisplayValue(htmlIds, "block");
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
        var classes = (parentId === null) ? document.getElementsByClassName(classesNames[i])
                                          : document.getElementById(parentId).document.getElementsByClassName(classesNames[i]);;
        
        for (let i=0; i < classes.length; i++) {
            classes[i].classList.add("hidden");
        }
    }
}


/**
 * Supprime la classe .hidden sur la classe CSS demandée
 * 
 * @param   {string} className La nom de la classe à afficher
 * @returns {undefined}
 */
function unhideClass(className) {
    
    var classes = document.getElementsByClassName(className);
    
    for (let i = 0; i < classes.length; i++) {
        classes[i].classList.remove("hidden");
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


/**
 * Modifie la valeur d'un paramètre dans l'url
 * 
 * @param {string} name  Le nom du paramètre
 * @param {string} value La nouvelle valeur voulue pour le paramètre
 * @returns {undefined}
 */
function updateUrlParam(name, value) {

    var search_params = new URLSearchParams(window.location.search);
    search_params.delete(name);
    search_params.append(name, value);
    // Met l'url à jour avec le nouveau paramètre
    window.history.pushState('', 'InvaZion - En ville', '?'+search_params);
}


/**
 *  Displays the location icon on every zone which contains items
 */
function toggleMapItems() {

    if (window.areItemsDisplayed !== true) {
        
        // Adds the HTML for the icons in the zones
        let classes = document.getElementsByClassName("hasItems");
        
        for(let i=0; i < classes.length; i++) {
            classes[i].innerHTML += '<img src="resources/img/free/map_location.svg" class="location">';
        }
        window.areItemsDisplayed = true;
    }
    else {
    
        // Hides the icons added by the previous call to the function
        let classes = document.getElementsByClassName("location");
        
        for(let i=0; i < classes.length; i++) {
            classes[i].style.display = 'none';
        }
        window.areItemsDisplayed = false;
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
            document.getElementById("round_actions").getElementsByTagName("input")[i].parentNode.classList.remove("active");
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


/*
 * Crée ou modifie un cookie
 
 * @param {string} name  Le nom du cookie
 * @param {string} value Le contenu du cookie
 */
function setCookie(name, value) {
	
	document.cookie = name+"="+value+"; SameSite=Lax"; 
}


/**
 * Creates or updates the parameters of the "config" . It gathers all 
 * the parameters in one cookie, in JSON, to avoid creating multiple cookies.
 * Example :
 * {"round_button":"dig", "show_panel":1}
 * 
 * @param {string} paramName
 * @param {string} paramValue
 */
function setCookieConfig(paramName, paramValue) {
    
    let cookieContent = getCookieConfig();
    cookieContent[paramName] = paramValue;
    setCookie("config", JSON.stringify(cookieContent));
}


/**
 * Gets the value of a parameter in the "config" cookie.
 * 
 * @param {string} paramName the name of the parameter to get (e.g. : "show_panel")
 * @return {string} The value of the parameter
 */
function getCookieConfig(paramName=null) {
    
    cookieContent = getCookie("config");
    
    if (cookieContent === null) {
        return {};
    } else if (paramName === null) {
        return JSON.parse(cookieContent);
    } else {
        return JSON.parse(cookieContent)[paramName];
    }
}


/*
 * Récupère le contenu d'un cookie
 * Source W3C :  
 */
function getCookie(cname) {
	
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(let i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
		  c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
		  return c.substring(name.length, c.length);
		}
	}
	return null;
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
    var jsonTopics = await callDiscussionApiOnce(refresh=true);
    
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
async function updateLandType(landType, coordX, coordY) {
    
    let token = getCookie('token'),
        mapId = document.getElementById("mapId").innerHTML;
    
    // Sends the characteristics of the new item to the API
    let json = await callApi("GET", "zone", `action=edit&stuff=${landType}&map_id=${mapId}&coord_x=${coordX}&coord_y=${coordY}&token=${token}`);
}


/**
 * Move the citizen on the map
 * 
 * @param {string} direction One of the allowed directions (north, east, northeast...)
 *                           See the documentation of the "zone" API
 */
async function moveCitizen(direction) {
    
    // Delete the informations about the previous zone (obsolete)
    _myZone = null;
    
    let token = getCookie('token');
    // Sends the characteristics of the new item to the API
    let json = await callApi("GET", "zone", `action=move&to=${direction}&token=${token}`);
    
    document.getElementById("message_move").innerHTML = (json.metas.error_code === "success") 
        ? ''
        : '<span class="'+json.metas.error_class+'">'+json.metas.error_message+'</span>';
    
    // Update the stored coordinates of the player
    document.querySelector("#citizenCoordX").innerHTML = json.datas.new_coord_x;
    document.querySelector("#citizenCoordY").innerHTML = json.datas.new_coord_y;
    
    // Update the attribute "data-citizen" of the destination zone to add the player
    let htmlCoord = json.datas.new_coord_x+"_"+json.datas.new_coord_y;
        myZone = document.querySelector("#zone"+htmlCoord+" .square_container");
    myZone.dataset.citizens = parseInt(myZone.dataset.citizens, 10) + 1;
    
    updateRoundActionButtons(json.datas.new_coord_x, json.datas.new_coord_y);
    updateActionPointsBar(json.datas.action_points_lost);
}


/**
 * Update the interface after killing a zombie
 * 
 * @param {string} apiAction The "action" parameter for the API url (e.g. "kill_zombies")
 */
async function killZombies(apiAction) {
    
    // Moves the citizen form the main city to his indivdual home
    json = await callApi("GET", "zone", "action="+apiAction+"&token="+getCookie('token'));
    
    document.getElementById("message_move").innerHTML = '<span class="'+json.metas.error_class+'">'+json.metas.error_message+'</span>';
    
    if(json.metas.error_code === "success") {  
        // The main container of the zone where the player is
        let myZone = document.querySelector("#me").parentNode;
        let oldNbrZombies = myZone.dataset.zombies,
            newNbrZombies = Math.max(0, oldNbrZombies - json.datas.nbr_zombies_removed);
            
        // Update the action blocks (round buttons next to the map)
        updateBlockActionZombies(newNbrZombies);
        updateBlockActionMove(newNbrZombies);
        
        // Update the zombie silhouettes on the map zone
        if(newNbrZombies > 0) {
            myZone.querySelector(".zombies img").getAttribute("src").innerHTML = "resources/img/motiontwin/zombie"+newNbrZombies+".gif";
        }
        // Update the hidden data about the zone
        myZone.dataset.zombies = newNbrZombies;
    }
}


/**
 * Adds the connected player on the appropriate zone of the map
 */
function addMeOnMap() {
    
    let myCoordX = document.querySelector("#citizenCoordX").innerHTML,
        myCoordY = document.querySelector("#citizenCoordY").innerHTML,    
        myZone = document.querySelector(`#zone${myCoordX}_${myCoordY} .square_container`);

    let htmlMe = '<div class="map_citizen" id="me"><img src="resources/img/free/human.png"></div>\
                  <div class="halo">&nbsp;</div>',
        htmlBubble = 'Vous êtes ici&nbsp;! Utilisez le volet \
                    à droite de la carte pour vous déplacer, fouiller le sol, \
                    attaquer des zombies, ramasser des objets...</div>';
    
    // Don't show the other citizens under the player's silhouette
    if(myZone.querySelector(".map_citizen") !==  null) {
        myZone.querySelector(".map_citizen").remove();
    }
    
    // Add the player's silhouette 
    myZone.innerHTML += htmlMe;
    myZone.querySelector(".bubble .roleplay").innerHTML = htmlBubble;
}


/**
 * Add a location sign above the player's city on the map
 */
function addCityLocationMarker() {
    
    let htmlBubble = 'Ceci est votre habitation ! Votre refuge contre les zombies...',
        htmlLocationMarker = '<img src="resources/img/free/map_location.svg" class="location">';
        
    let myCityId = document.querySelector("#gameData #cityId").innerHTML,
        myCityNode = document.querySelector(`[data-cityid="${myCityId}"]`);

    if(myCityNode !== null) {
        myCityNode.parentNode.querySelector(".bubble .roleplay").innerHTML = htmlBubble;
        myCityNode.parentNode.innerHTML += htmlLocationMarker;
    }
}


/**
 * Shifts the zone tooltip to the left if it overflows the map on the right
 * 
 * @param {type} hexagon The HTML of the zone where the tooltip is
 * @returns {undefined}
 */
function handleTooltipOverflow(hexagon) {
    
    let tooltipBounding = hexagon.querySelector(".bubble").getBoundingClientRect();
    let mapBounding    = document.querySelector("#map").getBoundingClientRect();
    if (tooltipBounding.right > mapBounding.right) {
        hexagon.querySelector(".bubble").style.left        = "-15em";
        hexagon.querySelector(".triangle_down").style.left = "16em";
    }
    else if (tooltipBounding.left < mapBounding.left) {
        hexagon.querySelector(".bubble").style.left        = "0.5em";
        hexagon.querySelector(".triangle_down").style.left = "0.5em";
    }
}


/**
 * Displays the tooltip over a zone of the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function displayTooltip(hexagon) { 
    
    if (hexagon !== null) {
        // Displays the tooltip
        hexagon.querySelector(".bubble").classList.add("block");
        // Shifts the zone tooltip to the left if it overflows the map on the right
        handleTooltipOverflow(hexagon);
    }
}


/**
 * Hides the tooltip of a zone of the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function hideTooltip(hexagon) {
    
    if (hexagon !== null) {
        hexagon.querySelector(".bubble").classList.remove("block");;
    }
}


/**
 * Switches the display/hide of the tooltip on the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function toggleTooltip(hexagon) {
    
    if (hexagon !== null) {
        hexagon.querySelector(".bubble").classList.toggle("block");
        handleTooltipOverflow(hexagon);
    }
}


/**
 * Displays/hides the big zoomed zone of the player over the map
 */
function toogleMyZone() {
    toggleHide("my_zone");
    toggleHide("displayMyZone");
    toggleHide("hideMyZone");
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
    
    var json = await callApi("GET", "events", "action=get&type=map"),
        html = "";

    for (let i in json.datas) {
        html += htmlLogEvents(json.datas[i]);
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
 * Place the citizens on the map. They are not loaded by the PHP to speed up
 * the loading of the map.
 * 
 * @param {int} mapId
 */
async function addCitizensOnMap(mapId) {
    
    // Get the citizens of the map by calling the Invazion's API
    _citizens = await getMapCitizensOnce(mapId);
    
    // Place the citizens on the appropriate zones
    for(let citizenId in _citizens) {
        let citizen = _citizens[citizenId],
            htmlCoords = citizen.coord_x+"_"+citizen.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
        
        // Don't add the citizen if an other citizen is already placed in the zone
        if(zone.querySelector(".map_citizen") === null && zone.dataset.zombies < 1 && zone.dataset.cityid < 1) {
            
            if(zone.dataset.citizens > 1)  {
                var content = "&#10010;",
                    bubble = "Plusieurs citoyens se sont rassemblés ici... \
                              Complotent-ils quelque chose&nbsp;?";
            } else {
                var content = citizen.citizen_pseudo.slice(0, 2),
                    bubble = "Le citoyen "+citizen.citizen_pseudo+" est ici.";
            }
            
            zone.insertAdjacentHTML("afterbegin", '<div class="map_citizen">'+content+'</div>');
            zone.querySelector(".roleplay").innerHTML = bubble;
            // Delete the "&nbsp;" required on the empty zones 
            if(zone.querySelector(".empty") !== null) {
                zone.querySelector(".empty").remove();
            }
        }
    }
}


/**
 * Refreshes the HTML of the concerned zones when a player moves (server-sent events)
 * param {array} event The event given by an EventSource() object
 *                     Doc : https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
async function UpdateMapRealtime(event, timestamp) {
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        citizenId     = document.getElementById("citizenId").innerHTML,
        mapId         = document.getElementById("mapId").innerHTML;

    // If event notified, get the new HTML contents for the modified zones
    let options = { method: "GET",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    };
    let htmlZones = await fetch("/generators/zone.php?map_id="+mapId+"&newerthan="+timestamp+"&citizen_id="+citizenId+"&citizen_pseudo="+citizenPseudo, options).then(toJson);

    // Updates the HTML for the modified zones
    for (let coords in htmlZones) {
        document.getElementById("zone"+coords).outerHTML = htmlZones[coords];
    }
    
    replaceBuildingsPlaceholders();
    
    // Place the player on his new zone
    addMeOnMap();
    
    // Get informations about the current zone through the "data-*" HTML attributes
    let zoneData = document.querySelector("#me").parentNode.dataset;
    // Display an alert over the movement paddle if the player is blocked
    updateBlockAlertControl(zoneData.zombies);  
    
    // Refresh the timestamp to memorize that these actions have been treated
    return timestamp = await JSON.parse(event.data).zones;
};


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
 * To pick up an item form the ground and put it in the bag
 * 
 * @param {int} itemId The ID of the item to pick up
 */
async function pickupItem(itemId) {
    
    let token = getCookie('token');
    
    // Calls the API to pick up the item
    let json = await callApi("GET", "zone", `action=pickup&item_id=${itemId}&token=${token}`);
    
    // Displays the eventual error message in a pop-up
    if(json.metas.error_code !== "success") {
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


/**
 * Replaces the buildings IDs on the map by the real data (building name, description...)
 * Useful to load those data from the configs stored in JSON in the HTML page,
 * without calling the "configs" API
 */
function replaceBuildingsPlaceholders() {
    
    // Gets all the placeholders on the map
    var buildingIds = document.querySelectorAll("#map .buildingId");
    
    for(let building of buildingIds) {
        // Warning: the class of the parent tag must be named as the field is
        // in the "configs" API. Example for the building #17: class="name" if we want 
        // to get configs["buildings"][17]["name"]
        let field = building.parentNode.className;        
        let buildingId = building.innerHTML;
        // Replaces the building ID placeholder by the data of the field
        building.outerHTML = _configsBuildings[buildingId][field];
    }
}


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
