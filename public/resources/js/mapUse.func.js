/* 
 * Functions concerning the map of the game and not essential for loading the page.
 * The minimal functions required for loading the map are stored in mapInit.func.js.
 * Don't put executable code in this file, only functions.
 */


/**
 * Refreshes the HTML of the concerned zones when a player moves (server-sent events)
 * param {array} event The event given by an EventSource() object
 *                     Doc : https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
async function updateMapRealtime(event, timestamp) {
    let citizenPseudo = document.getElementById("citizenPseudo").innerHTML,
        citizenId     = document.getElementById("citizenId").innerHTML,
        mapId         = document.getElementById("mapId").innerHTML;

    // If event notified, get the new HTML contents for the modified zones
    let options = { method: "GET",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    };
    let htmlZones = await fetch("/generators/zone.php?map_id="+mapId+"&newerthan="+timestamp+"&citizen_id="+citizenId+"&citizen_pseudo="+citizenPseudo, options).then(toJson);
    // Get the citizens in the zone (not included in the main zone datas)
    // TODO: don't call this when not needed:
    //      => needed when a citizen moves to another zone
    //      => NOT needed when a citizen kills a zombie
    let json = await callApi("GET", "citizens", `action=get&map_id=${mapId}`);   
    _citizens = json.datas;
    
    // Updates the HTML for the modified zones
    for (let coords in htmlZones) {
        document.getElementById("zone"+coords).outerHTML = htmlZones[coords];
    }
    
    addCitiesOnMap(mapId);
    
    // Place the player on his new zone
    addMeOnMap();
    
    // Get informations about the current zone through the "data-*" HTML attributes
    let zoneData = document.querySelector("#me").parentNode.dataset;
    // Display an alert over the movement paddle if the player is blocked
    updateBlockAlertControl(zoneData.controlpointszombies, mapId, zoneData.coordx, zoneData.coordy);  
    
    // Refresh the timestamp to memorize that these actions have been treated
    return timestamp = await JSON.parse(event.data).zones;
}


/**
 * Replaces the buildings IDs on the map by the real data (building name, description...)
 * Useful to load those data from the configs stored in JSON in the HTML page,
 * without calling the "configs" API
 */
function replaceBuildingsPlaceholders() {
    
    // Gets all the placeholders on the map
    var buildingIds = document.querySelectorAll("#map .buildingId");
    
    for(let building of buildingIds) {
        // Warning: the class of the parent tag must be named as the field is
        // in the "configs" API. Example for the building #17: class="name" if we want 
        // to get configs["buildings"][17]["name"]
        let field = building.parentNode.className;        
        let buildingId = building.innerHTML;
        
        // Special treatment to display the icon of the building
        if(field === "icon_placeholder") {
            let icon_html = _configsBuildings[buildingId]["icon_html"];
            let icon_path = "resources/img/"+_configsBuildings[buildingId]["icon_path"];
            
            if(_configsBuildings[buildingId]["icon_path"] !== null) {
                if(_configsBuildings[buildingId]["is_icon_tiled"] === 1) {
                    // Displays the building with the tile included in the image
                    building.closest(".hexagon").style.backgroundImage = `url(${icon_path})`;
                    // Erases the placeholder (ID) of the building
                    building.outerHTML = "&nbsp;";
                } else {
                    // Displays the image (PNG) of the building (without tile)
                    building.outerHTML = `<img src="${icon_path}" alt="${icon_html}" width="24" height="24">`;
                }
            }
            else {
                // If no image file for this building, displays an emoji for the building
                building.outerHTML = `<div class="icon_html">${icon_html}</div>`;
            }
        }
        else {
            // Replaces the building ID placeholder by the data of the field
            building.outerHTML = _configsBuildings[buildingId][field];
        } 
    }
}


/**
 * Adds the connected player on the appropriate zone of the map
 */
