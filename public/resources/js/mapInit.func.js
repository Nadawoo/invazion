/*
 * Minimal functions required to load the map.
 * Don't put functions not essential for loading the map here, put them 
 * in mapUse.func.js. This separation allows a faster loading of the page.
 * Don't put executable code in this file, only functions.
 */


/*
 * Get all the zones of the map by calling the Azimutant's API
 */
async function getMapZonesOnce(mapId) {
    
    if(_jsonMap === null) {
        let json = await callApi("GET", "maps", "action=get&map_id="+mapId);
        _jsonMap = json.datas.zones;
    }
    
    return _jsonMap;
}


/*
 * Get the cities of the map by calling the Azimutant's API
 */
async function getMapCitiesOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_cities === null) {
        let json = await callApi("GET", "cities", `action=get&map_id=${mapId}`);    
        _cities = json.datas;
    }
    
    return _cities;
}


/*
 * Get the citizens of the map by calling the Azimutant's API
 */
async function getMapCitizensOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_citizens === null) {
        let json = await callApi("GET", "citizens", `action=get&map_id=${mapId}`);    
        _citizens = json.datas;
    }
    
    return _citizens;
}


/**
 * Place the cities and buildings on the map. They are not loaded by the PHP 
 * to speed up the loading of the map.
 * 
 * @param {int} mapId
 */
async function addCitiesOnMap(mapId) {
    
    // #233 is the ID for the "Undiscovered building" in the API.
    let undiscoveredBuildingId = 233;
    
    // Get the cities of the map by calling the Azimutant's API
    _cities = await getMapCitiesOnce(mapId);
    
    // Place the cities on the appropriate zones
    for(let cityId in _cities) {
        
        let city = _cities[cityId],
            htmlCoords = city.coord_x+"_"+city.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container"),
            nbrItems = zone.dataset.items;
        
        let zones = await _jsonMap;

        let buildingCarcs = _configsBuildings[city.city_type_id],
            buildingIconHtml = buildingCarcs["icon_html"],
            buildingIconPath = "resources/img/"+buildingCarcs["icon_path"],
            buildingIconWidth = Math.round(buildingCarcs["icon_size_ratio"] * 32),
            buildingEmojiSize = Math.round(buildingCarcs["icon_size_ratio"] * 1.5),
            buildingName = buildingCarcs["name"];
        
        // If the city is already placed in the zone, don't add it twice
        if(zone.dataset.citytypeid != "") {
            continue;
        }
        
        if(buildingCarcs["icon_path"] !== null && zone.dataset.citytypeid == "") {
            if(buildingCarcs["is_icon_tiled"] === 1) {
                // Displays the building with the tile included in its image
                zone.closest(".hexagon").style.backgroundImage = `url(${buildingIconPath})`;                 
            } else {
                // Displays the image (PNG) of the building (without tile)
                zone.insertAdjacentHTML("afterbegin", `<img class="city_img" src="${buildingIconPath}" alt="${buildingIconHtml}" width="${buildingIconWidth}">`);
                // Delete the "&nbsp;" required on the empty zones 
//                if(zone.querySelector(".empty") !== null) {
//                    zone.querySelector(".empty").remove();
//                }
            }
        }
        else {
            // If no image file for this building, displays an emoji for the building
            zone.insertAdjacentHTML("afterbegin", `<div class="icon_html" style="font-size:${buildingEmojiSize}em;">${buildingIconHtml}</div>`);
            // Delete the "&nbsp;" required on the empty zones 
//            if(zone.querySelector(".empty") !== null) {
//                zone.querySelector(".empty").remove();
//            }
        }
        
        // Adds the number of zombies of the daily attack 
        // (#230 = ID of the "zombie base" building)
        if(city.city_type_id === 230) {
            zone.insertAdjacentHTML("afterbegin", `<span class="nbr_defenses" style="background:red">${zone.dataset.zombies} <img src="resources/img/motiontwin/zombie.gif" alt="&#x1F9DF;"></span>`);
        }        
        // Adds the number of defenses above each city
        // (#12 = ID of the "human city" building)
        else if(city.city_type_id === 12) {
            let nbrDefenses = city.total_defenses,
                nbrZombiesNextAttack = zone.dataset.zombies,
                defensesExcedent = nbrDefenses - nbrZombiesNextAttack;
            
            // Display the label above the map only if the city has at least one defense
            if(nbrDefenses > 0) {
                if(defensesExcedent >= 0) {
                    htmlNbrDefenses = `<span class="nbr_defenses safe">&#x2705;</span>`;
                } else {
                    htmlNbrDefenses = `<span class="nbr_defenses" style="background:darkred">&nbsp; ${defensesExcedent}&#128737;&#65039;</span>`;
                }
                zone.insertAdjacentHTML("afterbegin", `${htmlNbrDefenses}`);
            }
        }
        // Adds the number of items remaining inside the explorable building
        else if(city.city_type_id !== "undefined") {
            
            let html = "";
//            if(city.city_type_id === undiscoveredBuildingId) {
//                html = "";
//            }
            if(parseInt(zone.dataset.cyclelastvisit) < getCurrentCycle()) {
                html = `<span class="nbr_defenses diggable pulse">&#x26CF;&#xFE0F;</span>`;
            }
            else if(nbrItems > 0) {
                html = `<span class="nbr_defenses diggable" style="background:royalblue">${nbrItems}</span>`;
            }
            else {
//                let maxExplorations = 100;
//                // NB: #108 = ID of the item "Counter of explorations"
//                let nbrExplorationsDone = zones[htmlCoords]['items'][108] || 0;
//                let nbrExplorationsRemaining = maxExplorations-nbrExplorationsDone;
                html = `<span class="nbr_defenses safe">&#x2705;</span>`;
            }
            
            zone.insertAdjacentHTML("afterbegin", html);
        }
        // Adds the name of the building
        cityName = (city["city_name"] === null) ? buildingName : city["city_name"];
        zone.insertAdjacentHTML("afterbegin", `<span class="hidden city_name animate__animated animate__zoomIn">${cityName}</span>`);
        
        // Make the building's zone always visible, even when never visited
        zone.closest(".hexagon").style.opacity = 1;
        
        // The building is displayed differently depending on whether its zone 
        // has been discovered or not.
        if(city.city_type_id === undiscoveredBuildingId) {
            zone.closest(".hexagon").style.background = "none";
        } else {
            zone.closest(".hexagon").classList.add("ground_city", "elevate");
        }
        
        zone.dataset.cityid = cityId;
        // Used to memorize the type of building in HTML
        // TODO: we could remove this attribute by using the attribute data-cityid
        zone.dataset.citytypeid = city.city_type_id;
    }
    
    return _cities;
}


