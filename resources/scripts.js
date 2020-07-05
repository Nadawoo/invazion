// Will store the result of the API whichs gives the discussions list 
var jsonDiscussionApi;


/**
 * Returns the URL of the central server of InvaZion (which contains the APIs).
 * No need to change this unless you are the main developer of InvaZion.
 */
function getOfficialServerRoot() {
    
    return (window.location.hostname === "invaziongame.localhost"
        ? "http://invazion.localhost" 
        : "https://invazion.nadazone.fr");
}



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
 * @param   {string} clasesNames Liste des noms des classes à masquer
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
function switch_tab(activated_tab, inactivated_tab) {
    
    document.getElementById(activated_tab).className = "active_tab";
    document.getElementById(inactivated_tab).className = "inactive_tab";
}


/**
 * Modifie la valeur d'un paramètre dans l'url
 * 
 * @param {string} name  Le nom du paramètre
 * @param {string} value La nouvelle valeur voulue pour le paramètre
 * @returns {undefined}
 */
function update_url_param(name, value) {

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
        document.getElementById(blockId).style.display = "none";
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
    
    var tabs_list = ['city_perso', 'city_fellows', 'city_common', 'city_craft', 'city_build', 'city_door'];
    
    if (className === null) {
        className = tabs_list[0];
    }
    
    // On masque tous les éléments de la ville sans exception...
    hideClasses(tabs_list);
    // ... puis on affiche celui qu'on veut voir
    displayFlex(className);
    // Modifie l'url pour mémoriser dans quel onglet de la ville on se trouve.    
    update_url_param('tab', className);
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
 * Loads the content of a remote page. Use like this :  
 * 
 *      loadPage(url, function(responseText) {
 *      // Put here the code which uses responseText
 *      });
 * 
 * @param {string} url The url of the remote page to get
 */
function loadPage(url, callback) {
    
    // Fallback Microsoft.XMLHTTP for IE6 and IE5
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            if (typeof callback === 'function') callback(xhr.responseText);
        }
    }
    
   xhr.open("GET", url, true);
   xhr.send();
}


/**
 * Sends a form with the GET or POST method
 * 
 * @param {string} apiName The name of the Invazion's API to call (map, citizen, city...) 
 *                         E.g. : for the API "https://invazion.nadazone.fr/api/map", apiName is "map"
 */
 async function callApi(method, apiName, params) {
    
    let root   = getOfficialServerRoot(),
        apiUrl = `${root}/api/${apiName}`,
        option = {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        };
    if(method==="GET") {
        apiUrl += "?"+params;
    } else {
        option.body= params;
    }
    
    // For debugging : uncomment this line to watch in the browser console 
    // how many times you call the distant APIs, and eventually optimize the redundant calls
//    console.log("API call: "+apiName);
    
    return await fetch(apiUrl, option).then(apiResult=>toJson(apiResult));
}


/**
 * Converts a string to JSON and prints the malformed JSON in the console
 */
