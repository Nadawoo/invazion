/* 
 * Functions related to the cookies (to create, read...)
 * Don't put executable code in this file, only functions.
 */


/*
 * Crée ou modifie un cookie
 
 * @param {string} name  Le nom du cookie
 * @param {string} value Le contenu du cookie
 */
function setCookie(name, value) {
    
    // Set an explicit expiration time, otherwise the cookies will be deleted 
    // each time he PWA is closed.
    let maxAge = 3600*24*30;
    document.cookie = name+"="+value+"; SameSite=Lax; Max-Age="+maxAge+";"; 
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
