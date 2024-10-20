/* 
 * Javascript functions concerning the map of the game.
 * Put only functions in this file, no directly executable code.
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
            '<img id="explosionMe" class="scale-transition scale-out" src="resources/img/thirdparty/notoemoji/collision-512.webp" width="38">\
            <div class="map_citizen" id="me">\
                <span class="nbr_defenses">'+myPseudo+'</span>\
                <img src="resources/img/free/human.png">\
            </div>\
            <div class="halo">&nbsp;</div>'
        );
    }
    
    let htmlBubble = '<h5 class="name">Vous êtes ici&nbsp;!';
    
    myZone.querySelector(".map_citizen").id = "me";
    myZone.querySelector(".halo").classList.remove("inactive");
    myZone.querySelector(".nbr_defenses").innerHTML = myPseudo;
    myZone.querySelector(".bubble .roleplay").innerHTML = htmlBubble;
    
    // Event listener when clicking on the player on his map zone
    listenToMeOnMap();
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
            if(nbrCitizens > 1)  {
                var label = "[Groupe]";
                    bubble = `${nbrCitizens} citoyens sont rassemblés ici.`;
            } else {
                var label = citizen.citizen_pseudo,
                    bubble = "Le citoyen "+citizen.citizen_pseudo+" est ici.";
            }
            
            zone.insertAdjacentHTML("afterbegin", 
                `<img id="explosionMe" class="scale-transition scale-out" src="resources/img/thirdparty/notoemoji/collision-512.webp" width="38">
                <div class="map_citizen">
                    <span class="nbr_defenses">${label}</span>
                    ${htmlCitizensImages(nbrCitizens)}
                </div>
                <div class="halo inactive">&nbsp;</div>`);
            zone.querySelector(".roleplay").innerHTML = bubble;
            
            // Delete the "&nbsp;" required on the empty zones 
//            if(zone.querySelector(".empty") !== null) {
//                zone.querySelector(".empty").remove();
//            }
        }
    }
    
    return _citizens;
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
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
        
        let zones = await _jsonMap;

        let buildingCarcs = _configsBuildings[city.city_type_id],
            buildingIconHtml = buildingCarcs["icon_html"],
            buildingIconPath = "resources/img/"+buildingCarcs["icon_path"],
            buildingIconWidth = Math.round(buildingCarcs["icon_size_ratio"] * 32),
            buildingEmojiSize = Math.round(buildingCarcs["icon_size_ratio"] * 1.5),
            buildingName = buildingCarcs["name"],
            buildingDescr = buildingCarcs["descr_ambiance"];
        
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
                nbrItems = zone.dataset.items,
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
            if(parseInt(zone.closest(".square_container").dataset.cyclelastvisit) < getCurrentCycle()) {
                html = `<span class="nbr_defenses diggable pulse">&#x26CF;&#xFE0F;</span>`;
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
        zone.insertAdjacentHTML("afterbegin", `<span class="city_name hidden">${cityName}</span>`);
        
        // Adds the building description in the bubble of the zone
        zone.querySelector(".roleplay").innerHTML = `
            <h5 class="name">${buildingName}</h5>
            <hr>
            <div class="descr_ambiance">${nl2br(buildingDescr)}</div>`;
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
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        let zombiesAmount = squareContainer.querySelector(".zombies_amount");
        let controlpoints_zombies = parseInt(squareContainer.dataset.controlpointszombies);
        let controlpoints_one_citizen = 2;
        
        let color = 'white';
        if(controlpoints_zombies > controlpoints_one_citizen*3) {
            color = 'darkred';//'#ff0505'; // Needs 4 citizens (8 CP) or more
        } else if(controlpoints_zombies > controlpoints_one_citizen*2) {
            color = 'red';// '#ff5b00'; // Safe with 3 citizens (6 CP)
        } else if(controlpoints_zombies > controlpoints_one_citizen*1) {
            color = 'orange';//'#fb8c00'; // Safe with 2 citizens (4 CP)
        } else if(controlpoints_zombies > 0) {
            color = 'green'; //'#d4ac0d'; Safe with 1 citizen (2 CP)
        } else if(controlpoints_zombies === 0) {
            color = 'grey';  // No zombies
        } 
        
        display("#map_legend_zombies");
        // Color the zones depending on the number of zombies
        zombiesAmount.style.background = color;
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        if(parseInt(squareContainer.dataset.zombies) !== 0) {
            zombiesAmount.innerHTML = squareContainer.dataset.zombies;
        }
    }
    
    hide(["#attack_bar", "#tasks_button"]);
    // Displays the number of zombies on each zone
    display(".zombies_amount");
    // Hides the icons of zombies, because they are above the colored background
    hide([".zombies", ".nbr_defenses"]);
}


function desactivateMapZombiesView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
//    for(let i=0; i<hexagons.length; i++) {
//        
//        let squareContainer = hexagons[i].querySelector(".square_container");
//        // Remove the colors on the zones
//        squareContainer.style.background = "none";
//        squareContainer.style.border = "none";
//    }
    
    hide("#map_legend_zombies");
    hide(".zombies_amount");
    // Display the icons of zombies again
    display(".zombies");
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
    
    let zombieCoresCoords = getItemCoords(itemId);
    
    for(let coords of Object.values(await zombieCoresCoords)) {
        let zone = document.querySelector("#map #zone"+coords);
        zone.style.opacity = 1;
        zone.dataset.marker = 1;
    }
    
    toggleMapMarker('generic');
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
 * Get the coordinates of all the zones containing an exemplary of the given item
 * 
 * @param {int} itemId The ID of the item your are looking for
 * @returns {array} The coordinates of the zones containing the item
 *                  Exemple: [0_3, 4_1, 5_8, ...]
 */
