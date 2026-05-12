import { ZombLib } from "../lib/ZombLib.js";


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
    
    const zombLib = new ZombLib;
    const json = zombLib.callApi("GET", "games", `action=get`);
    
    json.then((result) => {
        const gameSelector = new GameSelector;
        gameSelector.populateGamesList(result.datas);
    });
}
