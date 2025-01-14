/**
 * Miscellaneous JS functions that doesn't fit the others JS scripts.
 * Don't put events listeners here (see events.js) nor actions executed 
 * on loading the page (see onPageLoad.js)
 */


/*
 * Button to enlarge/reduce the bag (hide the overflowing items)
 * @returns {undefined}
 */
function toggleBag() {
    
    let citizenId = document.querySelector("#citizenId").innerText;
    let bagItems = _citizens[citizenId]["bag_items"];
    let bagItemsSelector = "#bagbar .items_list";
    
    // Remove the status and the AP from the bag (not real items)
    let allBagItemsButTags = filterAllItemsButTags(bagItems, _configsItems, ["actionPoints", "status"]);
    
    // Populate the list of items in the bag if not already done
    if(document.querySelector(bagItemsSelector).innerText === "") {
        populateItemsList(bagItemsSelector, allBagItemsButTags, _citizens[citizenId]["bag_size"]);
    }
    
    toggle(bagItemsSelector);
    document.querySelector("#bagbar").classList.remove("inactive");
    
    hide("#statusbar .items_list");
    document.querySelector("#statusbar").classList.toggle("inactive");
    document.querySelector("#apbar").classList.toggle("inactive");
}


function toggleStatus() {
    
    toggle("#statusbar .items_list");
    document.querySelector("#statusbar").classList.remove("inactive");
    
    hide("#bagbar .items_list");
    document.querySelector("#bagbar").classList.toggle("inactive");
    document.querySelector("#apbar").classList.toggle("inactive");
}


/**
 * Filter the items by removing the ones owning the given tags.
 * 
 * @param {array} bagItems The list of items, as returned by the Azimutant's API
 *                         (pairs item_id=>item_amount)
 * @param {array} itemsCaracs The characteristics of the items, as returned 
 *                            by the "configs" API of Azimutant
 * @param {array} allButTags One or several tags to use for filtering.
 *                           The items owning these tags will be removed.
 * @return {array}
 */
function filterAllItemsButTags(bagItems, itemsCaracs, allButTags) {
     
    for(let [itemId, itemAmount] of Object.entries(bagItems)) {
        // Handles the anormal case where an item ID is not among the list of items.
        // Possible when an item on a map is not in the items set for this map.
//        let item_caracs = isset(items_caracs[$item_id]) ? items_caracs[$item_id] : set_default_variables('$item', item_id);
        let item_caracs = itemsCaracs[itemId];
        
        allButTags.forEach((tag) => {
            if(item_caracs['tags'].includes(tag)) {
                delete bagItems[itemId];
            }
        });
    }
    
    return bagItems;
}


/**
 * Wait for XX milliseconds before continuing the execution of the function
 * Ex: await sleep(1000);
 * Important: MUST be placed inside an "async" function to work
 * 
 * @param {int} milliseconds The duration of the pause, in milliseconds
 * @returns {Promise}
 */
function sleep(milliseconds) {
    
    return new Promise(resolve => setTimeout(resolve, milliseconds));
}


/**
 *  Displays the location icon on every zone which contains the specified element
 *  
 *  @param {string} objectToMark The alias of the object to mark on the map.
 *                               See the dictionary "markableObjects" in the present 
 *                               function to know the available aliases.
 */
function toggleMapMarker(objectToMark) {
    
    // Here are listed the DOM selectors to mark the zones you want (e.g. class name...)
    var markableObjects = {
        "items":    ".square_container:not([data-items='0'])",
        "citizens": ".square_container:not([data-citizens='0'])",
        "boost":    "#map [data-markerboost='1']",
        "resource": "#map [data-markerresource='1']",
        "generic":  "#map [data-marker='1']"
        };
        
    if (window.areMapMarkersActive !== true) {
        // Remove the eventual previously created markers, as they can mark 
        // an other type of item (boosts, resources...)
        deleteMapMarkers();
        // Add the HTML for the icons in the zones
        document.querySelectorAll(markableObjects[objectToMark]).forEach(element =>
            element.innerHTML += '<img src="resources/img/free/map_location.svg" class="location animate__animated animate__slideInDown">'
        );
        
        display("#map .location");
//        hide("#map .nbr_defenses");
        window.areMapMarkersActive = true;
    }
    else {    
        // Hides the icons added by the previous call to the function
        hide("#map .location");
        window.areMapMarkersActive = false;
    }
}


/**
 * Remove all the "location.svg" markers from the map, whatever they mark
 * (boosts, resources...)
 */
function deleteMapMarkers() {
    
    document.querySelectorAll("#map .location").forEach(element => 
        element.remove()
    );
    
    window.areMapMarkersActive = false;
}


/**
 * Displays/hides the blocks of actions at the right of the map
 * (digging, zombies, citizens in zone, build tent...)
 */
