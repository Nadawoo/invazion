/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */

import { Citizen } from "./entities/Citizen.js";
import { gameStates } from "./states/GameStates.js";

/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
export async function isCitizenInGame() {
    
    const myCitizen = new Citizen();
    
    if(myCitizen.id === null) return false;
    
    return (gameStates.citizens !== undefined && gameStates.citizens.get(myCitizen.id) !== undefined);
}
