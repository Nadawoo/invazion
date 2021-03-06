/**
 * Javacript version of the ZombLib (library to easily use the API of InvaZion)
 * v. 1.3
 */


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
 * Sends a form with the GET or POST method
 * 
 * @param {string} method  The HTTP method to send the data (GET or POST)
 * @param {string} apiName The name of the Invazion's API to call (map, citizen, city...) 
 *                         E.g.: for the API "https://invazion.nadazone.fr/api/map", apiName is "map"
 * @param {string} params  The additional parameters to send to the API, as a unique string
 *                         E.g.: "action=get&citizen_id=87"
 *                         (to know the available parameters, see the online API doc:
 *                         https://invazion.nadazone.fr/apis-list)
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
