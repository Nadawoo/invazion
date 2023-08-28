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
    updateBlockAlertControl(zoneData.controlpointscitizens, zoneData.controlpointszombies);  
    
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
        myZone = document.querySelector(`#zone${myCoordX}_${myCoordY} .square_container`);

    let htmlMe = '<div class="map_citizen" id="me"><img src="resources/img/free/human.png"></div>\
                  <div class="halo">&nbsp;</div>',
        htmlBubble = '<h5 class="name">Vous êtes ici&nbsp;!</h5>\
                    <hr>\
                    Utilisez les boutons rouges pour vous déplacer, fouiller le sol,\
                    attaquer des zombies, ramasser des objets...';
    
    // Don't show the other citizens under the player's silhouette
    if(myZone.querySelector(".map_citizen") !==  null) {
        myZone.querySelector(".map_citizen").remove();
    }
    
    // Add the player's silhouette 
    myZone.innerHTML += htmlMe;
    myZone.querySelector(".bubble .roleplay").innerHTML = htmlBubble;
}


/**
 * Place the citizens on the map. They are not loaded by the PHP to speed up
 * the loading of the map.
 * 
 * @param {int} mapId
 */
async function addCitizensOnMap(mapId) {
    
    // Get the citizens of the map by calling the Invazion's API
    _citizens = await getMapCitizensOnce(mapId);
    
    // Place the citizens on the appropriate zones
    for(let citizenId in _citizens) {
        let citizen = _citizens[citizenId],
            htmlCoords = citizen.coord_x+"_"+citizen.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
        
        // Don't add the citizen if an other citizen is already placed in the zone
        if(zone.querySelector(".map_citizen") === null && zone.dataset.zombies < 1 && zone.dataset.cityid < 1) {
            
            if(zone.dataset.citizens > 1)  {
                var content = "&#10010;",
                    bubble = "Plusieurs citoyens se sont rassemblés ici... \
                              Complotent-ils quelque chose&nbsp;?";
            } else {
                var content = citizen.citizen_pseudo.slice(0, 2),
                    bubble = "Le citoyen "+citizen.citizen_pseudo+" est ici.";
            }
            
            zone.insertAdjacentHTML("afterbegin", '<div class="map_citizen">'+content+'</div>');
            zone.querySelector(".roleplay").innerHTML = bubble;
            // Delete the "&nbsp;" required on the empty zones 
            if(zone.querySelector(".empty") !== null) {
                zone.querySelector(".empty").remove();
            }
        }
    }
}


/**
 * Place the cities and buildings on the map. They are not loaded by the PHP 
 * to speed up the loading of the map.
 * 
 * @param {int} mapId
 */
async function addCitiesOnMap(mapId) {
    
    // Get the cities of the map by calling the Invazion's API
    _cities = await getMapCitiesOnce(mapId);
    
    // Place the cities on the appropriate zones
    for(let cityId in _cities) {
        
        let city = _cities[cityId],
            htmlCoords = city.coord_x+"_"+city.coord_y,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container");
        
        let buildingCarcs = _configsBuildings[city.city_type_id],
            buildingIconHtml = buildingCarcs["icon_html"],
            buildingIconPath = "resources/img/"+buildingCarcs["icon_path"],
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
                zone.insertAdjacentHTML("afterbegin", `<div class="icon_placeholder"><img src="${buildingIconPath}" alt="${buildingIconHtml}" width="24" height="24"></div>`);
                // Delete the "&nbsp;" required on the empty zones 
                if(zone.querySelector(".empty") !== null) {
                    zone.querySelector(".empty").remove();
                }
            }
        }
        else {
            // If no image file for this building, displays an emoji for the building
            zone.insertAdjacentHTML("afterbegin", `<div class="icon_html">${buildingIconHtml}</div>`);
            // Delete the "&nbsp;" required on the empty zones 
            if(zone.querySelector(".empty") !== null) {
                zone.querySelector(".empty").remove();
            }
        }
        
        // Adds the name of the building
        cityName = (city["city_name"] === null) ? buildingName : city["city_name"];
        zone.insertAdjacentHTML("afterbegin", `<span class="city_name" style="">${cityName}</span>`);
        
        // Adds the building description in the bubble of the zone
        zone.querySelector(".roleplay").innerHTML = `<h5 class="name">${buildingName}</h5><hr><div class="descr_ambiance">${buildingDescr}</div>`;
        // Put the tile higher than its neighbors
        zone.closest(".hexagon").classList.add("ground_city", "elevate");
        // Make the building's zone always visible, even when never visited
        zone.closest(".hexagon").style.opacity = 1;
        // Used to memorize the type of building in HTML
        // TODO: we could remove this attribute by using the attribute data-cityid
        zone.dataset.citytypeid = city.city_type_id;
    }
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
    
    let mapRect  = document.querySelector("#map_body").getBoundingClientRect();
    let zoneRect = document.querySelector(zoneHtmlId).getBoundingClientRect();
    
    let zonePositions = {
        // "- mapRect.x" => substracts the space between the top of the window and
        // the top of the map
        // "+ zoneRect.width/2" => places the point at the center of the zone
        "x" : Math.round(zoneRect.x - mapRect.x + zoneRect.width/2),
        "y" : Math.round(zoneRect.y - mapRect.y + zoneRect.height/2)
        };
       
    return zonePositions;
}