/**
 * Place the citizens on the map. They are not loaded by the PHP to speed up
 * the loading of the map.
 * 
 * @param {int} mapId
 */
async function addCitizensOnMap(mapId) {
    
    // Get the citizens of the map by calling the Azimutant's API
    _citizens = await getMapCitizensOnce(mapId);
    
    // Place the citizens on the appropriate zones
    for(let citizenId in _citizens) {
        let citizen = _citizens[citizenId],
            htmlCoords = citizen.coord_x+"_"+citizen.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
        
        // Don't add the citizen if an other citizen is already placed in the zone
        if(zone.querySelector(".map_citizen") === null && zone.dataset.zombies < 1 && zone.dataset.cityid < 1) {
            
            let nbrCitizens = zone.dataset.citizens;
            let label = (nbrCitizens > 1) ? "[Groupe]" : citizen.citizen_pseudo;
            
            zone.insertAdjacentHTML("afterbegin", 
                `<div class="map_citizen">
                    <span class="nbr_defenses">${label}</span>
                    ${htmlCitizensImages(nbrCitizens)}
                </div>
                <div class="halo inactive">&nbsp;</div>
                <div class="overlay hidden"></div>
                `);
            
            // Delete the "&nbsp;" required on the empty zones 
//            if(zone.querySelector(".empty") !== null) {
//                zone.querySelector(".empty").remove();
//            }
        }
    }
    
    return _citizens;
}


/**
 * Displays on the map the wanted item. Useful to show where are important items.
 * 
 * @param {int} itemId The ID of the item, as returned by the "items" API
 * @returns {undefined}
 */
async function displayItemOnMap(itemId) {
    
    let itemCoords = getItemCoords(itemId);
    
    for(let coords of Object.values(await itemCoords)) {
        let img = image(itemId, 38);
        document.querySelector(`#map #zone${coords} .square_container`).insertAdjacentHTML('afterBegin', `<div style="position:absolute;top:-0.1rem;font-size:1.9em;">${img}</div>`);
    }
}


/**
 * Zooms in or out of the map with an HTML <range> tag
 * 
 * @param {int} newZoomPercent
 */
function zoomMapRange(newZoomPercent) {
    
    document.querySelector("#map_body").classList.add("zoomedIn");
    document.querySelector("#map_body").style.transform = `scale(${newZoomPercent}%)`;
    // Important: force ScroolBooster to recalculate the size of the map after zooming.
    // Without this, impossible to drag the map after zooming.
    _scrollBoosterInstance.updateMetrics();
//    setTimeout(centerMapOnMe, 300);
    
    window.currentZoomPercent = newZoomPercent;
}