function toggleActionBlock(buttonAlias) {
    
    let blockId = "#block_"+buttonAlias;
    let roundId = "#round_"+buttonAlias;
    
    // In case the parameter is not in the cookie yet, or not valid
    if (buttonAlias === undefined || document.querySelector(blockId) === null) {
        return false;
    }
    
    if(!document.querySelector(blockId).classList.contains("hidden")) {
        // If the block is already displayed, the button hides it
        hide(blockId);
        document.querySelector(roundId).classList.remove("active");
        // Will memorize in the cookie to hide the block
        buttonAlias = undefined;
    }
    else {
        // Hides all the action blocks...
        hide("#actions fieldset");
        document.querySelectorAll("#round_actions input").forEach(
            element => element.parentNode.parentNode.classList.remove("active")
        );        
        // ... Then displays the only action block we want
        display(blockId);
        // ... and hightlights the active button
        document.querySelector(roundId).classList.add("active");
    }
    
    setCookieConfig("round_button", buttonAlias);
}


/**
 * Displays/hides the items panels (bag and ground) at the bottom of the map 
 */
function toggleItemsPanel() {
    
    if (document.getElementById("bag_panel").style.height === "0px") {
        document.getElementById("bag_panel").style.height    = "10em";
        document.getElementById("ground_panel").style.height = "10em";
        setCookie('showitemspanel', 1);
    }
    else {
        document.getElementById("bag_panel").style.height    = 0;
        document.getElementById("ground_panel").style.height = 0;
        setCookie('showitemspanel', 0);
    }
}


/**
 * Sends the data to create a new item in game
 */
async function createItem() {
    
    let token = getCookie('token'),
        formData = new FormData(document.querySelector('form')),
        request = {};
    
    for (var pair of formData.entries()) {
        request += "&"+pair[0]+"="+pair[1];
    }
    
    // Sends the characteristics of the new item to the API
    let json = await callApi("POST", "configs", `action=create&type=item&token=${token}&${request}`);
    
    document.getElementById("error").innerHTML = json.metas.error_message;
}


/**
 * Displays the discussions list in the notifications panel.
 * WARNING : don't call this function more than needed, because it makes a distant request 
 * to the Azimutant's API.
 */
async function updateDiscussionsNotifs() {
    
    // Gets the titles of the discussions, by calling the Azimutant's API
    var jsonTopics = await callDiscussionApiOnce("all", refresh=true);
    
    var length = jsonTopics.datas.length;
    var titles = "";
    
    for (let i=0; i<length; i++) {        
        let topic        = jsonTopics.datas[i];
        let topicUrl     = urlDiscussion(topic["topic_id"], topic.last_message.message_id);
        let authorPseudo = topic.last_message.author_pseudo;
        let authorId     = topic.last_message.author_id;
        let lastMessage  = topic.last_message.message;
        let localDate    = dateIsoToString(topic.last_message.datetime_utc);

        titles += htmlDiscussionNotif(topic.title, localDate, topicUrl, authorId, authorPseudo, lastMessage);
    }

    document.getElementById("notifsList").innerHTML = titles;
}


/**
 * Moves the player from the central city to his individual home.
 * 
 * @param {int} mapId
 * @param {int} cityId
 * @returns {undefined}
 */
async function teleportToCity(mapId, cityId) {
    
    // Moves the citizen from the main city to his indivdual home
    jsonTeleport = await callApi("GET", "zone", "action=teleport&to=city&target_id="+cityId+"&token="+getCookie('token'));
    
    if(jsonTeleport.metas.error_code === "success") {    
        coordX = jsonTeleport.datas.new_coord_x;
        coordY = jsonTeleport.datas.new_coord_y;

        // Refreshes the contents of the chest (replaces the contents of 
        // the city repository by the contents of the personal chest)
        let options = { method: "GET",
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        };            
        jsonCityEnclosure = await fetch("/generators/city_enclosure.php?city_id="+cityId+"&map_id="+mapId+"&coord_x="+coordX+"&coord_y="+coordY, options).then(toJson);
        document.getElementById("blockHomeStorage").innerHTML = jsonCityEnclosure.datas.html_home_storage;
        document.getElementById("blockCityStorage").innerHTML = jsonCityEnclosure.datas.html_city_storage;
    }
}


/**
 * Change the ground type of the zone (grass, peeble, lava...)
 */
async function updateLandType(landType, coordX, coordY, radius) {
    
    let token = getCookie('token'),
        mapId = document.getElementById("mapId").innerHTML;
    
    // Sends the characteristics of the new item to the API
    let json = await callApi("GET", "zone", `action=edit&stuff=${landType}&coord_x=${coordX}&coord_y=${coordY}&radius=${radius}&token=${token}`);
}


/**
 * Move the citizen on the map
 * 
 * @param {string} direction One of the allowed directions (north, east, northeast...)
 *                           See the documentation of the "zone" API
 */
