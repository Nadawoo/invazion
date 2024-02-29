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
        myZone = document.querySelector(`#zone${myCoordX}_${myCoordY} .square_container`);

    let htmlMe = '<img id="explosionMe" class="scale-transition scale-out" src="resources/img/thirdparty/notoemoji/collision-512.webp" width="38">\
                  <div class="map_citizen" id="me"><img src="resources/img/free/human.png"></div>\
                  <div class="halo">&nbsp;</div>';
    let htmlBubble = '<h5 class="name">Vous êtes ici&nbsp;!';
    
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
    
    return _citizens;
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
            zone.insertAdjacentHTML("afterbegin", `<div class="icon_html"  style="font-size:${buildingEmojiSize}em;">${buildingIconHtml}</div>`);
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
            zone.insertAdjacentHTML("afterbegin", `<span class="nbr_defenses">${city.total_defenses}&#128737;&#65039;</span>`);
        }
        // Adds the number of items remaining inside the explorable building
        else if(city.city_type_id !== "undefined") {
            let maxExplorations = 100;
            // NB: #108 = ID of the item "Counter of explorations"
            let nbrExplorations = zones[htmlCoords]['items'][108] || 0;
            zone.insertAdjacentHTML("afterbegin", `<span class="nbr_defenses">${maxExplorations-nbrExplorations}/${maxExplorations}</span>`);
        }
        // Adds the name of the building
        cityName = (city["city_name"] === null) ? buildingName : city["city_name"];
        zone.insertAdjacentHTML("afterbegin", `<span class="city_name">${cityName}</span>`);
        
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
 * NB: the order of the zones given in parameters (origin/destination) doesn't 
 * really matter, as the result will be the same (a line between the 2 points).
 * 
 * @param {string} lineName   The alias of the line to treat, stored in <line name="..."> 
 * @param {string} origHtmlId The HTML ID of the first zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone3_10")
 * @param {string} destinHtmlId The HTML ID of the second zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone8_2")
 * @returns {undefined}
 */
function updateLineBetweenZones(lineName, origHtmlId, destinHtmlId, color="green") {
          
    let orig   = getZonePositions(origHtmlId);
    let destin = getZonePositions(destinHtmlId);
    
    // Erases the existing line in the <svg>
    let lineNode = document.querySelector(`#mapSvg line[name=${lineName}]`);
    if(lineNode !== null) {
        lineNode.remove();
    }
    
    // Draws the new line in the same <svg>
    let line = document.createElementNS("http://www.w3.org/2000/svg", "line");
    line.setAttribute("name", lineName);
    line.setAttribute("x1", orig.x);
    line.setAttribute("y1", orig.y);
    line.setAttribute("x2", destin.x);
    line.setAttribute("y2", destin.y);
    line.setAttribute("style", `stroke:${color}`);
    document.querySelector("#mapSvg").append(line);
}


/**
 * Draws a line between the cities connected over the map
 * 
 * @param {int} mapId
 */
async function updateConnectedCitiesLines(mapId) {

   _cities = await getMapCitiesOnce(mapId);

   for(let city of Object.entries(_cities)) {
       let childCity = city[1];

       if(childCity["connected_city_id"] !== null) {
           let parentCity = _cities[childCity["connected_city_id"]],
               childCityZoneId  = `zone${childCity["coord_x"]}_${childCity["coord_y"]}`,
               parentCityZoneId = `zone${parentCity["coord_x"]}_${parentCity["coord_y"]}`;
           updateLineBetweenZones(`${childCityZoneId}To${parentCityZoneId}`, `#${parentCityZoneId}`, `#${childCityZoneId}`, "#F4D03F");
       }
   }
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
        
        display("map_legend_zombies");
        // Color the zones depending on the number of zombies
        squareContainer.style.background = color;
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        
        if(parseInt(squareContainer.dataset.zombies) !== 0) {
            squareContainer.querySelector(".zombies_amount").innerHTML = squareContainer.dataset.zombies;
        }
    }
    
    // Displays the number of zombies on each zone
    unhideClasses(["zombies_amount"]);
    // Hides the icons of zombies, because they are above the colored background
    hideClasses(["zombies"]);
}


function desactivateMapZombiesView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        // Remove the colors on the zones
        squareContainer.style.background = "none";        
    }
    
    hide("map_legend_zombies");
    hideClasses(["zombies_amount"]);
    // Display the icons of zombies again
    unhideClasses(["zombies"]);
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
        
        display("map_legend_items");
        // Color the zones depending on the number of items
        squareContainer.style.background = color;        
        // Reveal all the zones, regardless their date of last visit
//        hexagons[i].style.opacity = 1;
        // Mark the zones visited today
        if(squareContainer.dataset.visitedtoday === "1") {
            squareContainer.innerHTML += '<div class="items_amount">&#x1F97E;</div>';
        }
        
        // Hides the zombies, because they are above the colored background
        hideClasses(["zombies"]);
    }
}


function desactivateMapItemsView() {
    
    let hexagons = document.querySelectorAll("#map_body .hexagon");
    
    for(let i=0; i<hexagons.length; i++) {
        
        let squareContainer = hexagons[i].querySelector(".square_container");
        // Hides the legend
        hide("map_legend_items");
        // Remove the colors on the zones
        squareContainer.style.background = "none";        
        hideClasses(["items_amount"]);
        // Display the zombies again
        unhideClasses(["zombies"]);
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
 * Zooms in or out of the map with the "+/-" buttons
 * 
 * @param {string} direction Set to "in" to zoom in, "out" to zoom out.
 */
function zoomMapStep(direction) {
    // Default zoom level = 100%
    if(window.currentZoomPercent === undefined) {        
        window.currentZoomPercent = 100;
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
 * Show/hide the <range> selector to change the zoom level
 * 
 * @returns {undefined}
 */
function toggleZoomRange() {
    
    let display =(document.querySelector("#zoom_form .range-field").style.display === "block") ? "none" : "block";
    
    document.querySelector('#zoom_form .range-field').style.display = display;
}


/**
 * Centers the zoomed map on the player
 */
function centerMapOnMe() {
    
    let viewport = document.querySelector("#map_body_wrapper").getBoundingClientRect();
    let me = document.querySelector("#me").getBoundingClientRect();
    
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
    display(["actions_panel"]);
    changeDisplayValue("personal_block_wrapper", "flex");
    // Hide some elements of the GUI to make the interface look lighter
    hide(["attack_bar", "map_navigation", "floating_wall"]);
    hideClasses(["nbr_defenses", "bubble"]);
    // Display the button which switches to the Map mode
    hide(["action_mode_button"]);
    changeDisplayValue("map_mode_button", "flex");
}


/*
 * Unzoom todisplay the large map
 */
function switchToMapView() {
    // Display the large map (unzoom)
    zoomMapRange(100);
    setTimeout(() => centerMapOnMe(), 500);
    // Hide the actions panel in large mode
    hide(["actions_panel", "personal_block_wrapper"]);
    // Display again the general elements of the GUI
    changeDisplayValue(["attack_bar", "map_navigation", "floating_wall"], "flex");
    unhideClasses(["nbr_defenses", "bubble"]);
    // Display the button which switches to the Action mode
    hide(["map_mode_button"]);
    changeDisplayValue("action_mode_button", "flex");
}
