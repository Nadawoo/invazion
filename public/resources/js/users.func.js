/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */

import { getMapCitizensOnce } from "./mapInit.func.js";


/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
export async function isCitizenInGame(mapId) {
    
    const citizenIdString = document.querySelector("#citizenId").innerText;
    
    if(citizenIdString === "") {
        return false;
    }
    else {
        const citizenId = Number(citizenIdString);
        const _citizens = await getMapCitizensOnce(mapId);
        
        return (_citizens !== undefined && _citizens[citizenId] !== undefined);
    }
}