async function moveCitizen(direction) {
    
    // Ask the API for moving the player
    let token = getCookie('token'); 
    let json = await callApi("GET", "zone", `action=move&to=${direction}&token=${token}`);
    
    let current_AP = (document.querySelector("#actionPoints").innerText),
        lost_AP    = json.datas.action_points_lost;
        new_AP     = current_AP - lost_AP;
        
    // Display the eventual error in a toast
    if(lost_AP > 0 || json.metas.error_code !== "success") {
        let error_message = (json.metas.error_code === "success")
                    ? `-${lost_AP} point d'action consommé<br>(${new_AP} restants)`
                    : json.metas.error_message;
        displayToast(error_message, json.metas.error_class);
    }
    
    if(lost_AP > 0) {
        document.querySelector("#actionPoints").innerText = new_AP;
        document.querySelector("#personal_block .actionpoints .icon").innerText = new_AP;
        animateCss("#personal_block .actionpoints .icon", "flash");
    }
    
    updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);
}


/**
 * Animate an element of the GUI by using the CSS library Animate.css
 * Official site: https://animate.style
 *  
 * @param {string} cssSelector The selector of the element to animate, like with 
 *                             querySelector(). Example: "#myBlock"
 * @param {string} effectName The name of the animation to apply. Must exist among 
 *                            the effects proposed by Animate.css (see https://animate.style/).
 *                            Don't add the "animate__" prefix, just the name of the effect.
 *                            Example : "flash" (not "animate__flash")
 * @returns {undefined}
 */
function animateCss(cssSelector, effectName) {
    
    // Add the class for applying the specified effect with Animate.css
    let prefixedEffectName = `animate__${effectName}`;
    document.querySelector(cssSelector).classList.add("animate__animated");
    document.querySelector(cssSelector).classList.add(prefixedEffectName);
    // Then remove the class to allow replaying the animation later
    setTimeout(
        function() {
            document.querySelector(cssSelector).classList.remove(prefixedEffectName);
        },
        1500
    );
}


/**
 * Update the coordinates of the player and other player-related data modified 
 * by the movement
 * 
 * @param {int} newCoordX
 * @param {int} newCoordY
 * @returns {undefined}
 */
async function updateMeAfterMoving(newCoordX, newCoordY) {
        
    // Delete the informations about the previous zone (obsolete)
    _myZone = null;    
    
    // Update the stored coordinates of the player
    document.querySelector("#citizenCoordX").innerHTML = newCoordX;
    document.querySelector("#citizenCoordY").innerHTML = newCoordY;
    
    // Update the coordinates of the player in the movement paddle
    updateMovementPaddle(newCoordX, newCoordY);
    // Update the coordinates of the player in the land editor
    updateMapEditor(newCoordX, newCoordY);
    
    // Update the attribute "data-citizen" of the destination zone to add the player
    let htmlCoord = newCoordX+"_"+newCoordY;
        myZone = document.querySelector("#zone"+htmlCoord+" .square_container");
    myZone.dataset.citizens = parseInt(myZone.dataset.citizens, 10) + 1;
    
    updateRoundActionButtons(newCoordX, newCoordY);
    updateCityDistance(newCoordX, newCoordY);
//    updateBlockLandType(myZone.dataset.landtype);
    updateZombiesGauge(Number(myZone.dataset.zombies));
    updateEnterBuildingButton(myZone.dataset.citytypeid, myZone.dataset.controlpointscitizens, myZone.dataset.zombies);
    updateMoveCost(parseInt(myZone.dataset.zombies));
    updateCardCitizensInZone(myZone.dataset.citizens);
    updateBlockAction('dig');
    
    setTimeout(()=>{ centerMapOnMe(10) }, 1000);
}


/**
 * Shows/hides the card under the movement paddle notifying the presence 
 * of other humans in the player's zone
 * 
 * @param {int} nbrCitizensInZone The number of citizens in the zone
 *                                (including the current player)
 * @returns {undefined}
 */
function updateCardCitizensInZone(nbrCitizensInZone) {
    
    (nbrCitizensInZone <= 1) ? hide("#card_citizens") : display("#card_citizens");
}


/**
 * Displays/hides the button to enter the building or city in the player's zone. 
 * 
 * @param {int} cityTypeId The ID of the city type in the zone (not the ID of the city)
 */
function updateEnterBuildingButton(cityTypeId, controlPointsCitizens, nbrZombies) {
    
    // Displays the building's name under the movement paddle
    if(cityTypeId === "") {
        hide("#block_move #card_building");
    } else {
        document.querySelector("#block_move .building_name").innerHTML = _configsBuildings[cityTypeId]["name"];
        display("#block_move #card_building");
        document.querySelector("#card_building").classList.add("above_paddle");
        display(".ignore_button");
        hide(["#card_dig", "#card_ap_cost", "#card_citizens"]);
    }
    
    // Button to enter in the city
    let enterCity = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_enterable"] === 1) ? "block" : "none";
    document.querySelector(`#block_move form[name="enter_city"]`).style.display = enterCity;
    
    // Button to destroy the tent (building #13 in the database)
    let destroyCity = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_destroyable"] === 1) ? "block" : "none";
    document.querySelector('#block_move form[name="destroy_city"]').style.display = destroyCity;
    
    // Button to activate a crypt (building ID #2)
    let enterCrypt = (cityTypeId === "2") ? "block" : "none";
    document.querySelector('#button_crypt').style.display = enterCrypt;
    
    // Button to explore the building
    let buildingVisibility = (cityTypeId !== "" && _configsBuildings[cityTypeId]["is_explorable"]) ? "block" : "none";
    document.querySelector("#button_explore").style.display = buildingVisibility;
}


