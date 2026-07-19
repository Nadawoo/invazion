/**
 * Class relative to the list of tasks (goals) displayed to the player.
 */

import { Citizen } from "../entities/Citizen.js";
import { getMyCityZoneId } from "../mapUse.func.js";


export class Tasks {
    
    /**
     * Add the number of defenses required in the "Build defenses" task
     * @returns {undefined}
     */
    populateTaskDefenses() {
        
        const myCitizen = new Citizen();
        let nbrCityDefenses = null,
            nbrZombiesNextAttack = null,
            nbrMissingDefenses = null;
            
        // Avoid errors if the player is not attached to a city yet.
        if(myCitizen.cityId === null) {
            nbrCityDefenses = "??",
            nbrZombiesNextAttack = "??",
            nbrMissingDefenses = "??";
        } else {        
            nbrCityDefenses = _cities[myCitizen.cityId]['total_defenses'],
            nbrZombiesNextAttack = parseInt(document.querySelector(`#${getMyCityZoneId()} .square_container`).dataset.zombies),
            nbrMissingDefenses = nbrZombiesNextAttack - nbrCityDefenses;
        }
            
        if(nbrMissingDefenses > 0 || Number.isInteger(nbrMissingDefenses) === false) {
            document.querySelector("#poptasks .nbr_missing_defenses").innerHTML = nbrMissingDefenses;
            document.querySelector("#poptasks .nbr_zombies").innerHTML = nbrZombiesNextAttack;
        } else {
            // Mark the task as completed in the tasks list
            document.querySelector("#poptasks .nbr_missing_defenses").innerHTML = "des";
            document.querySelector("#task10 .icon").innerHTML = "&#x2705;";
            document.querySelector("#task10 .collapsible-body").innerHTML = "Vous avez terminé cette tâche !";
        }
    }
}