/**
 * Get the coordinates of all the zones containing an exemplary of the given item
 * 
 * @param {int|string} itemId The ID of the item your are looking for.
 *                            Set it to "boost" to get the coordinates of the zones 
 *                            containg items giving action points (water, food...)
 * @returns {array} The coordinates of the zones containing the item
 *                  Exemple: [0_3, 4_1, 5_8, ...]
 */
async function getItemCoords(itemId) {
    
    _jsonMap = await getMapZonesOnce(mapId);    
    let itemCoords = [];
    
    // If we try to get the items giving action points (water, food...),
    // the parameter is a string and not an item ID
    if(Number.isInteger(itemId) === false) {
        let itemType = itemId;
        let itemsIds = getItemsIdsByType(itemType);
        
        for(let zone of Object.entries(_jsonMap)) {
            // If the item ID is in the zone, memorize its coordinates
            if(zone[1].items !== null && itemsIds.some(element => Object.keys(zone[1].items).includes(element))) {
                itemCoords.push(zone[0]);
            }
        }
    } else {
        // If we try to get only one specific item ID
        for(let zone of Object.entries(_jsonMap)) {
            // If the item ID is in the zone, memorize its coordinates
            if(zone[1].items !== null && itemId in zone[1].items) {
                itemCoords.push(zone[0]);
            }
        }
    }
    
    return itemCoords;
}


/**
 * Get the list of the items that give action points to the player
 * (food, water, drug...)
 * @returns {Array}
 */
function getItemsIdsByType(itemType) {
    // These values must exist in the the "item_type" field in the Azimutant's 
    // items API
    let apiItemsTypes = [];
    if(itemType === "boost") {
        apiItemsTypes = ["food", "water"];
    } else if(itemType === "resource") {
        apiItemsTypes = ["resource", "resource_rare"];
    } else {
        console.log(`[Azimutant error] Unknown parameter value for \"itemType\"`
                    +`in getItemsIdsByType()`);
        return false;
    }
    
    let itemsIds = [];
    for(let itemCaracs of Object.entries(_configsItems)) {
        if(itemCaracs[1]["item_type"] !== null
            && apiItemsTypes.some(element => itemCaracs[1]["item_type"].includes(element))
            ) {
            itemsIds.push(itemCaracs[0]);
        }
    }
    
    return itemsIds;
}


/*
 * Zoom on the player to show the "action" buttons
 */
function switchToActionView() {
    
    // Zoom the map on the player
    zoomMapRange(220);
    setTimeout(() => centerMapOnMe(), 2000);
    
    // Display the action blocks (move, dig...)
    display(["#actions_panel", "#personal_block_wrapper"]);
    
    // If we switch to the action mode for the first time
    if(document.querySelector("#actions").innerHTML === "") {
        // Populate the HTML for the action block (move, dig...)
        let tplActions = document.querySelector("#tplActions").content.cloneNode(true);
        document.querySelector("#actions").appendChild(tplActions);
        // Load the event listeners for move, dig, etc
        listenToActionModeActions();
        // Load event listener to go back to the map
        document.querySelector("#map_mode_button").addEventListener("click",
            switchToMapView
        );
    }    
    
    // Hide some elements of the GUI to make the interface look lighter
    hide(["#views_bar", "#map_navigation", "#tasks_button", "#game_footer"]);
    hide([".bubble"]);
    desactivateMapPathsView();
    // Display the button which switches to the Map mode
    display("#map_mode_button");
    // Remove the illumination on the button which displays the map navigation
    document.querySelector("#views_bar .map").classList.remove("active");     
    document.querySelector("#views_bar .paths").classList.remove("active");
    
    document.querySelector("#map").classList.add("action_view");
    
    updateActionBlocks();
    
    window.isActionViewActive = true;
}


/**
 * Display/hide the tooltip over a zone of the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
function triggerTooltip(hexagon) { 
    
    if(hexagon !== null
        && (hexagon.querySelector(".bubble") === null || hexagon.querySelector(".bubble").classList.contains("block") === false)
        ) {
        
        // Initialize the tooltip for the first time
        if(hexagon.querySelector(".bubble") === null) {
            // Writes the HTML of the tooltip in the DOM
            let template = document.querySelector("#tplTooltip").content.cloneNode(true);
            hexagon.querySelector(".square_container").append(template);
            // Will hide the tooltip when the mouse leaves the hexagon
            hexagon.addEventListener("mouseleave",
                                    ()=>hideTooltip(hexagon),
                                    { passive: true }
            );
        }
        
        // Display the hidden tooltip
        displayTooltip(hexagon);
    }
}
