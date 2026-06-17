/*
 * Minimal functions required to load the map.
 * Don't put functions not essential for loading the map here, put them 
 * in mapUse.func.js. This separation allows a faster loading of the page.
 * Don't put executable code in this file, only functions.
 */

import { ZombLib } from "./lib/ZombLib.js";
import { Items } from "./components/Items.js";
import {
    populateBuilderBlock,
    updateActionBlocks,
    updateBlockAction,
    updateDigButtons
    }
    from "./actionBlocks.func.js";
import { listenToRoads } from "./eventsMain.func.js";
import {
    centerMapOnMe,
    desactivateMapPathsView,
    getZonePositions,
    switchToMapView
    }
    from "./mapUse.func.js";
import {
    addItemsIconInZone,
    displayToast,
    toggleActionBlock,
    getMyZoneOnce
    }
    from "./misc.func.js";

/*
 * Get all the zones of the map by calling the Azimutant's API
 */
export async function getMapZonesOnce(mapId) {
    
    if(_jsonMap === null) {
        let cookies = new Cookies(),
            token = cookies.getCookie('token'),
            zombLib = new ZombLib(),
            json = await zombLib.callApi("GET", "maps", `action=get&map_id=${mapId}&token=${token}`);
    
        if(json.metas.error_code === "success") {
            _jsonMap = json.datas;
        } else {
            _jsonMap = false;
            displayToast(json.metas.error_message, json.metas.error_class);
        }
    }
    
    return _jsonMap;
}


/*
 * Get the cities of the map by calling the Azimutant's API
 */
export async function getMapCitiesOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_cities === null) {
        let zombLib = new ZombLib();
        let json = await zombLib.callApi("GET", "cities", `action=get&map_id=${mapId}`);    
        _cities = json.datas;
    }
    
    return _cities;
}


/*
 * Get the citizens of the map by calling the Azimutant's API
 */
export async function getMapCitizensOnce(mapId) {
    
    // If the API has already be called before, don't call it again
    if(_citizens === null) {
        let zombLib = new ZombLib()
        let json = await zombLib.callApi("GET", "citizens", `action=get&map_id=${mapId}`);    
        _citizens = json.datas;
    }
    
    return _citizens;
}


export async function getMapRoadsOnce(mapId) {
    
    // If the API has already be called before, don't re-call it
    if(_roads === null) {
        let zombLib = new ZombLib();
        let json = await zombLib.callApi("GET", "connections", `action=get&map_id=${mapId}`);  
        _roads = json.datas.roads;
    }
    
    return _roads;
}


/**
 * Place the cities and buildings on the map. They are not loaded by the PHP 
 * to speed up the loading of the map.
 * 
 * @param {int} mapId
 * @param {string} htmlCoords Give coordinates if you want to place only the cities  
 *                            located in a specific zone. Example: "5_7" will only 
 *                            place the city located in the zone X=5 and Y=7.
 *                            If not set, all the cities of the map will be placed.
 */
export async function addCitiesOnMap(mapId, htmlCoords=null) {
    
    // #233 is the ID for the "Undiscovered building" in the API.
    let undiscoveredBuildingId = 233;
    let citiesToPlaceCaracs = null;
    
    // Get the cities of the map by calling the Azimutant's API
    _cities = await getMapCitiesOnce(mapId);
    
    // Filter the cities if we want to place only on a given zone
    if(htmlCoords !== null) {
        let [coordX, coordY] = htmlCoords.split("_").map(Number);
        citiesToPlaceCaracs = filterCitiesByCoords(coordX, coordY);
    } else {
        citiesToPlaceCaracs = _cities;
    }
    
    // Place the cities on the appropriate zones
    for(let cityId in citiesToPlaceCaracs) {
        
        let city = citiesToPlaceCaracs[cityId],
            htmlCoords = city.coord_x+"_"+city.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
            
        let buildingCarcs = _configsBuildings[city.city_type_id],
            buildingIconHtml = buildingCarcs["icon_html"],
            buildingIconPath = "resources/img/"+buildingCarcs["icon_path"],
            buildingIconWidth = Math.round(buildingCarcs["icon_size_ratio"] * 28),
            buildingEmojiSize = (buildingCarcs["icon_size_ratio"] * 1.5).toFixed(2),
            buildingName = buildingCarcs["name"];
            
        // If the city is already placed in the zone, don't add it twice
        if(zone.dataset.citytypeid != "") {
            displayToast(`[Bug] Impossible de placer la ville #${cityId} sur 
                          une zone déjà construite [${city.coord_x}:${city.coord_y}]`,
                         "critical");
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
            zone.insertAdjacentHTML("afterbegin", `<button aria-label="Bâtiment : ${buildingName}" class="icon_html" style="font-size:${buildingEmojiSize}em;" data-action="openBuildingPopup"><span role="img">${buildingIconHtml}</span></button>`);
            // Delete the "&nbsp;" required on the empty zones 
//            if(zone.querySelector(".empty") !== null) {
//                zone.querySelector(".empty").remove();
//            }
        }
        
        // Adds the name of the building
        let cityName = (city["city_name"] === null) ? buildingName : city["city_name"];
        zone.insertAdjacentHTML("afterbegin", `<span class="hidden city_name animate__animated animate__zoomIn">${cityName}</span>`);
        
        // Make the building's zone always visible, even when never visited
        zone.closest(".hexagon").style.opacity = 1;
        
        // The building is displayed differently depending on whether its zone 
        // has been discovered or not.
        if(city.city_type_id === undiscoveredBuildingId) {
            zone.closest(".hexagon").style.background = "none";
        } else {
            zone.closest(".hexagon").classList.add("elevate");
        }
        
        zone.dataset.cityid = cityId;
        // Used to memorize the type of building in HTML
        // TODO: we could remove this attribute by using the attribute data-cityid
        zone.dataset.citytypeid = city.city_type_id;
        
        // Will highlight the road leading to the city when hovering it
        listenToRoads(zone.closest(".hexagon"));
    }
    
    return _cities;
}