async function toJson(response) {
    
    try {
        //.text() pour retourner un texte brut, ou .json() pour parser du JSON
        return await response.clone().json();
    } catch (e) {
        await response.clone().text().then(apiResult=>{
            console.error(e);
            console.groupCollapsed("See the result returned by the API:");
            console.log(apiResult);
            console.groupEnd();
        });
    }
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
        jsonDiscussionApi = await callApi("GET", "discuss/threads", "action=get&sort=last_message_date");
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
 * To send a reply in an existing discussion
 */
async function replyDiscussion(topicId) {
    
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        message  = document.getElementById("message"+topicId).value,
        token    = getCookie('token');
        
    let json = await callApi("POST", "discuss/threads", `action=reply&topic_id=${topicId}&message=${message}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        // Clears and hides the form after posting
        document.getElementById("message"+topicId).value = "";
        document.getElementById("sendform"+topicId).style.display = "none";
        // Unhides the "Reply" button
        document.getElementById("replyButton"+topicId).style.display = "block";
        // Appends the text of the posted reply at the bottom of the discussion
        document.getElementById("replies"+topicId).innerHTML += htmlDiscussionMessage(message, citizenPseudo, new Date().toISOString());
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
        let localDate    = htmlDate(topic.last_message.datetime_utc);

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
function htmlDate(utcDate) {
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
 * Builds the HTML to notify a new discussion in the notification block
 */
function htmlDiscussionNotif(topicTitle, date, url, authorId, authorPseudo, lastMessage) {
    
    authorPseudo = (authorPseudo==="") ? "Membre#"+authorId : authorPseudo;
    return '<a href="'+ url +'" target="_blank" class="notif">\
                &#x1F5E8;&#xFE0F; <strong>'+ authorPseudo +'</strong> a répondu à <span style="color:darkred">'+ topicTitle +'</span>\
                <div class="extract">« '+lastMessage+'<span style="color:darkred">...</span> »</div>\
                <div class="date">'+ date +'</div>\
            </a>';
}


function htmlDiscussion(topicId, topicTitle, lastMessage, nbrOtherMessages) {
    
    var url = urlDiscussion(topicId, lastMessage.message_id),
        otherMessagesLink = '<a href="'+url+'" target="_blank" class="link_other_messages">··· voir '+nbrOtherMessages+' réponses ···</a>',
        readMoreLink     = ' <a href="'+url+'" target="_blank" style="font-size:0.8em">[suite...]</a>';
    
    return '<hr>\
            <div class="topic discuss">\
                <h3><a href="'+url+'" target="_blank">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </a></h3>\
                '+otherMessagesLink+'\
                '+htmlDiscussionMessage(lastMessage.message+readMoreLink, lastMessage.author_pseudo, lastMessage.datetime_utc)+'\
                <div id="replies'+topicId+'"></div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';return false">\
                        Répondre...\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+'); return false;">\
                        <div id="replyError'+topicId+'"></div>\
                        <textarea id="message'+topicId+'" placeholder="D\'accord ? Pas d\'accord ? Votre réponse ici..."></textarea>\
                        <input type="submit" value="Envoyer">\
                    </form>\
                </div>\
            </div>';
}


function htmlDiscussionMessage(message, pseudo, utcDate) {
    
    return '<div class="message">\
            <div class="pseudo">&#x1F464; <strong>'+pseudo+'</strong></div>\
            <div class="time" title="Fuseau horaire de Paris">'+htmlDate(utcDate)+'</div>\
            <div class="text">'+nl2br(message)+'</div>\
        </div>';
}


/*
 * Executed as soon as the page loads, without user action
 */

// If we are on the main game page (those elements doesn't exist on the connection page)
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
    
    
    // Displays/hides the notifications panel
    document.getElementById("notifsButton").addEventListener("click", function(){

        if (window.getComputedStyle(document.getElementById("notifsBlock")).display === 'none') {
            updateDiscussionsNotifs();
            document.getElementById("notifsBlock").style.display = 'block';
        }
        else {
            document.getElementById("notifsBlock").style.display = 'none';
        }
    });
    document.getElementById("notifsClose").addEventListener("click", function(){
        document.getElementById("notifsBlock").style.display = 'none';
    });


    // Switches the action "Display my zone"/"Display the map"
    document.getElementById("backToMap").addEventListener("click", function(){
        
        if (getCookieConfig('show_zone') === 1) {
            setCookieConfig('show_zone', 0);
        } else {
            setCookieConfig('show_zone', 1);
        }
        toggle('my_zone');
        toggle('displayMyZone');
        toggle('hideMyZone');
    });
    
    
    // Displays/hides the tooltip of the zone when the mouse hovers the zone
    document.getElementById("map").addEventListener("mouseover", function(){
        displayTooltip(event.target.closest(".hexagon"));
    });
    document.getElementById("map").addEventListener("mouseout",  function(){
        hideTooltip(event.target.closest(".hexagon"));
    });
    // The onclick event is required for the mobile devices (no notion of "hover" there)
    document.getElementById("map").addEventListener("click", function(){
        toggleTooltip(event.target.closest(".hexagon"));
    });
    
    // Same thing when hovering the center of the movement paddle
    document.getElementById("central").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("central").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("central").addEventListener("click", function() {
        toggleTooltip(document.getElementById("my_hexagon"));
    });
    
    // Same thing when hovering the GPS on the smartphone
    document.getElementById("minimap").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("minimap").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("minimap").addEventListener("click", function() {
        toggleTooltip(document.getElementById("my_hexagon"));
    });
}