/**
 * Update the interface after killing a zombie
 * 
 * @param {string} apiAction The "action" parameter for the API url (e.g. "kill_zombies")
 */
async function killZombies(apiAction) {
    
    // Moves the citizen form the main city to his indivdual home
    json = await callApi("GET", "zone", "action="+apiAction+"&token="+getCookie('token'));
    
    // Display the explosion effect on the zone
    document.querySelector("#explosionMe").classList.add("scale-in");
    setTimeout(function() {
        document.querySelector("#explosionMe").classList.remove("scale-in");
        displayToast(json.metas.error_message, json.metas.error_class);
    }, 1500);
    
    if(json.metas.error_code === "success") {  
        // The main container of the zone where the player is
        let myZone = document.querySelector("#me").parentNode;
        let oldNbrZombies = myZone.dataset.zombies,
            newNbrZombies = Math.max(0, oldNbrZombies - json.datas.nbr_zombies_removed);
        let mapId = Number(document.querySelector("#gameData #mapId").innerHTML);
        
        // Update the action blocks (round buttons next to the map)
        updateBlockActionZombies(newNbrZombies);
        updateMoveCost(newNbrZombies);
        updateBlockAlertControl(Number(myZone.dataset.controlpointszombies), mapId, myZone.dataset.coordx, myZone.dataset.coordy);
        
        // Update the zombie silhouettes on the map zone
        if(newNbrZombies > 0) {
            myZone.querySelector(".zombies").getAttribute("src").innerHTML = "resources/img/motiontwin/zombie"+newNbrZombies+".gif";
        }
        // Update the hidden data about the zone
        myZone.dataset.zombies = newNbrZombies;
    }
}


/**
 * Display/hide the tootip of an item by clicking on its icon
 * 
 * @param {object} event
 * @returns {undefined}
 */
function toggleItem(event) {
    
    let itemLabel = event.target.closest(".item_label");
    var tooltip = itemLabel.querySelector(".details");
    // If the item's tooltip is already opened, we just hide it
    
    if(!tooltip.classList.contains("hidden")) {
        tooltip.classList.add("hidden");
        itemLabel.style.border = null;
        // Avoids instant re-opening of the tooltip, as it is a click in .item_label too
        event.stopPropagation();
    }
    else {
        // If we want to open a new tooltip, first close all the other open tooltips.
        let classes = document.querySelectorAll(".item_label .details:not(.hidden)");
        for (let i=0; i < classes.length; i++) {
            classes[i].closest(".item_label").style.border = null;
        }
        hide(".item_label .details");
        // Then, display the intended tooltip
        tooltip.classList.remove("hidden");
        itemLabel.style.border = "2px solid darkred";
        
        // Avoid the item's tooltip to overflow the parent container
        let parentList = tooltip.closest(".items_list");
        let tooltipRect = tooltip.getBoundingClientRect();
        let parentListRect = parentList.getBoundingClientRect();
        // Avoid overflowing on the right
        let rightGap = parentListRect.right - tooltipRect.right;
        if(rightGap < 0) {
            tooltip.style.marginLeft = `${rightGap}px`;
        }
        // Avoid overflowing at the bottom
        let bottomGap = parentListRect.bottom - tooltipRect.bottom;
        if(bottomGap < 0) {
            tooltip.style.marginTop = `${bottomGap}px`;
        }
    }
}


/**
 * Gets the log of attacks with the API and write it in the communications panel
 */
async function getCyclicAttacks(nbrExecutions) {
    
    // Don't run the function more than once (it calls the API)
    if (nbrExecutions >= 1) {
        return false;
    }
    
    // Get the HTML elements to build the log of attacks
    let options = { method: "GET",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    };
    let htmlElements = await fetch("/generators/log_attacks.php?action=get&type=cyclicattack&sort=desc", options).then(toJson);
    
    // Display the log of attacks
    document.getElementById("wallAttacks").innerHTML += htmlElements.join("\n");
}


/**
 * Gets the log of the citizens actions (healing...)
 * @param {string} htmlContainerId L'ID du <div> dans lequel écrire la liste des événements 
 */
