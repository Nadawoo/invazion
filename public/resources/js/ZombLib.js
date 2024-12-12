/**
 * Javacript version of the ZombLib (library to easily use the API of Azimutant)
 * v. 1.3
 */


/**
 * Returns the URL of the central server of Azimutant (which contains the APIs).
 */
async function getOfficialServerRoot() {
    
    const configs = await getConfigFile();
    let result = false;

    if (window.location.hostname === filterUrlHostName(configs.dev.gui_server_root)) {
        result = configs.dev.api_server_root;
    } else if (window.location.hostname === filterUrlHostName(configs.prod.gui_server_root)) {
        result = configs.prod.api_server_root;
    } else {
        result = false;
    }

    return result;
}


/**
 * Extract the host name from an url
 * Ex: "http://mydomain.com" => "mydomain.com"
 * 
 * @param {string} fullUrl
 * @returns {String}
 */
function filterUrlHostName(fullUrl) {
    
    const url = new URL(fullUrl);
    return url.hostname;
}


/**
 * Get the configuration set for the server (dev/prod)
 * @returns {unresolved}
 */
async function getConfigFile() {
    
    try {
        const response = await fetch('../config.json');
        return await response.json();
    } catch (error) {
        console.error('[Azimutant] Error while loading configuration file :', error);
    }
}


/**
 * Sends a form with the GET or POST method
 * 
 * @param {string} method  The HTTP method to send the data (GET or POST)
 * @param {string} apiName The name of the Azimutant's API to call (map, citizen, city...) 
 *                         E.g.: for the API "https://invazion.nadazone.fr/api/map", apiName is "map"
 * @param {string} params  The additional parameters to send to the API, as a unique string
 *                         E.g.: "action=get&citizen_id=87"
 *                         (to know the available parameters, see the online API doc:
 *                         https://invazion.nadazone.fr/apis-list)
 */
 async function callApi(method, apiName, params) {
    
    let root   = await getOfficialServerRoot(),
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
    
    return await fetch(apiUrl, option).then(toJson);
}


/**
 * Converts a string to JSON and prints the malformed JSON in the console
 */
async function toJson(apiResult) {
    
    try {
        //.text() pour retourner un texte brut, ou .json() pour parser du JSON
        return await apiResult.clone().json();
    } catch (e) {
        await apiResult.clone().text().then(apiResult=>{
            console.error(e);
            console.groupCollapsed("See the result returned by the API:");
            console.log(apiResult);
            console.groupEnd();
            throw e;
        });
    }
}
