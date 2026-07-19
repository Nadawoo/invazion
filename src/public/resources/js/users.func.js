/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */

import { Citizen } from "./entities/Citizen.js";
import { getMapCitizensOnce } from "./mapInit.func.js";


/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
export async function isCitizenInGame(mapId) {
    
    const myCitizen = new Citizen();
    
    if(myCitizen.id === null) return false;

    const _citizens = await getMapCitizensOnce(mapId);
    return (_citizens !== undefined && _citizens[myCitizen.id] !== undefined);
}
