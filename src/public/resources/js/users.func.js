/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */

import { Citizen } from "./entities/Citizen.js";
import { gameStates } from "./states/GameStates.js";
import { getMapCitizensOnce } from "./mapInit.func.js";


/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
export async function isCitizenInGame(mapId) {
    
    const myCitizen = new Citizen();
    
    if(myCitizen.id === null) return false;

    gameStates.citizens = await getMapCitizensOnce(mapId);
    
    return (gameStates.citizens !== undefined && gameStates.citizens[myCitizen.id] !== undefined);
}