/**
 * Draws a line in the existing <svg> to replace the previous line.
 * The order of the zones given in parameters doesn't matter.
 * 
 * @param {string} origHtmlId The HTML ID of the first zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone3_10")
 * @param {string} destinHtmlId The HTML ID of the second zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone8_2")
 * @returns {undefined}
 */
function updateLineBetweenZones(origHtmlId, destinHtmlId) {
          
    let orig   = getZonePositions(origHtmlId);
    let destin = getZonePositions(destinHtmlId);
    
    // Erases the existing line in the <svg>
    document.querySelector("#mapSvg").innerHTML = "";
    
    // Draws the new line in the same <svg>
    let line = document.createElementNS("http://www.w3.org/2000/svg", "line");
    line.setAttribute("x1", orig.x);
    line.setAttribute("y1", orig.y);
    line.setAttribute("x2", destin.x);
    line.setAttribute("y2", destin.y);
    document.querySelector("#mapSvg").append(line);
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
        let controlpoints_zombies = parseInt(squareContainer.dataset.controlpointszombies);
        
        let color = 'white';
        if(controlpoints_zombies > 15) {
            color = 'darkred';//'#ff0505'; // Needs 4 citizens (20 CP) or more
        } else if(controlpoints_zombies > 10) {
            color = 'red';// '#ff5b00'; // Safe with 3 citizens (15 CP)
        } else if(controlpoints_zombies > 5) {
            color = 'orange';//'#fb8c00'; // Safe with 2 citizens (10 CP)
        } else if(controlpoints_zombies > 0) {
            color = 'green'; //'#d4ac0d'; Safe with 1 citizen (5 CP)
        } else if(controlpoints_zombies === 0) {
            color = 'grey';  // No zombies
        } 
        
        // Color the zones depending on the number of zombies
        squareContainer.style.background = color;        
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        // Hides the zombies, because they are above the colored background
        hideClasses(["zombies"]);
    }
}


function desactivateMapZombiesView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        // Remove the colors on the zones
        squareContainer.style.background = "none";        
        // Display the zombies again
        unhideClass("zombies");
    }
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


function toggleMapNeighborhoodView() {
    
    if(window.isMapNeighborhoodViewActive === true) {
        var display = "none";
        window.isMapNeighborhoodViewActive = false;
    } else {
        var display = "block";
        window.isMapNeighborhoodViewActive = true;
    }
    
    let zones = document.querySelectorAll("#map .zone_name");
    for(let zone of zones) {
        zone.style.display = display;
    }
    
    let cities = document.querySelectorAll("#map .city_name");
    for(let city of cities) {
        city.style.display = display;
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
        
        // Color the zones depending on the number of items
        squareContainer.style.background = color;        
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        // Hides the zombies, because they are above the colored background
        hideClasses(["zombies"]);
    }
}


function desactivateMapItemsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        // Remove the colors on the zones
        squareContainer.style.background = "none";        
        // Display the zombies again
        unhideClass("zombies");
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
 * Zooms in or out of the map
 * 
 * @param {string} direction Set to "in" to zoom in, "out" to zoom out.
 */
function zoomMap(direction) {
    
    // Set here the percentages of zoom you allow
    // <100 = zoom out, >100 = zoom in
    let allowedZooms = [60, 70, 80, 100, 200, 400];
    // Set here the key (not the value!) of the "allowedZooms" wich corresponds 
    // to the default zoom level you want
    let defaultZoomLevel = 3;
    
    if(window.currentZoomLevel === undefined) {        
        window.currentZoomLevel = defaultZoomLevel;
    }
    
    let zoomLevel = window.currentZoomLevel;
    zoomLevel = (direction === "in") ? Math.min(zoomLevel+1, allowedZooms.length-1) : Math.max(zoomLevel-1, 0);
    
    document.querySelector("#map_body").classList.add("zoomedIn");
    document.querySelector("#map_body").style.transform = `scale(${allowedZooms[zoomLevel]}%)`;
    setTimeout(centerMapOnMe, 300);
    
    window.currentZoomLevel = zoomLevel;
}


/**
 * Centers the zoomed map on the player
 */
function centerMapOnMe() {
    
    document.querySelector("#me").scrollIntoView({behavior: "smooth", block: "center", inline: "center"});
}
