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
    
    document.getElementById(element_name).style.display = (
        current_display === "none" ? "block" : "none"
        );
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
	
	document.cookie = name+"="+value; 
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
        setCookie('phonetab', tabId);
    }
    // Si c'est le chargement de la page
    else {
        // Récupère l'onglet actif
        tabId = getCookie('phonetab');
        
        // Si le cookie n'xiste pas encore, on fixe un onglet par défaut
        if (tabId === null) {
            
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
    //.text() pour retourner un texte brut, ou .json() pour parser du JSON
    return await fetch(apiUrl, option).then(a=>a.json());
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
            document.cookie = "token="+json.datas.token;
            // Redirects to the main game page after the connction
            window.location.replace("index.php#Outside");
        }
    }
}


/**
 * To send a reply in an existing discussion
 */
async function replyDiscussion() {
    
    let topic_id = document.getElementById("topic_id").getAttribute("value"),
        message  = document.getElementById("message").value,
        token    = getCookie('token');
        
    let json = await callApi("POST", "discuss/threads", `action=reply&topic_id=${topic_id}&message=${message}&token=${token}`);
    
    if (json.metas.error_code === "success") {
        document.getElementById("message").value = "";
        document.getElementById("replies").innerHTML += htmlDiscussionMessage(message, "John Doe");
    }
    else {
        document.getElementById("replyError").innerHTML = '<span class="red">'+json.metas.error_message+'</span>';
    }
}


/**
 * Gets the list of the discussion threads (to display it in the notifications panel, for example) 
 * WARNING : don't call this function more than needed, because it makes a distant request 
 * to the InvaZion's API.
 */
async function callDiscussTopics() {
    
    // Gets the titles of the discussions, by calling the InvaZion's API
    var jsonTopics = await callApi("GET", "discuss/threads", "action=get&sort=last_message_date");
    
    // Sets the presentation of the date of the discussion
    var dateFormat  = { weekday:'long', year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'numeric', }
    
    var length = jsonTopics.datas.length;
    var titles = "";
    
    for (i=0; i<length; i++) {        
        let topic        = jsonTopics.datas[i];
        let topicUrl     = getOfficialServerRoot()+'/discuss/topic?topic='+topic["topic_id"]+'#msg'+topic.last_message.message_id;
        let authorPseudo = topic.last_message.author_pseudo
        let dateObject   = new Date(topic.last_message.datetime_utc); 
        let localDate    = new Intl.DateTimeFormat('fr-FR', dateFormat).format(dateObject);

        titles += htmlDiscussTopics(topic.title, localDate, topicUrl, authorPseudo);
    }

    document.getElementById("notifsList").innerHTML = titles;
}


/**
 * Builds the HTML to notify a new discussion in the notification block
 */
function htmlDiscussTopics(topicTitle, date, url, authorPseudo) {
    
    return '<a href="'+ url +'" target="_blank" class="notif">\
                &#x1F5E8;&#xFE0F; <strong>'+ authorPseudo +'</strong> a répondu à <span style="color:darkred">'+ topicTitle +'</span>\
                <div class="date">'+ date +'</div>\
            </a>';
}


function htmlDiscussionMessage(message, pseudo) {
    
    return '<div class="message">\
            <div class="pseudo">&#x1F464; <strong>'+pseudo+'</strong></div>\
            <div class="time" title="Fuseau horaire de Paris">Mardi 3 juin (2020) à 13h15</div>\
            <div class="text">'+message+'</div>\
        </div>';
}


/*
 * Exécuté dès le chargement de la page, sans action du visiteur
 */

// Si on est à l'intérieur d'une ville
if (document.getElementById('city_container') !== null) {
    
    // Charge le premier onglet de la ville par défaut
    var search_params = new URLSearchParams(window.location.search);
    switchCityTab(search_params.get('tab'));
}

// Mémorise si le joueur veut voir la carte entière ou juste la zone où il se trouve
if (getCookie('show_zone') === "1") {
    display('my_zone');
    hide('displayMyZone');
}
else {
    hide('my_zone');
    hide('hideMyZone');
}


// Par défaut, la liste des objets du sac et au sol sont réduites
if (getCookie('showitemspanel') === null || getCookie('showitemspanel') === '0') {
    toggleItemsPanel();
}


// Affiche l'onglet actif du smartphone au chargement de la page
activatePhoneTab();


// Displays/hides the notifications panel
document.getElementById("notifsButton").addEventListener("click", function(){
    
    if (window.getComputedStyle(document.getElementById("notifsBlock")).display === 'none') {
        callDiscussTopics();
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
    
    if (getCookie('show_zone') === '1') {
        setCookie('show_zone', 0);
    } else {
        setCookie('show_zone', 1);
    }
    toggle('my_zone');
    toggle('displayMyZone');
    toggle('hideMyZone');
});