async function getLogEvents(htmlContainerId) {
    
    var json = await callApi("GET", "discuss/threads", "action=get&type=event"),
        html = "";
        
    for (let i in json.datas) {
        html += htmlEventTemplate(json.datas[i]);
    }
    
    document.getElementById(htmlContainerId).innerHTML = html;
}


/*
 * Get the data about the player's zone by calling the Azimutant's API
 */
async function getMyZoneOnce(mapId, coordX, coordY) {
    
    // If the API has already be called before, don't re-call it
    if(_myZone === null) {
        let htmlCoord = coordX+"_"+coordY,
            token = getCookie('token'),
            json = await callApi("GET", "maps", `action=get&map_id=${mapId}&token=${token}&zones=${htmlCoord}`);    
        _myZone = json.datas.zones[htmlCoord];
    }
    
    return _myZone;
}


/**
 * Display the actions for fighting against zombies
 * 
 * @param {int} nbrZombies The number of zombies in the player's zone
 */
function showFightingZombiesButtons(nbrZombies) {
    
    if(nbrZombies > 0) {
        display("#action_zombies");
    }
}


/**
 * Digs to find an item in the zone
 */
async function dig() {
    
    let token = getCookie('token');
    
    // Call the API to dig
    let json = await callApi("GET", "zone", `action=dig&token=${token}`);
    
    // Display the result of the digging in pop-up
    document.querySelector("#popsuccess").classList.add("force_visibility");
    document.querySelector("#popsuccess .content").innerHTML = nl2br(json.metas.error_message);
    
    if(json.metas.error_code === "success") {
        // Hide the message "There are no items on the ground..."
        hide("#items_ground .greytext");
        // Add the new item(s) in the ground items list
        newItemsWithAmounts = mergeItemsIdsWithAmounts(json.datas.found_items_ids);
        populateItemsList("#items_ground .items_list", newItemsWithAmounts);
        // Make the digging button inactive
        updateDigButtons(1);
    }
}


/**
 * From a list of items IDs, eventually with duplications, bind the amount to each item:
 * - "1" if only one occurrency of the ID
 * - The amount of occurrencies if the item ID is several times in the list
 * Ex: [52, 67, 67, 104] => {52:1, 67:2, 104:1}
 * 
 * @param {array} itemsIds The list of items IDs
 *                         Ex: [52, 67, 67, 104]
 * @returns {array} The pairs item ID / item amount
 *                  Ex: {52:1, 67:2, 104:1}
 */
function mergeItemsIdsWithAmounts(itemsIds) {
    
    let itemsWithAmounts = {};
    itemsIds.forEach(itemId => {
        itemsWithAmounts[itemId] = (itemsWithAmounts[itemId] === undefined) ? 1 : itemsWithAmounts[itemId]+1;
    });
    
    return itemsWithAmounts;
}


/**
 * Picks up an item from the ground and puts it in the player's bag
 * 
 * @param {object} eventSubmitter The event.submitter returned by the eventListener
 */
async function pickupItem(eventSubmitter) {
    
    let token = getCookie('token'),
        itemId = eventSubmitter.value;
    
    // Calls the API to pick up the item
    let json = await callApi("GET", "zone", `action=pickup&item_id=${itemId}&token=${token}`);
    
    if(json.metas.error_code === "success") {
        // HTML: moves the item from the ground list to the bag list
        let itemNode = eventSubmitter.closest("li");
        document.querySelector('#bagbar .items_list').prepend(itemNode);
        // Removes 1 empty slot in the bag
        document.querySelector('#bagbar .empty_slot').remove();
        // Animate the bag to show the player where the item goes
        animateCss("#bagbar .block_icon", "heartBeat");
        // Decreases the counter for the ground items
        let myZone = document.querySelector("#me").parentNode.dataset;
        myZone.items = parseInt(myZone.items, 10) - 1;
        document.querySelector("#round_dig .dot_number").innerHTML = myZone.items;
    } else {
        // Displays the eventual error message in a pop-up
        document.querySelector("#popsuccess").classList.add("force_visibility");
        document.querySelector("#popsuccess .content").innerHTML = json.metas.error_message;
    }
}


/**
 * Drops an item from the player's bag and puts it on the ground
 * 
 * @param {object} eventSubmitter The event.submitter returned by the eventListener
 */