async function getItemCoords(itemId) {
    
    _jsonMap = await getMapZonesOnce(mapId);    
    let itemCoords = [];
    
    for(let zone of Object.entries(_jsonMap)) {
        // If the item ID is in the zone, memorize its coordinates
        if(zone[1].items !== null && itemId in zone[1].items) {
            itemCoords.push(zone[0]);
        }
    }
    
    return itemCoords;
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
        
        let color = 'white';
        if(nbrItems > 15) {
            color = 'darkred';
        } else if(nbrItems > 10) {
            color = 'red';
        } else if(nbrItems > 5) {
            color = 'orange';
        } else if(nbrItems > 0) {
            color = 'green';
        } else if(nbrItems === 0) {
            color = 'grey'; 
        }
        
        display(".items_amount");
        display("#map_legend_items");
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        if(!squareContainer.querySelector(".items_amount")) {
            let newDiv = document.createElement("div");
            newDiv.className = "items_amount";
            squareContainer.appendChild(newDiv);
            
            // Mark the zones visited today
            if(parseInt(squareContainer.dataset.cyclelastvisit) === getCurrentCycle()) {
                newDiv.innerHTML += '&#x1F97E;';
            } else {
                newDiv.innerHTML += '&#x26CF;&#xFE0F;';
            }
        }
        let itemsAmount = squareContainer.querySelector(".items_amount");
        
        // Color the zones depending on the number of items
        itemsAmount.style.background = color;
        
        // Hides the zombies, because they are above the colored background
        hide([".zombies", ".nbr_defenses"]);
        hide(["#attack_bar", "#tasks_button"]);
    }
}


function desactivateMapItemsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        // Hides the legend
        hide("#map_legend_items");
        // Remove the colors on the zones
//        let squareContainer = hexagons[i].querySelector(".square_container");
//        squareContainer.style.background = "none";
//        squareContainer.style.border = "none";
        hide(".items_amount");
        // Display the zombies again
        display(".zombies");
    }
}


/**
 * Displays the expeditions on the map
 * 
 * @returns {undefined}
 */
async function activateMapPathsView() {
    
    // Get the datas about the expeditions
    let mapId = await document.querySelector("#mapId").innerText;
    let json = await callApi("GET", "paths", "action=get&map_id="+mapId);
    
    // Draw the course of each expedition on the map
    drawPathsOnMap(json.datas.courses);
    // Populate the list of expeditions (horizontal bar)  
    populatePathsBar(json.datas.courses, json.datas.members);
    // Populate the list of expeditions (lateral panel)    
    populatePathsPanel(json.datas.courses, json.datas.members);
}


function desactivateMapPathsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        // Hide the stages of expeditions drawn on the map
        hide(".path_stage");
        // Resets the active expedition in the paths bar
        hide("#paths_bar .active");
        display("#paths_bar .inactive");
        // Hides the list of expeditions
        hide(["#paths_panel", "#paths_bar", "#attack_bar"]);
//        document.querySelectorAll("#paths_panel .card").forEach(el => el.remove());
    }
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
    desactivateMapPathsView();
    window.isMapPathsViewActive = false;
    
    display(["#attack_bar", "#tasks_button"]);
    
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
 * Zoom on the player to show the "action" buttons
 */
function switchToActionView() {
    // Zoom the map on the player
    zoomMapRange(220);
    setTimeout(() => centerMapOnMe(), 2000);
    // Display the actions panel (dig...)
    display(["#actions_panel", "#personal_block_wrapper"]);
    // Hide some elements of the GUI to make the interface look lighter
    hide(["#views_bar", "#map_navigation", "#tasks_button", "#game_footer"]);
    hide([".nbr_defenses", ".bubble"]);
    desactivateMapPathsView();
    // Display the button which switches to the Map mode
    display("#map_mode_button");
    // Remove the illumination on the button which displays the map navigation
    document.querySelector("#views_bar .map").classList.remove("active");     
    document.querySelector("#views_bar .paths").classList.remove("active");
    
    updateActionBlocks();
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
}
