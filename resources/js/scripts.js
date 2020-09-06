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
 * Afficher un élément (par exemple au survol de la souris)
 * 
 * @param {string} element_name     L'id HTML de l'élément à afficher/masquer
 */
function display(element_name) {
    
    document.getElementById(element_name).style.display = "block";
}


/**
 * Masquer un élément (par exemple au survol de la souris)
 * 
 * @param {string} element_name     L'id HTML de l'élément à afficher/masquer
 */
function hide(element_name) {
    
    document.getElementById(element_name).style.display = "none";
}


/**
 * Masque des éléments à partir de noms de classes (pas d'id).
 * Utilisé notamment pour le menu de la ville.
 * 
 * @param   {array} classesNames Liste des noms des classes à masquer
 * @returns {undefined}
 */
function hideClasses(classesNames) {
    
    var i;
    for (i=0; i < classesNames.length; i++) {
        
        var classes = document.getElementsByClassName(classesNames[i]);
        
        var i2;
        for (i2=0; i2 < classes.length; i2++) {
            classes[i2].style.display = "none";
        }
    }
}


/**
 * Affiche des éléments masqués à partir de noms de classes (pas d'id).
 * Inverse de hideClasses()
 * 
 * @param   {array} classesNames Liste des noms des classes à afficher
 * @returns {undefined}
 */
function displayClasses(classesNames) {
    
    var i;
    for (i=0; i < classesNames.length; i++) {
        
        var classes = document.getElementsByClassName(classesNames[i]);
        
        var i2;
        for (i2=0; i2 < classes.length; i2++) {
            classes[i2].style.display = "block";
        }
    }
}


/**
 * Affiche des éléments en Flexbox à partir d'un nom de classe (pas d'id)
 * 
 * @param   {string} className La nom de la classe à afficher
 * @returns {undefined}
 */
function displayFlex(className) {
    
    var classes = document.getElementsByClassName(className);
    
    var i;
    for (i = 0; i < classes.length; i++) {
        classes[i].style.display = "flex";
    }
}


/**
 * Met un onglet au premier plan
 * 
 * @param {type} activated_tab   L'onglet à mettre au premier plan
 * @param {type} inactivated_tab L'onglet à mettre en arrière-plan (masqué)
 * @return {undefined}
 */