async function dropItem(eventSubmitter) {
    
    let token = getCookie('token'),
        itemId = eventSubmitter.value;
    
    // Calls the API to pick up the item
    let json = await callApi("GET", "zone", `action=drop&item_id=${itemId}&token=${token}`);
    
    if(json.metas.error_code === "success") {
        // HTML: moves the item from the ground list to the bag list
        let itemNode = eventSubmitter.closest("li");
        document.querySelector('#items_ground ul').prepend(itemNode);
        // Adds 1 empty slot in the bag
        let tplEmptySlot = document.querySelector('#tplEmptySlot').content.cloneNode(true);
        document.querySelector('#bagbar .items_list').appendChild(tplEmptySlot);
        // Replaces the "drop" icon by the "pick-up" icon for this item
        itemNode.querySelector(".form_drop").classList.add("hidden");
        itemNode.querySelector(".form_pickup").classList.remove("hidden");
        // Hides the message "There are no items on the ground..."
        hide("#items_ground .greytext");
        // Increases the counter for the ground items
        let myZone = document.querySelector("#me").parentNode.dataset;
        myZone.items = parseInt(myZone.items, 10) + 1;
        document.querySelector("#round_dig .dot_number").innerHTML = myZone.items;
        
    } else {
        // Displays the eventual error message in a pop-up
        document.querySelector("#popsuccess").classList.add("force_visibility");
        document.querySelector("#popsuccess .content").innerHTML = json.metas.error_message;
    }
}


/**
 * Hides the pop-up over the map
 */
function closePopup() {
    
    document.querySelector("#popsuccess").classList.remove("force_visibility");
}


///**
// * Place the cities on the map
// * TODO: merge this function with replaceBuildingsPlaceholders(). Requires that
// * the desert buildings are treated as cities in the API and the database.
// * 
// */
//function replaceCitiesPlaceholders() {
//    
//    let zonesWithCity = document.querySelectorAll('#map [data-citytypeid]:not([data-citytypeid=""])');
//    
//    for(let zone of zonesWithCity) {
//        let buildingTypeId = zone.dataset.citytypeid;
//        let config = _configsBuildings[buildingTypeId];
//        // Adds the text in the bubble of the zone
//        zone.querySelector(".bubble .roleplay").innerHTML = '<h5 class="name">'+config.name+'</h5><hr>'
//                                                            +config.descr_ambiance;
//    }
//}


/**
 * Countdown to escape once the humans have lost the control of the zone
 */
function controlCountdown() {
    // Get the number of seconds from now to the end of the countdown
    let timestamp = document.getElementById("controlTimestamp").innerHTML,
        diff = (timestamp*1000-Date.now()) / 1000;
    diff = Math.max(0, diff);
    // Converts the difference to a manipulable date object    
    let date = new Date(1970, 0, 1);
    date.setSeconds(diff);
    
    document.getElementById("controlCountdown").innerHTML = date.getMinutes()+"mn "+date.getSeconds()+"s";
}


/**
 * Countdown before the midnight attack
 * Partially based on https://stackoverflow.com/questions/54256629/countdown-to-midnight-refresh-every-day/54257213
 * Feel free to optimize it...
 */
function attackCountdown() {
    
    // Get the number of seconds from now to midnight
    let now = new Date();
    let nextMidnight = new Date();
    nextMidnight.setHours(24,0,0,0);    
    let remainingSeconds = (nextMidnight.getTime() - now.getTime())/1000;
    
    // Convert the difference to a manipulable date object 
    let date = new Date(1970, 0, 1);
    date.setSeconds(remainingSeconds);
    
    document.getElementById("attackCountdown").innerHTML = date.getHours()+"h "+date.getMinutes()+"mn "+date.getSeconds()+"s";
}


/**
 * Updates the distance to the city displayed under the movement paddle
 * 
 * @param {int} citizenCoordX
 * @param {int} citizenCoordY
 */
async function updateCityDistance(citizenCoordX, citizenCoordY) {
    
    let myCityId = document.querySelector("#gameData #cityId").innerHTML,
        myCityNode = document.querySelector(`[data-cityid="${myCityId}"]`),
        distance = null;   
    
    if(myCityNode !== null) {
        let myCityZone = myCityNode.parentNode.querySelector(".square_container").dataset;
            distance = getDistance(myCityZone.coordx, myCityZone.coordy, citizenCoordX, citizenCoordY);
    }
    // Reduces the image of the city as we move away
    let biggestImageSize = 42,
        smallestImageSize = 16;
    document.querySelector("#block_distance img").height = Math.max(smallestImageSize, biggestImageSize-distance*2);
    // Updates the number of kilometers
    document.querySelector("#block_distance .distance").innerHTML = distance;
}


/**
 * Calculates the number of cells between a citizen and its city
 * 
 * @param {int} cityX    The X coordinate of the city
 * @param {int} cityY    The Y coordinate of the city
 * @param {int} citizenX The X coordinate of the citizen
 * @param {int} citizenY The Y coordinate of the citizen
 * @return {int} The number of cells between the citizen and the city
 */
function getDistance(cityX, cityY, citizenX, citizenY) {

    // We calculate the relative coordinates of the citizen as if the city was in [0:0].
    // And we remove the eventual negative sign, because the orientation (N/S/W/E)
    // has no influence on the distance.
    let distanceX = Math.abs(citizenX - cityX),
        distanceY = Math.abs(citizenY - cityY);

    // Formula provided by https://www.redblobgames.com/grids/hexagons/#distances
    return distanceY + Math.max(0, (distanceX-distanceY)/2);
}


