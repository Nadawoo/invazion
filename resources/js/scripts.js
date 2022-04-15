// Will store the result of the API whichs gives the discussions list 
var jsonDiscussionApi;


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
 * Highlights the active tab in the communication panel and inactivate the others
 * 
 * @param {type} activatedTab The tab to highlight
 * @return {undefined}
 */
function activateDiscussionTab(activatedTab) {
    
    var allTabs = document.getElementById("discussionTabs").children,
        allTabsIds = [];
    // Automatically list the tabs IDs
    for (let i = 0; i < allTabs.length; i++) {
        allTabsIds.push(allTabs[i].getAttribute("id"));
    }
    
    document.getElementById(activatedTab).className = "active_tab";
    
    for (let i = 0; i < allTabsIds.length; i++) {
        if (allTabsIds[i] !== activatedTab) {
            document.getElementById(allTabsIds[i]).className = "inactive_tab";
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
    
    var tabsList = ['cityMenuMyHome', 'cityMenuCity', 'cityMenuDoor'];
    
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
    toggle('citizens_list');
    toggle(idName);
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
 * Calls the API to get the list of the discussions, in the most performant way:
 * > By default, calls the API only once, then stores the result in memory (faster)
 * > If you need to update the results, you can force recalling the API (up-to-date but slower)
 * 
 * @param {string} refresh Set this value to "true" to force the function to call the API
 *                         even if the result of a previous call is already stored in memory.
 * @return jsonDiscussionApi JSON list of the discussions returned by the API
 */
async function callDiscussionApiOnce(refresh=false) {
    
    if (jsonDiscussionApi === undefined || refresh === true) {        
        jsonDiscussionApi = await callApi("GET", "discuss/threads", "action=get&sort=last_message_date&fullmsg=1");
    }
    return jsonDiscussionApi;
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
 * Show/hide the vertical panel for the discussions and events
 */
async function enlargeWall() {
    
    let minBarHeight = "2.5rem",
        maxBarHeight = "100%";

    if (document.querySelector("#floating_wall").style.height !== maxBarHeight) {
        // Enlarges the panel
        document.querySelector("#floating_wall").style.height = maxBarHeight;
        document.querySelector("#enlarge_wall .arrow").style.transform = "rotate(+180deg)";
    }
    else {
        // Reduces the panel
        document.querySelector("#floating_wall").style.height = minBarHeight;
        document.querySelector("#enlarge_wall .arrow").style.transform = "rotate(0)";
    }
}


/**
 * To start a new discussion
 * @returns {Boolean}
 */
async function createDiscussion() {
    
    let title         = document.getElementById("titleNew").value,
        message       = document.getElementById("messageNew").value,
        guest_pseudo  = document.getElementById("guestPseudo").value,
        author_pseudo = document.getElementById("citizenPseudo").innerHTML,
        token         = getCookie('token');

    let json = await callApi("POST", "discuss/threads", `action=create&title=${title}&message=${message}&guest_pseudo=${guest_pseudo}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        json.datas.message = message;
        json.datas.author_pseudo = author_pseudo;
        // Display the new discussion thread
        document.getElementById("newDiscussion").innerHTML += htmlDiscussion(json.datas.topic_id, title, json.datas, 0);
        hide("send");
        // Clear the form for the eventual next thread to send
        document.getElementById("sendform").reset();
    }
    else if (json.metas.error_code === "undefined_pseudo") {
        document.getElementById("errorNewTopicPseudo").innerHTML = json.metas.error_message;
    }
    else {
        document.getElementById("errorNewTopicMessage").innerHTML = json.metas.error_message;
    }
}


/**
 * To send a reply in an existing discussion
 */
async function replyDiscussion(topicId, nbrMessages) {
    
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        message  = document.getElementById("message"+topicId).value,
        token    = getCookie('token');
        
    let json = await callApi("POST", "discuss/threads", `action=reply&topic_id=${topicId}&message=${message}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        // Clears and hides the form after posting
        document.getElementById("message"+topicId).value = "";
        hide("sendform"+topicId);
        // Unhides the "Reply" button
        display("replyButton"+topicId);
        // Appends the text of the posted reply at the bottom of the discussion
        document.getElementById("replies"+topicId).innerHTML += htmlDiscussionMessage(message, citizenPseudo, new Date().toISOString(), nbrMessages+1);
        // Clears the eventual error message (obsolete after sending)
        document.getElementById("replyError"+topicId).innerHTML = "";
    }
    else {
        document.getElementById("replyError"+topicId).innerHTML = '<span class="red">'+json.metas.error_message+'</span>';
    }
}


/**
 * Builds the url to a discussion, eventually with an anchor to a reply
 * Example : https://invazion.nadazone.fr/discuss/topic?topic=7&p=#msg37
 * @param {int} discussionId The ID of the discussion
 * @param {int} messageId    The ID of a message inside the discussion, if you want to
 *                            direct the user directly on it.
 * @return {String}
 */
function urlDiscussion(discussionId, messageId="") {
    
    return getOfficialServerRoot()+'/discuss/topic?topic='+discussionId+'#msg'+messageId;
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
    
    // Moves the citizen form the main city to his indivdual home
    jsonTeleport = await callApi("GET", "zone", "action=teleport&to=city&target_id="+cityId+"&token="+getCookie('token'));
    
    if(jsonTeleport.metas.error_code === "success") {    
        coordX = jsonTeleport.datas.new_coord_x;
        coordY = jsonTeleport.datas.new_coord_y;

        // Refreshes the contents of the chest (replaces the contents of 
        // the city repository by the contents of the personal chest)
        let options = { method: "GET",
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        };            
        jsonCityEnclosure = await fetch("core/view/generators/city_enclosure.php?city_id="+cityId+"&map_id="+mapId+"&coord_x="+coordX+"&coord_y="+coordY, options).then(toJson);
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
 * Update the interface after killing a zombie
 * 
 * @param {string} apiAction The "action" parameter for the API url (e.g. "kill_zombies")
 */
async function killZombies(apiAction, coordX, coordY) {
    
    // Moves the citizen form the main city to his indivdual home
    json = await callApi("GET", "zone", "action="+apiAction+"&token="+getCookie('token'));
    
    document.getElementById("message_move").innerHTML = '<span class="'+json.metas.error_class+'">'+json.metas.error_message+'</span>';
    
    if(json.metas.error_code === "success") {        
        let nbrZombies = document.querySelector("#round_zombies .dot_number"),
            newNbrZombies = Math.max(0, nbrZombies.innerHTML - json.datas.nbr_zombies_removed),
            coordX = document.getElementById("citizenCoordX").innerHTML,
            coordY = document.getElementById("citizenCoordY").innerHTML;
    
        // Update the number of zombies in the round button
        nbrZombies.innerHTML = newNbrZombies;
        
        // Update in the red frame above the movement paddle
        let alertControlNbrZombies = document.querySelector("#alert_control .nbr_zombies");
        if(alertControlNbrZombies !== null) {
            alertControlNbrZombies.innerHTML = newNbrZombies;
            if(newNbrZombies <= 0) {
                hideIds("alert_control");
            }
        }
        
        // Update movement cost (action points)
        if(newNbrZombies <= 0) {
            document.querySelector("#movement_cost").innerHTML = "Déplacement gratuit<br>(vous avez éliminé tous les zombies)";
        }
        
        // Update in the action block "zombies"        
        if(newNbrZombies > 0) {
            document.querySelector("#block_zombies .nbr_zombies").innerHTML = newNbrZombies+" zombies";
            document.querySelector("#block_zombies .zombies_visual .zombie").remove();
        } else {
            document.querySelector("#block_zombies .zombies_text").innerHTML = "Vous avez éradiqué toutes les menaces alentour ! La voie est libre...";
            document.querySelector("#block_zombies .zombies_visual").innerHTML = "";
            document.querySelector("#block_zombies .buttons_kill").innerHTML = "";
        }
        
        // Update the zombie silhouettes on the map zone
        document.querySelector("#zone"+coordX+"_"+coordY+" .zombies img").getAttribute("src").innerHTML = "resources/img/motiontwin/zombie"+newNbrZombies+".gif";
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
 * Displays the discussions on the constructions page in the city.
 */
async function updateDiscussionsList() {
    
    // Gets the titles of the discussions, by calling the InvaZion's API
    var jsonTopics = await callDiscussionApiOnce();
    
    var citizenPseudo = document.getElementById("citizenPseudo").innerHTML;
    var length = jsonTopics.datas.length;
    var discussions = "";
    
    for (let i=0; i<length; i++) {        
        let topic            = jsonTopics.datas[i],
            nbrOtherMessages = topic.nbr_messages-1;

        discussions += htmlDiscussion(topic.topic_id, topic.title, topic.last_message, nbrOtherMessages);
    }
    
    document.getElementById("wallDiscuss").innerHTML = htmlNewDiscussionForm(citizenPseudo)
                                                       + discussions;
}


/**
 * Gets the all the messages of a discussion by calling the InvaZion's API to 
 * 
 * @param {int} topicId the ID of the discussion to load
 * @returns {string} The JSON returned by the API
 */
async function loadDiscussion(topicId) {
    
    var json = await callApi("GET", "discuss/threads", `action=get&topic_id=${topicId}`),
        messages = json["datas"]["messages"],
        htmlMessages = "",
        i = 0;
    
    for(let msg in messages) {
        i++;
        htmlMessages += htmlDiscussionMessage(messages[msg]["message"], messages[msg]["author_pseudo"], messages[msg]["datetime_utc"], i);
    }
    document.getElementById("replies"+topicId).innerHTML = htmlMessages;
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
 * Show/hide the form to create a new discussion thread
 */
function toggleSendform(event) {
    
    toggle("sendform");
    toggle("buttonNewTopic");
    // Desactivate the normal form
    event.preventDefault();
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
    let htmlElements = await fetch("core/view/generators/log_attacks.php?action=get&type=cyclicattack&sort=desc", options).then(toJson);
    
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
    let htmlZones = await fetch("core/view/generators/zone.php?map_id="+mapId+"&newerthan="+timestamp+"&citizen_id="+citizenId+"&citizen_pseudo="+citizenPseudo, options).then(toJson);

    // Updates the HTML for the modified zones
    for (let coords in htmlZones) {
        document.getElementById("zone"+coords).outerHTML = htmlZones[coords];
    }

    // Refresh the timestamp to memorize that these actions have been treated
    return timestamp = await JSON.parse(event.data).citizens;
};


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


/*
 * Executed as soon as the page loads, without user action
 */

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
       
    // Memorizes if the player wants to see the whole map or just the area where he is
    if (getCookieConfig('show_zone') === 1) {
        hideIds("displayMyZone");
        unhideId("my_zone");
        unhideId("hideMyZone");
    } else {
        unhideId("displayMyZone");
    }
    
    toggleActionBlock(getCookieConfig("round_button"));
    
    // By default, the list of objects in the bag and on the ground are reduced
    // UNUSED : the panel is now replaced by the big action button at the right of the map
//    if (getCookie('showitemspanel') === null || getCookie('showitemspanel') === '0') {
//        toggleItemsPanel();
//    }
     
    // Displays the active tab of the in-game du smartphone
    activatePhoneTab();
    
    // If we are inside a city
    if (document.getElementById('city_container') !== null) {    
        // By default, loads the first tab of the city
        var search_params = new URLSearchParams(window.location.search);
        switchCitySubmenu(search_params.get('tab'));
    }
    
    
    // Countdown to escape once the humans have lost the control of the zone
    if (document.getElementById("controlCountdown") !== null) {
        setInterval(controlCountdown, 1000);
    }
    
    // Countdown before the next zombie attack
    attackCountdown();
    setInterval(attackCountdown, 1000);
        
    // Server-sent events to update the map in real time
    var timestamp = Math.floor(Date.now()/1000);
    setTimeout(function() {
        // NB: keep the ".php" extension, otherwise it will give a "CORS error" with the local API version
        let evtSource = new EventSource(getOfficialServerRoot()+"/api/sse.php");
        evtSource.onmessage = async function(event) { 
            timestamp = await UpdateMapRealtime(event, timestamp);
        };
    }, 2000);
}