function switch_tab(activated_tab, inactivated_tabs) {
    
    document.getElementById(activated_tab).className = "active_tab";
    
    var i;
    for (i = 0; i < inactivated_tabs.length; i++) {
        document.getElementById(inactivated_tabs[i]).className = "inactive_tab";
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
        
        for(var i=0; i < classes.length; i++) {
            classes[i].innerHTML += '<img src="resources/img/map-location.svg" class="location">';
        }
        window.areItemsDisplayed = true;
    }
    else {
    
        // Hides the icons added by the previous call to the function
        let classes = document.getElementsByClassName("location");
        
        for(var i=0; i < classes.length; i++) {
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
    
    // In case the parameter is not present in the cookie yet
    if (buttonAlias === undefined) {
        return false;
    }
    
    let blockId = "block_"+buttonAlias;
    let roundId = "round_"+buttonAlias;
            
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
        for (i=0; i<actionBlocks.length; i++) {
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
 * Affiche le panneau de la ville correspondant à l'onglet actif
 * (chantiers, maison, porte de la ville...)
 * 
 * @param {string} className La classe HTML des éléments à afficher
 * @returns {undefined}
 */
function switchCityTab(className) {
    
    var tabs_list = ['city_perso', 'city_fellows', 'city_storage', 'city_well', 'city_craft', 'city_build', 'city_door'];
    
    if (className === null) {
        className = tabs_list[0];
    }
    
    // On masque tous les éléments de la ville sans exception...
    hideClasses(tabs_list);
    // ... puis on affiche celui qu'on veut voir
    displayFlex(className);
    // Modifie l'url pour mémoriser dans quel onglet de la ville on se trouve.    
    updateUrlParam('tab', className);
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
	for(var i = 0; i <ca.length; i++) {
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
    hideClasses(["screen"]);
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
 * Show/hide the vertical panel for the discussions and events
 */
function enlargeWall() {
    
    let minBarHeight = "2.5rem",
        maxBarHeight = "100%";

    if (document.getElementById("floating_wall").style.height !== maxBarHeight) {
        // Enlarges the panel...
        document.getElementById("floating_wall").style.height = maxBarHeight;
        document.getElementById("enlarge_wall").getElementsByClassName("arrow")[0].style.transform = "rotate(+180deg)";
        // ... and loads the discussions if not already loaded
        if (document.getElementById("discussions").innerHTML === "") {
            updateDiscussionsList();
        }
    }
    else {
        document.getElementById("floating_wall").style.height = minBarHeight;
        document.getElementById("enlarge_wall").getElementsByClassName("arrow")[0].style.transform = "rotate(0)";
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
    
    for (i=0; i<length; i++) {        
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
 * Shifts the zone tooltip to the left if it overflows the map on the right
 * 
 * @param {type} hexagon The zone where the tooltip is
 * @returns {undefined}
 */
function handleTooltipOverflow(hexagon) {
    
    let tooltipBounding = hexagon.getElementsByClassName("bubble")[0].getBoundingClientRect();
    let mapBounding    = document.getElementById("map").getBoundingClientRect();
    if (tooltipBounding.right > mapBounding.right) {
        hexagon.getElementsByClassName("bubble")[0].style.left        = "-15em";
        hexagon.getElementsByClassName("triangle_down")[0].style.left = "16em";
    }
    else if (tooltipBounding.left < mapBounding.left) {
        hexagon.getElementsByClassName("bubble")[0].style.left        = "0.5em";
        hexagon.getElementsByClassName("triangle_down")[0].style.left = "0.5em";
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
        hexagon.getElementsByClassName("bubble")[0].style.display = "block";
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
        hexagon.getElementsByClassName("bubble")[0].style.display = "none";
    }
}


/**
 * Switches the display/hide of the tooltip on the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function toggleTooltip(hexagon) {
    
    if (hexagon !== null) {
        var current_display = window.getComputedStyle(hexagon.getElementsByClassName("bubble")[0]).display;
        (current_display === "none") ? displayTooltip(hexagon) : hideTooltip(hexagon);
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
    
    var playerPseudo = document.getElementById("citizenPseudo").innerHTML;
    var length = jsonTopics.datas.length;
    var titles = "";
    
    for (i=0; i<length; i++) {        
        let topic            = jsonTopics.datas[i],
            nbrOtherMessages = topic.nbr_messages-1;

        titles += htmlDiscussion(topic.topic_id, topic.title, topic.last_message, nbrOtherMessages);
    }
    
    document.getElementById("discussions").innerHTML = titles;
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
    
    for(var msg in messages) {
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
 * Generates the HTML for the result of the daily zombies attack
 * @param {array} apiData The data as returned by the API "events"
 */
function htmlEventAttack(apiData) {
    
    var zombiesOverflow = apiData.zombies-apiData.defenses,
        message = "";
        
    if (zombiesOverflow <= 0) {
        message = htmlAttackRepulsed(apiData);
    } else {
        message = htmlAttackNotRepulsed(apiData);
    }
    
    return htmlEvent(message.title, message.message, dateIsoToString(apiData.datetime_utc));
}


/**
 * Gets the log of attacks with the API and write it in the communications panel
 */
async function getCyclicAttacks(nbrExecutions) {
    // Don't run the function more than once (it calls the API)
    if (nbrExecutions >= 1) {
        return false;
    }
    
    var json = await callApi("GET", "events", "action=get&type=cyclicattack"),
        html = "";

    for (var i in json.datas) {
        html += htmlEventAttack(json.datas[i]);
    }
    
    document.getElementById("events").innerHTML += html;
}


/*
 * Executed as soon as the page loads, without user action
 */

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Memorizes if the player wants to see the whole map or just the area where he is
    if (getCookieConfig('show_zone') === 1) {
        display('my_zone');
        hide('displayMyZone');
    }
    else {
        hide('my_zone');
        hide('hideMyZone');
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
        switchCityTab(search_params.get('tab'));
    }
}