/**
 * Displays on the map the wanted item. Useful to show where are important items.
 * 
 * @param {int} itemId The ID of the item, as returned by the "items" API
 * @returns {undefined}
 */
export async function displayItemOnMap(itemId) {
    
    let items = new Items();
    let itemCoords = getItemCoords(itemId);
    
    for(let coords of Object.values(await itemCoords)) {
        let itemIcon = items.icon(itemId, 38);
        document.querySelector(`#map_body #zone${coords} .square_container`).insertAdjacentHTML('afterBegin', `<div style="position:absolute;top:-0.1rem;font-size:1.9em;">${itemIcon}</div>`);
    }
}


/**
 * Zooms in or out of the map with an HTML <range> tag
 * 
 * @param {int} newZoomPercent
 */
export function zoomMapRange(newZoomPercent) {
    
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
export async function getItemCoords(itemId) {
    
    _jsonMap = await getMapZonesOnce(mapId);    
    let itemCoords = [];
    
    // If we try to get the items giving action points (water, food...),
    // the parameter is a string and not an item ID
    if(Number.isInteger(itemId) === false) {
        let itemType = itemId;
        let itemsIds = getItemsIdsByType(itemType);
        
        for(let zone of Object.entries(_jsonMap.zones)) {
            // If the item ID is in the zone, memorize its coordinates
            if(zone[1].items !== null && itemsIds.some(element => Object.keys(zone[1].items).includes(element))) {
                itemCoords.push(zone[0]);
            }
        }
    } else {
        // If we try to get only one specific item ID
        for(let zone of Object.entries(_jsonMap.zones)) {
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
export function switchToActionView() {
    
    const mapId = Number(document.querySelector("#mapId").innerHTML);
    const zoneData = document.querySelector("#me").parentNode.dataset;
    
    // Zoom the map on the player
    zoomMapRange(500);
    setTimeout(() => centerMapOnMe(10), 2000);
    
    // Display the action blocks (move, dig...)
    display(["#actions_panel", "#personal_block_wrapper"]);
    
    // If we switch to the action mode for the first time
    if(document.querySelector("#actions").innerHTML === "") {
        // Populate the HTML for the action block (move, dig...)
        let tplActions = document.querySelector("#tplActions").content.cloneNode(true);
        document.querySelector("#actions").appendChild(tplActions);
        // Add the list of buildable buildings
        populateBuilderBlock();
        // Add an icon on the zone to show there are items here
        addItemsIconInZone(zoneData.coordx, zoneData.coordy, zoneData.items);
        // Load event listener to go back to the map
        document.querySelector("#map_mode_button").addEventListener("click",
            switchToMapView
        );
        
        // Listen to the collapsible elements
        const elems = document.querySelectorAll("#block_build .collapsible");
        const instances = M.Collapsible.init(elems);
    }    
    
    // Activate the "Move" tab action
    setTimeout(async () => {
        document.querySelector("#round_move").classList.add("active"); 
        toggleActionBlock('move');
        updateBlockAction('move');
        // Hide the card for digging if the zone is not diggable
        _myZone = await getMyZoneOnce(mapId, zoneData.coordx, zoneData.coordy);
        updateDigButtons(_myZone.user_specific.is_visited_today); 
    }, 1000);    
    
    desactivateMapPathsView();
    // Display the button which switches to the Map mode
    display("#map_mode_button");
    // Remove the illumination on the button which displays the map navigation
    document.querySelector("#views_bar .map").classList.remove("active");     
    document.querySelector("#views_bar .paths").classList.remove("active");
    // NB: See the CSS file to see the modifications implied by adding this class
    document.querySelector("#map").classList.add("action_view");
    
    addCitizensOnMyZone();
    
    updateActionBlocks();
    
    window.isActionViewActive = true;
}


/**
 * Add icons on the current player's zone for the other citizens here.
 * This functon doesn't populate the list in the "Citizens" action block
 * 
 * @returns {undefined}
 */
async function addCitizensOnMyZone() {
    
    const myCitizenId = Number(document.querySelector("#citizenId").innerHTML);
    const myZone = document.querySelector("#me").parentNode.dataset;
    const coordX = myZone.coordx;
    const coordY = myZone.coordy;
    
    // Get the citizens of the map by calling the Azimutant's API
    _citizens = await getMapCitizensOnce(mapId);    
    // Keep only the citizens who are in the player's zone
    const citizensInMyZone = Object.values(_citizens).filter(citizen => citizen.coord_x == coordX 
                                                                        && citizen.coord_y == coordY
                                                                        && citizen.citizen_id != myCitizenId);
    
    // Add on the zone the silhouettes of the fellows
    const citizensContainer = document.createElement("button");
    citizensContainer.classList.add("fellows");
    citizensContainer.dataset.action = "switchActionBlock";
    citizensContainer.dataset.name = "citizens";

    for(const i in citizensInMyZone) {
        const citizenIcon = document.createElement("img");
        citizenIcon.src = "resources/img/free/human.png";
        citizenIcon.height = 8;
        citizenIcon.width  = 5;
        citizensContainer.appendChild(citizenIcon);

        if(i >= 2) break;
    }
    
    document.querySelector(`#zone${coordX}_${coordY} .square_container`).appendChild(citizensContainer);
}


/**
 * Display/hide the tooltip over a zone of the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
export function triggerTooltip(hexagon) { 
    
    if(hexagon !== null
        && (hexagon.querySelector(".bubble") === null || hexagon.querySelector(".bubble").classList.contains("block") === false)
        ) {
        
        // Initialize the tooltip for the first time
        if(hexagon.querySelector(".bubble") === null) {
            // Writes the HTML of the tooltip in the DOM
            let template = document.querySelector("#tplTooltip").content.cloneNode(true);
            hexagon.querySelector(".square_container").append(template);
            // Will hide the tooltip when the mouse leaves the hexagon
            hexagon.addEventListener("mouseleave", function() {
                    let tooltip = new Tooltip();
                    tooltip.hide(hexagon);
                },
                { passive: true }
            );
        }
        
        // Display the hidden tooltip
        let tooltip = new Tooltip();
        tooltip.display(hexagon);
    }
}


/**
 * Add light halos on the map around the cities
 */
export function updateLightHalos() {
    
    const mask = document.querySelector("#lightMask");

    // Remove the former light halos
    mask.querySelectorAll("circle").forEach(c => c.remove());

    const zones = document.querySelectorAll("#map_body .cityframe, #map_body .map_citizen");
    const namespace = "http://www.w3.org/2000/svg";
    
    zones.forEach(zone => {
        // Get the pixel coordinates of the zone
        let hexagon = zone.closest(".hexagon");
        let positions = getZonePositions(`#${hexagon.id}`);

        let {x, y} = positions;
        let circle = document.createElementNS(namespace, 'circle');

        circle.setAttribute('cx', x);
        circle.setAttribute('cy', y);
        circle.setAttribute('r', 65);
        circle.setAttribute('fill', 'url(#penumbra)');
        mask.appendChild(circle);
    });
}


/**
 * Add the name of the map at the top of the map
 * 
 * @returns {undefined}
 */
export async function populateMapTitle(mapId) {
    
    const mapName = (await _jsonMap).map_name ?? "";
    
    document.querySelector("#map_title").innerText = `Carte ${mapId}. ${mapName}`;
}


export function isActionViewActive() {
    
    let result = false;
    let mapNode = document.querySelector("#map");
    
    if(mapNode) {
        result = mapNode.classList.contains("action_view");
    }
    
    return result;
}