function addMeOnMap() {
    
    let myCoordX = document.querySelector("#citizenCoordX").innerHTML,
        myCoordY = document.querySelector("#citizenCoordY").innerHTML,    
        myPseudo = document.querySelector("#citizenPseudo").innerHTML,
        myZone = document.querySelector(`#zone${myCoordX}_${myCoordY} .square_container`);

    // If there is no other citizen in the zone
    if(myZone.querySelector(".map_citizen") === null) {
        myZone.insertAdjacentHTML("afterbegin", 
            `<div class="map_citizen" id="me">
                <span class="nbr_defenses">${myPseudo}</span>
                <img src="resources/img/free/human.png">
                <img id="explosionMe" class="scale-transition scale-out" src="resources/img/thirdparty/notoemoji/collision-512.webp" width="38">
            </div>
            <div class="halo">&nbsp;</div>
            <div class="overlay"></div>
            `);
    } else {
        myZone.querySelector(".map_citizen").id = "me";
        myZone.querySelector(".halo").classList.remove("inactive");
        myZone.querySelector(".overlay").classList.remove("hidden");
    }
    
    // Event listener when clicking on the player on his map zone
    listenToMeOnMap();
}


/**
 * Generates the HTML images for X citizens in a zone of a map
 * with natural positions (not just in one line) and avoids overlaps.
 * 
 * @param {int} nbrCitizens
 * @returns {String} HTML
 */
function htmlCitizensImages(nbrCitizens) {
    
    // Set of predefined positions for the images of the citizens 
    // in a zone, to avoid overlaps.
    // - First key = the number of citizens in the zone
    // - Subkeys = citizen 1, citizen 2, citizen 3...
    var positions = {
        1: { 1:{"top":"-2.1em", "left":"inherit"} },
        2: { 1:{"top":"-2.3em", "left":"0.3em"},
             2:{"top":"-1.8em", "left":"1em"}
            },
        3: { 1:{"top":"-2.5em", "left":"0.3em"},
             2:{"top":"-2.3em", "left":"1.4em"},
             3:{"top":"-1.8em", "left":"0.9em"}
            },
        4: { 1:{"top":"-2.7em", "left":"0.3em"},
             2:{"top":"-2.3em", "left":"1.4em"},
             3:{"top":"-1.8em", "left":"0.3em"},
             4:{"top":"-1.7em", "left":"1.3em"}
            },
        5: { 1:{"top":"-2.7em", "left":"0.3em"},
             2:{"top":"-2.4em", "left":"0.7em"},
             3:{"top":"-2.7em", "left":"1.6em"},
             4:{"top":"-1.8em", "left":"0.3em"},
             5:{"top":"-1.7em", "left":"1.3em"}
            },
        };
        
    // If there are too many citziens for the cases set, assume that
    // we use the maximal number of citizens.
    let maxCitizens = Object.keys(positions).length;
    nbrCitizens = (nbrCitizens > maxCitizens) ? maxCitizens : nbrCitizens;
    
    var content = "";
    for(let i=0; i<nbrCitizens; i++) {
        let top  = positions[nbrCitizens][i+1]["top"],
            left = positions[nbrCitizens][i+1]["left"];
        content += `<img src="/resources/img/free/human.png" height="48"
                     style="position:absolute;top:${top};left:${left}">`;
    }
    
    return content;
}


/**
 * Returns the HTML ID of the zone where the player's city is.
 * 
 * @returns {string} The ID of the zone, e.g. "zone10_8"
 */
function getMyCityZoneId() {

    let myCityId = document.querySelector("#gameData #cityId").innerHTML, 
        result = null;
    
    if(myCityId !== "") {
        let myCityNode = document.querySelector(`[data-cityid="${myCityId}"]`);
        result = myCityNode.parentNode.id;
    }
    
    return result; 
}
    
    
/**
 * Gets the X and Y position of a zone on the screen (e.g. 297, 153).
 * Useful to draw an SVG line between two zones.
 * Not to be confused with the coordinates shown on the map (e.g. [7:12])
 * 
 * @param {string} zoneHtmlId The HTML ID of the zone whose coordinates you want.
 *                            Don't forget the hashtag (e.g. "#zone5_11")
 * @returns {dict} zonePositions The X and Y position of the zone
 */
