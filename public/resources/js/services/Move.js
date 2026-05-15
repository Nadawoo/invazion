import { ZombLib } from "../lib/ZombLib.js";
import {
    displayToast,
    updateActionBlocksAfterMoving,
    updateActionPoints,
    updateMeAfterMoving 
    }
    from "../misc.func.js";


/**
 * Various methods for moving a citizen from a zone to an other.
 * 
 * @type type
 */
export class Move {
    
    /**
     * Move a citizen to an adjacent zone
     * 
     * @param {string} direction One of the allowed directions (north, east, northeast...)
     *                           See the documentation of the "zone" API
     */
    async walk(direction) {

        // Ask the API for moving the player
        let cookies = new Cookies(),
            token = cookies.getCookie('token');

        let zombLib = new ZombLib();
        let json = await zombLib.callApi("GET", "zone", `action=move&to=${direction}&token=${token}`);

        let current_AP = (document.querySelector("#actionPoints").innerText),
            lost_AP    = json.datas.action_points_lost,
            newAP     = current_AP - lost_AP;

        // Display the eventual error in a toast
        if(lost_AP > 0 || json.metas.error_code !== "success") {
            let error_message = (json.metas.error_code === "success")
                        ? `-${lost_AP} point d'action consommé<br>(${newAP} restants)`
                        : json.metas.error_message;
            displayToast(error_message, json.metas.error_class);
        }

        if(lost_AP > 0) {
            updateActionPoints(newAP);
        }

        updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);
        updateActionBlocksAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);
    }
    
    
    /**
     * Teleport a citizen to a city
     * 
     * @param {int} destinationCityId The ID of the city which is the destination for the teleportation
     * @returns {undefined}
     */
    async teleportToCity(destinationCityId) {

       // Ask the API for teleporting the player
       let cookies = new Cookies(),
           token = cookies.getCookie('token');

       let zombLib = new ZombLib();
       let json = await zombLib.callApi("GET", "zone", `action=teleport&to=city&target_id=${destinationCityId}&token=${token}`);
       updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);

       // Display the result (error or success) in a toast
       displayToast(json.metas.error_message, json.metas.error_class);

       // Refresh the contents of the chest (replaces the contents of 
       // the city repository by the contents of the personal chest)
//    let options = { method: "GET",
//                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//                    };            
//    let response = await fetch("/generators/city_enclosure.php?city_id="+cityId+"&map_id="+mapId+"&coord_x="+coordX+"&coord_y="+coordY, options);
//    let jsonCityEnclosure = await zombLib.toJson(response);
//    document.getElementById("blockHomeStorage").innerHTML = jsonCityEnclosure.datas.html_home_storage;
//    document.getElementById("blockCityStorage").innerHTML = jsonCityEnclosure.datas.html_city_storage;
    }
    

    /**
     * Move a citizen to a city by using the roads
     * 
     * @param {Array} path The IDs of the cities constituting the path
     *                     Ex: [104,48,302]
     * @returns {undefined}
     */
    async driveToCity(path) {

        // Ask the API for teleporting the player
        let cookies = new Cookies(),
            token = cookies.getCookie('token');

        let zombLib = new ZombLib();
        let json = await zombLib.callApi("GET", "zone", `action=drive&path=${path}&token=${token}`);
        updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);

        // Display the result (error or success) in a toast
        displayToast(json.metas.error_message, json.metas.error_class);
    }
}