/**
 * Generates the HTML of a citizen with his name in a chips + the actions buttons
 * (to heal, to attack...)
 * 
 * @param {dict} citizen The citzen's data, as returned by the Azimutant's API
 *                       (citizen_id, citizen_pseudo...) 
 * @param {boolean} bigChips Set to "true" to make bigger the chips of the user
 * @param {boolean} displayActionButtons Set to "false" if you want to hide the buttons 
 *                                    for healing/attacking the citizen (useful 
 *                                    when the citizen is not in the same zone)
 * @param {boolean} displayItsMe Set to "true" to display a mention "It's me"
 *                               next to the user chips
 * @returns {string} HTML
 */
function getHtmlActionBlockFellow(citizen, bigChips=false, displayActionButtons=true, displayItsMe=false) {
    
    // The model for the HTML is located in a <template> tag
    template = document.querySelector("#tplActionBlockFellow").content.cloneNode(true);
    
    // Populates the template with the citizen's data
    template.querySelector(".pseudo").innerHTML = citizen.citizen_pseudo;
    template.querySelector('form[name="attack"] input[name="params[target_id]"]').value = citizen.citizen_id;
    template.querySelector('form[name="heal"] input[name="params[target_id]"]').value = citizen.citizen_id;
    
    if(bigChips === true) {
        template.querySelector(".userListItem").classList.add("big");
    }
    if(displayItsMe === false) {
        template.querySelector('.itsMe').classList.add("hidden");
    }
    
    // Displays/hides the buttons attack/heal according to the wounds of the player
    if(displayActionButtons === false) {
        template.querySelector('.actionButtons').classList.add("hidden");
    }
    else if(citizen.is_wounded === 0) {
        template.querySelector('form[name="heal"]').classList.add("hidden");
    } else {
        template.querySelector('form[name="attack"]').classList.add("hidden");
    }
    
    return template;
}


/**
 * Generates the image of an item
 * 
 * @param {int} itemId The ID of the item, as returned by the "items" API
 * @param {int} height The dimensions to resize the image
 * @returns {String} HTML
 */
function image(itemId, height) {
    
    let icon_path   = _configsItems[itemId]["icon_path"],
        icon_symbol = _configsItems[itemId]["icon_symbol"];

    return (icon_path !== null) ? `<img src="resources/img/${icon_path}" alt="icon_symbol" height="${height}"  width="${height}">` : icon_symbol;
}


/**
 * Displays the stages constituting the midnight attack 
 * (kill the citizens outside, etc.)
 * 
 * @returns {undefined}
 */
async function displayMessageEndCycle() {

   let textZoneId = "messageEndCycle";
   let stages = [  {"icon": '&#127751;',
                    "descr": "Fin du cycle. Le soleil se couche..."},
                   {"icon": '&#127964;&#65039;',
                    "descr": "Mort des citoyens non-abrités..."},
                   {"icon": '&#129440;',
                    "descr": "Mort des citoyens blessés..."},
                   {"icon": '<img src="resources/img/motiontwin/zombie4.gif" alt="&#129503;">',
                            "descr": "Apparition de nouveaux zombies sur la carte..."},
                   {"icon": '<img src="resources/img/thirdparty/notoemoji/sunrise-512.webp" alt="&#127748;" height="52">',
                            "descr": "Une nouvelle journée commence ! Bonne chance..."}
                   ];

//   hide("#timer");
   display(`${textZoneId}`);

   for(let stage of stages) {
       document.getElementById(textZoneId).innerHTML = `<span style="font-size:2.6em;">${stage.icon}</span>${stage.descr}`;
       await sleep(1500);
   }
   await sleep(1500);

   hide(`#${textZoneId}`);
//   display("#timer");
}


/**
 * Highlights the features of the game, by using the "feature discovery" function 
 * of Materialize.css.
 * See their documentation: https://materializecss.com/feature-discovery.html
 * 
 */