function getZonePositions(zoneHtmlId) {
    
    let mapElement = document.querySelector("#map_body"),
        zoneElement = document.querySelector(`${zoneHtmlId} .square_container`);
    // Get bounding rectangles
    let mapRect = mapElement.getBoundingClientRect(),
        zoneRect = zoneElement.getBoundingClientRect();
    // Calculate the zoom factor caused by an eventual CSS transform:scale()
    let scaleX = zoneRect.width / zoneElement.offsetWidth,
        scaleY = zoneRect.height / zoneElement.offsetHeight;
    
    let zonePositions = {
        // "- mapRect.x" => substracts the space between the top of the window and
        // the top of the map
        // "+ zoneRect.width/2" => places the point at the center of the zone
        // "/ scaleX" => cancels the eventual zoom caused by a CSS transform:scale(),
        // otherwise the coordinates could appear shifted.
        "x" : Math.round((zoneRect.x - mapRect.x + zoneRect.width/2) / scaleX),
        "y" : Math.round((zoneRect.y - mapRect.y + zoneRect.height/2) / scaleY)
        };
       
    return zonePositions;
}


/**
 * Displays colors above map zones to show the repartition of the zombies
 * 
 * @returns {undefined}
 */
function activateMapZombiesView() {
    
    // Get only the hexagons wich contain zombies for better performance
    // (not all the hexagons of the map) for better performance
    let hexagons = document.querySelectorAll("#map_body .hexagon .zombies");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].closest(".hexagon").querySelector(".square_container");
        let zombiesAmount = squareContainer.querySelector(".zombies_amount");
        let controlpoints_zombies = parseInt(squareContainer.dataset.controlpointszombies);
        let controlpoints_one_citizen = 2;
        
        if(zombiesAmount === null) {
            squareContainer.insertAdjacentHTML("beforeend", '<span class="zombies_amount"></span>');
            zombiesAmount = squareContainer.querySelector(".zombies_amount");
        }
        
        let color = "";
        if(controlpoints_zombies > controlpoints_one_citizen*3) {
            color = 'darkred';//'#ff0505'; // Needs 4 citizens (8 CP) or more
        } else if(controlpoints_zombies > controlpoints_one_citizen*2) {
            color = 'red';// '#ff5b00'; // Safe with 3 citizens (6 CP)
        } else if(controlpoints_zombies > controlpoints_one_citizen*1) {
            color = 'orange';//'#fb8c00'; // Safe with 2 citizens (4 CP)
        } else if(controlpoints_zombies > 0) {
            color = 'green'; //'#d4ac0d'; Safe with 1 citizen (2 CP)
        } else if(controlpoints_zombies === 0) {
//            color = 'green';  // No zombies
        } 
        
        // Color the zones depending on the number of zombies
        zombiesAmount.style.background = color;
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        if(parseInt(squareContainer.dataset.zombies) !== 0) {
            zombiesAmount.innerHTML = squareContainer.dataset.zombies;
        }
    }
    
    hide(["#views_bar, #attack_bar", "#tasks_button"]);
    hide([".nbr_defenses"]);
    display("#map_legend_zombies");
    display(".zombies_amount");
}


function desactivateMapZombiesView() {
    
    hide("#map_legend_zombies");
    hide(".zombies_amount");
}


function toggleMapZombiesView() {
    
    if (window.isMapZombiesViewActive === true) {   
        desactivateMapZombiesView();
        window.isMapZombiesViewActive = false;
    } else {
        activateMapZombiesView();
        window.isMapZombiesViewActive = true;
    }
}


/**
 * Add/remove a location mark on the zones containing the given item
 * 
 * @param {int} itemId The ID of the item to mark
 * @returns {undefined}
 */
async function toggleMapItemMarker(itemId) {
    
    var itemsCoords = [];
    if(Number.isInteger(itemId)) {
        itemsCoords = getItemCoords(itemId);
    } else {
        itemsCoords = getItemCoords("boost");
    }

    for(let coords of Object.values(await itemsCoords)) {
        let zone = document.querySelector("#map #zone"+coords);
        zone.style.opacity = 1;
        zone.dataset.marker = 1;
    }
    
    toggleMapMarker('generic');
}


function toggleMapNeighborhoodView() {
    
    if(window.isMapNeighborhoodViewActive === true) {
        display("#map .nbr_defenses");
        hide(["#map .zone_name", "#map .city_name"]);
        window.isMapNeighborhoodViewActive = false;
    } else {
        hide("#map .nbr_defenses");
        display(["#map .zone_name", "#map .city_name"]);
        window.isMapNeighborhoodViewActive = true;
    }
}


/**
 * Displays colors above map zones to show the amount of items on the ground
 * 
 * @returns {undefined}
 */
function activateMapItemsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
        
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        let nbrItems = parseInt(squareContainer.dataset.items);
        
        let color = "";
        if(nbrItems > 15) {
            color = 'darkred';
        } else if(nbrItems > 10) {
            color = 'red';
        } else if(nbrItems > 5) {
            color = 'orange';
        } else if(nbrItems > 0) {
            color = 'green';
        } else if(nbrItems === 0) {
//            color = 'grey';
        }
        
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        if(!squareContainer.querySelector(".items_amount")) {
            let newDiv = document.createElement("div");
            newDiv.className = "items_amount";
            newDiv.innerHTML = (nbrItems > 0) ? nbrItems : "";
            squareContainer.appendChild(newDiv);
        }
        
        // Color the zones depending on the number of items
        squareContainer.querySelector(".items_amount").style.background = color;
    }
    
    hide(["#views_bar, #attack_bar", "#tasks_button"]);
    hide(["#map .nbr_defenses"]);
    display("#map_legend_items");
    display(".items_amount");
}


/**
 * Displays colors above map zones to show the zones already explored
 * 
 * @returns {undefined}
 */
function activateMapExplorationsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
     
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container"),
            color = "";
        
        if(!squareContainer.querySelector(".items_amount")) {
            let newDiv = document.createElement("div");
            newDiv.className = "items_amount";
            squareContainer.appendChild(newDiv);
            
            // Mark the zones visited today
            if(Number(squareContainer.dataset.cyclelastvisit) === getCurrentCycle()) {
                newDiv.innerHTML += "&#x1F97E;";
                color = "darkred";
            } else {
                newDiv.innerHTML += "&#x26CF;&#xFE0F;";
                color = "green";
            }
            
            // Color the zones
            squareContainer.querySelector(".items_amount").style.background = color;
        }
    }
    
    hide(["#views_bar, #attack_bar", "#tasks_button"]);
    hide([".nbr_defenses"]);
    display("#map_legend_explorations");
    display(".items_amount");
}


function desactivateMapItemsView() {
    
    hide("#map_legend_items");
    hide("#map .items_amount");
    display("#map .nbr_defenses");
}


function desactivateMapExplorationsView() {
    
    hide("#map_legend_explorations");
    hide(".items_amount");
}


function desactivateMapPathsView() {
    
    // Hide the stages of expeditions drawn on the map
    hide(".path_stage");
    // Resets the active expedition in the paths bar
    hide("#paths_bar .active");
    display("#paths_bar .inactive");
    // Hides the list of expeditions
    hide(["#paths_panel", "#paths_bar"]);
//    document.querySelectorAll("#paths_panel .card").forEach(el => el.remove());
}


function toggleMapItemsView() {
    
    if (window.isMapItemsViewActive === true) {   
        desactivateMapItemsView();
        window.isMapItemsViewActive = false;
    } else {
        activateMapItemsView();
        window.isMapItemsViewActive = true;
    }
}


function toggleMapExplorationsView() {
    
    if (window.isMapExplorationsViewActive === true) {   
        desactivateMapExplorationsView();
        window.isMapExplorationsViewActive = false;
    } else {
        activateMapExplorationsView();
        window.isMapExplorationsViewActive = true;
    }
}


function toggleMapPathsView() {
    
    if (window.isMapPathsViewActive === true) {   
        desactivateMapPathsView();
        window.isMapPathsViewActive = false;
    } else {
        activateMapPathsView();
        window.isMapPathsViewActive = true;
    }
}


/**
 * Cancels all the effects of satellite view (colors for the zombies...)
 * 
 * @returns {undefined}
 */
function resetMapView() {
    
    desactivateMapZombiesView();
    window.isMapZombiesViewActive = false;
    desactivateMapItemsView();
    window.isMapItemsViewActive = false;
    desactivateMapExplorationsView();
    window.isMapExplorationsViewActive = false;
    desactivateMapPathsView();
    window.isMapPathsViewActive = false;
    
    hide("#map .location");
    
    display(["#views_bar, #attack_bar", "#tasks_button"]);
    
    window.isMapNeighborhoodViewActive = true;
    toggleMapNeighborhoodView();
}


/**
 * Display/hide the frames around the cities on the map and the bar to filter them
 * (defensense, transportations, weather...)
 */
