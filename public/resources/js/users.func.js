/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */


/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
function isCitizenInGame() {
    
    return Number.isInteger(parseInt(document.querySelector("#citizenId").innerText));
}