function launchTutorial(elems, instances, step) {

    // ---- FIX FOR MATERIALIZE.CSS ----//
    // 
//    // Fix #1: missing image of the feature
//    // The "Feature discovery" of Materialize seems to have a big bug: if more than
//    // one feature is defined (with <div class="tap-target">), the image of the feature
//    // is not displayed, excepted for the first feature. Cause: it seems like 
//    // Materialize fails to inject a required piece of HTML. The code below adds it. 
//    
//    // The missing bunch of HTML that contains the image of the feature, and that
//    // Materialize.css mysteriously doesn't insert.
//    let tapTargetWaveNode = document.querySelector("#tplTapTargetWave").content.cloneNode(true);
//    // Clones the HTML of image of the feature (ex: the round button for digging)
//    let tapTargetNode = document.querySelector(`#${elems[step].dataset.target}`).cloneNode(true);
//    // Add the missing bunch of HTML after the text of the feature 
//    // (= after the appropriate .tap-target)
//    elems[step].appendChild(tapTargetWaveNode);
//    // Add the image of the feature inside the newly inserted HTML
//    elems[step].querySelector(".tap-target-origin").appendChild(tapTargetNode);
//    
//    // Fix #2 (part 2/2): the features placed in absolute-positioned block enlarge the page
//    // For the features we have marked as needing the fix (marked by the homemade "fix-position" class),
//    // go back to the .tap-target-wrapper parent (created on-the-fly by Materialize)
//    // and modify its position to keep it inside the width of the page.
//    if(elems[step].querySelector(".tap-target-origin").parentNode.parentNode.classList.contains("fix-position") === true) {
//        let tapTargetWrapper = elems[step].querySelector(".tap-target-origin").parentNode.parentNode.parentNode;
//        tapTargetWrapper.style.left = 0;
//        // Fix #3: the feature placed in absolute-positioned block shift the attack bar
//        // out of the screen
//        // Remove the "abolute position", because it shifts the attack bar
//        // (normally fixed at the bottom) ou of the screen
//        tapTargetWrapper.style.position = "";
//    }    
    //
    // ---- END OF THE FIX ----//

    // Open the Materialize's feature highlighter
    // NB: this condition ensures that the user can't open simultaneous occurrences
    // of the tutorial
    if((step === 0 && instances[0].isOpen === false) || step !== 0) {
        instances[step].open();
    }
    // When the users closes a feature, open the next one
    instances[step].options.onClose = function() {
        // Remove the fix previously added, to avoid cumultating multiple occurrences of it
        // if the user reloads tutorial later
//        elems[step].querySelector(".tap-target-origin").remove();
        // Open the next step of the tutorial
        if(step+1 < instances.length) {
            launchTutorial(elems, instances, step+1);
        }
    };
}


/**
 * Calculate the number of action points of the citizens in a zone
 * 
 * @param {array} citizens The datas of the "citizens" API
 * @param {int} mapId
 * @param {int} coordX
 * @param {int} coordY
 */
async function sumControlpoints(citizens, mapId, coordX, coordY) {
    
    var sumControlpoints = 0;
    
    for(let citizen of Object.values(citizens)) {
        if(citizen.coord_x === parseInt(coordX) && citizen.coord_y === parseInt(coordY)) {
            sumControlpoints += citizen.control_points;
        }
    }

    return sumControlpoints;
}


/**
 * Updates the content of the block descriptibing the type of land,
 * under the movement paddle.
 * 
 * @param {string} landType The alias of the type of land (peeble, sand, water...)
 * @returns {undefined}
 */
function updateBlockLandType(landType) {
    
    let lands = {
        "peeble": {"name": "Cailloux épars",
                   "descr": "La présence de ces cailloux indique qu'un bâtiment\n"
                            +"se trouve non loin, dans un rayon de 2 zones.\n"
                            +"Poursuivez vos recherches..."
                   },
        "bigpeeble": {"name": "Rocaille",
                      "descr": "On trouve généralement un bâtiment dans une zone\n"
                               +"adjacente à ce type de terrain. Encore un effort..."
                      }
        };

    document.querySelector("#block_landtype").innerHTML = (lands[landType] !== undefined ? '<span title="'+lands[landType]["descr"]+'">&#128065;&#65039; '+lands[landType]["name"]+'</span>' : '');
}


/**
 * To control a bot citizen
 * 
 * @param {int} targetCitizenId the ID of the citizen you want to take control over.
 * @returns {undefined}
 */
async function switchToCitizen(targetCitizenId) {
    
    let token = getCookie('token');
    let json = await callApi("GET", "me", `action=switch_citizen&target_id=${targetCitizenId}&token=${token}`);
    
    // Update the cookie to write the token corresponding to the now-controlled citizen 
    if(json.metas.error_code === "success") {
        setCookie("token", json.datas.token);
    }
    
    displayToast(json.metas.error_message, json.metas.error_class);
}


/**
 * Short way to display a toast with Materialize.css
 * 
 * @param {string} message The text of the message to put in the toast
 * @param {type} error_class One of the standardized class returned by the InvZion's API
 *                           ("info", "warning", "critical"). This allow changing 
 *                           the background of the toast according to the gravity 
 *                           of the alert.
 * @returns {undefined}
 */
function displayToast(message, error_class) {
    
    M.toast({
        html: message,
        classes: error_class,
        displayLength: 2500,
        outDuration: 800
        });
}


/**
 * Add the data in the pop-up detailing the sources of the city defenses
 * @returns {undefined}
 */ 
function populateDefensesDetails() {
    
    let popup = document.querySelector("#popdefenses");
    let defenses = popup.querySelector(".defenses_list");
    
//    defenses.querySelector(".zombies").innerText = nbrZombiesInZone;
//    defenses.querySelector(".attack_details .zombies").innerText = nbrZombiesInZone;
//    defenses.querySelector(".controlpoints_citizens").innerText = controlPointsCitizens;
}