function toggleCityframesView() {

    toggle(["#views_bar", "#tasks_button", "#attack_bar"]);
    toggle(["#cityframes_bar", "#map_legend_cityframes"]);
    
    toggle(["#mapSvg", ".cityframe .label", "#map .nbr_defenses"]);
    document.querySelectorAll(".cityframe").forEach(
        (cityframe) => cityframe.classList.toggle("active")
    );
    
    // By default, display only the "defenses"
    switchCityframesType("defenses");
    document.querySelector("#cityframes_bar .defenses").classList.add("active");
}


function switchCityframesType(typeToActivate) {
    
    // Display the appropriate legend on the map
    hide('#map_legend_cityframes ul');
    display(`#map_legend_cityframes .${typeToActivate}`);    
    // Display the appropriate cityframes on the map
    hide('#map .cityframe');
    display(`#map .${typeToActivate}`);//, '#map .zombie_core']);    
    // Display the appropriate connections between the cities
    hide('#mapSvg line');
    display(`#mapSvg .${typeToActivate}`);
    
    // Highlight the button of the activated type (defenses, resources...)
    document.querySelectorAll(`#cityframes_bar .path`).forEach(
        element => element.classList.remove("active")
    );
    document.querySelector(`#cityframes_bar .${typeToActivate}`).classList.add("active");
}


/**
 * Adds a location sign above the player's city on the map
 * 
 * @param {string} myCityZoneId The HTML ID of the zone which contains the player's city
 * @returns {undefined}
 */
function addCityLocationMarker(myCityZoneId) {
    
    let htmlBubble = 'Ceci est votre habitation ! Votre refuge contre les zombies...',
        htmlLocationMarker = '<img src="resources/img/free/map_location.svg" class="location">';

    if(myCityZoneId !== null) {
        document.querySelector(`#${myCityZoneId} .bubble .roleplay`).innerHTML = htmlBubble;
        document.querySelector(`#${myCityZoneId}`).innerHTML += htmlLocationMarker;
    }
}


/**
 * Zooms in or out of the map with the "+/-" buttons
 * 
 * @param {string} direction Set to "in" to zoom in, "out" to zoom out.
 */
function zoomMapStep(direction) {
    // Default zoom level
    if(window.currentZoomPercent === undefined) {        
        window.currentZoomPercent = _defaultMapZoomPercent;
    }
    
    let currentZoomPercent = parseInt(window.currentZoomPercent);
    let newZoomPercent = (direction === "in") ? currentZoomPercent+30 : currentZoomPercent-30;
    // Move the cursor on the <range> selector
    document.querySelector("#zoom_range").value = newZoomPercent;
    // Executes the zoom/unzoom
    zoomMapRange(newZoomPercent);
}


/**
 * Centers the zoomed map on the player
 */
function centerMapOnMe() {
    
    centerMapOnZone(document.querySelector("#me").closest(".hexagon").id);
}


/**
 * Centers the map on a zone
 * 
 * @param {string} zoneHtmlId The HTML ID of the concerned zone, ex: "zone10_2"
 * @returns {undefined}
 */
function centerMapOnZone(zoneHtmlId) {
    
    let viewport = document.querySelector("#map_body_wrapper").getBoundingClientRect();
    let me = document.querySelector(`#${zoneHtmlId}`).getBoundingClientRect();
    
    let offsetX = (me.x - viewport.x + me.width/2 - viewport.width/2); 
    let offsetY = (me.y - viewport.y + me.height/2 - viewport.height/7);
    
    _scrollBoosterInstance.scrollTo({ x: offsetX, y: offsetY });
    _scrollBoosterInstance.updateMetrics();
}


/*
 * Unzoom to display the large map
 */
function switchToMapView() {
    // Display the large map (unzoom)
    zoomMapRange(_defaultMapZoomPercent);
    setTimeout(() => centerMapOnMe(), 500);
    // Hide the actions panel in large mode
    hide(["#actions_panel", "#personal_block_wrapper", "#map_mode_button"]);
    // Display again the general elements of the GUI
    display(["#views_bar", "#map_navigation", "#tasks_button", "#game_footer", "#attack_bar",
             "#map .nbr_defenses", "#map .bubble"]);
    // Restore the illumination on the button which displays the map navigation
    document.querySelector("#views_bar .map").classList.add("active");  
    
    document.querySelector("#map").classList.remove("action_view");
}
