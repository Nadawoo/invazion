/**
 * Various mandatory functions for loading the page.
 * If a function is not mandatory, put it in the "misc.func.js" file.
 */


/**
 * Show/hide the given element by clicking on a button
 * 
 * @param {string|array} elementsNames The list of HTML IDs or classes to show/hide.
 *                          Can be a string if only one ID or class to show/hide.
 *                          In both cases, don't forget the # or . before each ID or class name.
 */
function toggle(elementsNames) {
    
    if(typeof(elementsNames) === "object") {
        // If elementsNames is a list of IDs or classes, treat each one.
        for(let i=0; i<elementsNames.length; i++) {
            var occurrencies = document.querySelectorAll(elementsNames);
            // If the element is a class and not an ID, we need a loop to treat all the ocurrencies
            for(i=0;i<occurrencies.length;i++) {
                occurrencies[i].classList.toggle("hidden");
            }
        }
    }
    else {
        // If elementsNames is only one ID or class
        var occurrencies = document.querySelectorAll(elementsNames);
        // If the element is a class and not an ID, we need a loop to treat all the ocurrencies
        for(i=0;i<occurrencies.length;i++) {
            occurrencies[i].classList.toggle("hidden");
        }
    }
}


function display(elementsNames) {
    
    if(typeof(elementsNames) === "string") {
        document.querySelectorAll(elementsNames).forEach(element => element.classList.remove("hidden"));
    }
    else {
        for(i=0; i < elementsNames.length; i++){
            document.querySelectorAll(elementsNames[i]).forEach(element => element.classList.remove("hidden"));
        }
    }
}


function hide(elementsNames) {
    
    if(typeof(elementsNames) === "string") {
        document.querySelectorAll(elementsNames).forEach(element => element.classList.add("hidden"));
    }
    else {
        for(i=0; i < elementsNames.length; i++){
            document.querySelectorAll(elementsNames[i]).forEach(element => element.classList.add("hidden"));
        }
    }
}


/**
 * Update the value of a paramter in the URL (ex: ?tab=constructions)
 * 
 * @param {string} name  The name of the parameter
 * @param {string} value The new value of the parameter. Set it to NULL if you want 
 *                       to simply remove the parameter frome the URL
 * @returns {undefined}
 */
function updateUrlParam(name, value) {

    var search_params = new URLSearchParams(window.location.search);
    search_params.delete(name);
    if(value !== null) {
        search_params.append(name, value);
    }
    // Met l'url à jour avec le nouveau paramètre
    window.history.pushState('', 'Azimutant - En ville', '?'+search_params);
}


/**
 * 
 * Returns the number of the current cycle (day XX)
 * @returns {int}
 */
function getCurrentCycle() {
    
    return parseInt(document.querySelector("#current_day").innerHTML);
}


/**
 * Calculate the number of all the cities on the map
 */
async function nbrCitiesOnMap() {
    
    return (await _cities !== null) ? Object.keys(_cities).length : 0;
}


/**
 * Calculate the number of not discovered cities on the map
 */
async function nbrUndiscoveredCitiesOnMap() {
    
    let nbrUndiscoveredCities = 0;
    
    if(await _cities !== null) {
        Object.values(_cities).forEach((caracs) => {
            let zone = document.querySelector(`#zone${caracs.coord_x}_${caracs.coord_y} .square_container`);
            if(Number(zone.dataset.cyclelastvisit) === 0) {
                nbrUndiscoveredCities++;
            }
        });
    }
    
    return nbrUndiscoveredCities;
}


/**
 * Display the number of (not) discoverd cities on the map
 */
async function updateCitiesCounter() {
    
    let nbrTotalCities = await nbrCitiesOnMap(),
        nbrUndiscoveredCities = await nbrUndiscoveredCitiesOnMap();

    // Display the number of not discovered cities in the counter
    // at the bottom of the map
    let nbrDiscoveredCities = nbrTotalCities-nbrUndiscoveredCities;
    document.querySelector("#city_counter .number").innerText = `${nbrDiscoveredCities}/${nbrTotalCities}`;
}


/**
 * Display the number of zombie cores on the map
 */
async function updateZombieCoresCounter() {

    let nbrZombieCores = 0;
    
    if(await _cities !== null) {
        // Calculate the number of zombie cores on the map
        Object.values(_cities).forEach((caracs) => {
            // #228 is the ID of the type of building for the "zombie cores"
            if(Number(caracs.city_type_id) === 228) {
                nbrZombieCores++;
            }
        });
    }
    
    // Display the number of zombie cores in the counter
    // at the bottom of the map
    document.querySelector("#zombie_cores_counter .number").innerText = `${nbrZombieCores}/${nbrZombieCores}`;
}
