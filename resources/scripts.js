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
	return "";
}


/*
 * Augmente ou diminue la hauteur du panneau flottant en bas de la carte
 * (liste des objets de la case...)
 * 
 * @param {str} panelId L'id HTML du panneau à agrandir
 * @returns {undefined}
 */
function resize_panel(panelId) {
    
    if (document.getElementById(panelId).className !== "big_panel") {
        
        document.getElementById(panelId).className = "big_panel";
    }
    else {
        
        document.getElementById(panelId).className = "";
    }
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
if (getCookie('show_zone') === "0") {
	
	hide('my_zone');
}
else {
	display('my_zone');
}
