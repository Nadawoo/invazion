import { ZombLib } from "../lib/ZombLib.js";
import { displayToast, getMe } from "../misc.func.js";


/**
 * Function for loading the page with the JS router
 * 
 * @returns {unresolved}
 */
export async function gamesScreen() {
    
    const response = await fetch("/screens/games.php");
    return await response.text();
}


export function initGamesPage() {
    
    populateGamesList();
}


async function getMyCurrentGameId() {
    
    let myCurrentGameId = null;
    
    const jsonMe = await getMe();
    
    if(jsonMe.metas.error_code !== "success") {
        displayToast(jsonMe.metas.error_message, jsonMe.metas.error_class);
    }
    else {
        myCurrentGameId = jsonMe.datas.map_id;
        
        
//        const gameSelector = new GameSelector;
//        gameSelector.populateMyGamesList(result.datas);
//        
//        console.log(citizenMapId);
    }
    
    return myCurrentGameId;
}


async function populateGamesList() {
    
    const zombLib = new ZombLib;
    const json = zombLib.callApi("GET", "games", `action=get`);
    
    const myCurrentGameId = await getMyCurrentGameId();
    
    json.then((result) => {
        const gameSelector = new GameSelector;
        gameSelector.populateAllGamesList(result.datas);
        
        if(myCurrentGameId !== null) {
            display("#myGames");
            gameSelector.populateMyGamesList(result.datas, myCurrentGameId);
        }
    });
}
