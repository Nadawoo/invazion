/**
 * Various mandatory functions for loading the page.
 * If a function is not mandatory, put it in the "misc.func.js" file.
 */


/**
 * 
 * Returns the number of the current cycle (day XX)
 * @returns {int}
 */
function getCurrentCycle() {
    
    return parseInt(document.querySelector("#current_day").innerHTML);
}
