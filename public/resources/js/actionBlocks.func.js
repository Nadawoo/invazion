/* 
 * Functions about the "action blocks" next to the map (the big round buttons :
 * move, items, zombies, humans, building)
 */


/**
 * Main function : pdate the content of the actions blocks next to the map 
 * (move, items, zombies...)
 * 
 * @param {string} blockAlias The name of the action block to update 
 */
async function updateBlockAction(blockAlias) {
    
    if(blockAlias === "citizens") {
        
        let myZone = document.querySelector("#me").parentNode.dataset;
        updateBlockActionCitizens(myZone.coordx, myZone.coordy);
    }
    else if(blockAlias === "zombies") {
        
        let nbrZombies = document.querySelector("#me").parentNode.dataset.zombies; 
        updateBlockActionZombies(nbrZombies); 
    }
    else if(blockAlias === "dig") {
        
        let mapId = Number(document.querySelector("#mapId").innerHTML),
            coordX = Number(document.querySelector("#citizenCoordX").innerHTML),
            coordY = Number(document.querySelector("#citizenCoordY").innerHTML);
        updateBlockActionDig(mapId, coordX, coordY); 
    }
}


async function updateActionBlocks() {
    
    // Get informations about the current zone through the "data-*" HTML attributes
    let zoneData = await document.querySelector("#me").parentNode.dataset;

    // Highlights the player's location on page load
//        let myHexagon = document.getElementById("me").closest(".hexagon");
//        displayTooltip(myHexagon);
    // Updates the coordinates of the player in the movement paddle
    updateMovementPaddle(zoneData.coordx, zoneData.coordy);
    // Updates the cards of contextual actions under the movement paddle
    updateMoveCost(parseInt(zoneData.zombies));
    updateCardCitizensInZone(parseInt(zoneData.citizens));
    // Updates the distance to the city displayed under the movement paddle
    updateCityDistance(zoneData.coordx, zoneData.coordy);     
    // Displays the button to enter if there is a city in the zone
    setTimeout(function() { updateEnterBuildingButton(zoneData.citytypeid, zoneData.zombies); }, 1000);
    // Updates the coordinates of the player in the land editor
    updateMapEditor(zoneData.coordx, zoneData.coordy);
    // Update the numbers in the big buttons next to the map
    updateRoundActionButtons(zoneData.coordx, zoneData.coordy);
    // Display an alert over the movement paddle if the player is blocked
    updateBlockAlertControl(Number(zoneData.controlpointszombies), mapId, zoneData.coordx, zoneData.coordy);
    // Display the actions for fighting against zombies
    showFightingZombiesButtons(zoneData.zombies);
    // Displays help about the land type of the current zone
//    updateBlockLandType(zoneData.landtype);
    // Update the cursor indicating the control of the zone
    updateZombiesGauge(zoneData.zombies);
}


/**
 * Updates the cost (in action points) for leaving the zone
 * @param {int} newNbrZombies The number of zombies in the zone after the action
 */
function updateMoveCost(newNbrZombies) {
    
    // Updates the block showing the AP before=>after moving
    let currentAp = document.querySelector("#actionPoints").innerHTML,
        ApAfterMove = currentAp - 1;    
    document.querySelector("#card_ap_cost .actionspoints_decrease").innerHTML = currentAp+"&#x2794;"+ApAfterMove+"&#9889;";
    
    // The movement has no AP cost in some situations => hide the card under 
    // the movement paddle
    if(newNbrZombies === 0 && parseInt(_configsMap.moving_cost_no_zombies) === 0) {
        hide("#card_ap_cost");
    }
    else if(newNbrZombies >= 1 && parseInt(_configsMap.moving_cost_zombies) === 0) {
        hide("#card_ap_cost");
    }
    else if(ApAfterMove < 0) {
        hide("#card_ap_cost");
    }
}


/**
 * Update the HTML displaying the action points after consuming AP
 * 
 * @param {int} actionsPointsLost The amount of AP to decrease
 */
//function updateActionPointsBar(actionsPointsLost) {
//    // Update the number of AP in the hidden data storage
//    document.querySelector("#actionPoints").innerHTML -= actionsPointsLost;
//    
//    let currentAP   = document.querySelector("#actionPoints").innerHTML,
//        maxAP       = document.querySelector("#maxActionPoints").innerHTML;
//    
//    let htmlCurrentAP = '&#x26A1'.repeat(currentAP),
//        htmlConsumedAP  = '<span style="opacity:0.3">'+('&#x26A1;'.repeat(Math.max(0, maxAP-currentAP)))+'</span>';
//    
//    // Update the HTML gauge displaying the number of action points
//    document.querySelector("#apBar").innerHTML = htmlCurrentAP + htmlConsumedAP;
//}


/**
 * Display an alert over the movement paddle if the player is blocked
 * 
 * @param {int} controlpointsZombies  The sum of control points of the zombies in the zone
 */
async function updateBlockAlertControl(controlpointsZombies, mapId, coordX, coordY) {
    
    let actionPoints = Number(document.querySelector("#gameData #actionPoints").innerHTML);
    let controlpointsCitizens = await sumControlpoints(await _citizens, mapId, coordX, coordY);
    
    // Displays an alert when the player has not enough action points to  move
    if ((   controlpointsZombies === 0 && actionPoints < _configsMap.moving_cost_no_zombies)
        || (controlpointsZombies === 0 && actionPoints === 0 && _configsMap.moving_cost_no_zombies > 0)
        || (controlpointsZombies   > 0 && actionPoints < _configsMap.moving_cost_zombies)
        ) {
        // Display the alert text above the movement paddle
        display("#alert_tired");
        // Turn to red the halo under the player on the map
        document.querySelector("#me").classList.add("alert");
        document.querySelector(".halo").classList.add("alert");
    } else {
        hide("#alert_tired");
        document.querySelector("#me").classList.remove("alert");
        document.querySelector(".halo").classList.remove("alert");
    }
    
    // Displays an alert when the citizens have less control points than the zombies on the zone
    (controlpointsCitizens >= controlpointsZombies) ? hide("#alert_control") : display("#alert_control");
}


/**
 * Update the cursor indicating the control of the zone
 * 
 * @param {int} nbrZombies
 * @returns {undefined}
 */
function updateZombiesGauge(nbrZombies) {
    
    let oldActiveNode = document.querySelector(`#zombies_gauge li[class="active"]`);
    let newActiveNode = document.querySelector(`#zombies_gauge li[aria-label="${nbrZombies}"]`);
    let lostControlNode = document.querySelector(`#zombies_gauge li:last-child`);
    
    // Desactivate the previously highlighted number of zombies
    if(oldActiveNode !== null) {
        oldActiveNode.classList.remove("active");
        lostControlNode.querySelector(".nbr_zombies").innerText = "";
    }
    
    // Highlight the new number of zombies
    if(newActiveNode !== null) {
        newActiveNode.classList.add("active");
    } else {
        lostControlNode.classList.add("active");
        lostControlNode.querySelector(".nbr_zombies").innerText = nbrZombies;
    }
}


/**
 * Toggles the digging button to active/inactive if the player can't dig here
 * 
 * @param {int} is_visited_today Values "1" if the player has already visited 
 *                               the zone today(comes from the Azimutant's API) 
 */
function updateDigButtons(is_visited_today) {
    
    let digButton = document.querySelector('#block_dig form[name="dig"] .redbutton');
    
    if(is_visited_today === 1) {
        digButton.classList.add("inactive");
        hide("#block_move #card_dig");
    } else {
        digButton.classList.remove("inactive");
        display("#block_move #card_dig");
    }  
}


/**
 * Update the content of the action block "Zombies" 
 * @param {int} newNbrZombies The number of zombies in the zone after the action
 */
function updateBlockActionZombies(newNbrZombies) {
    
    // Activates the big round action button "Zombies"
    let zombiesButton = document.querySelector("#round_zombies");
    if(newNbrZombies > 0) {
        zombiesButton.querySelector("input").classList.remove("inactive");
    } else {
        zombiesButton.querySelector("input").classList.add("inactive");
    }
    
    // Update the number of zombies in the round button
    document.querySelector("#round_zombies .dot_number").innerHTML = newNbrZombies;
    
    // Update the content of the "zombies" frame
    if(newNbrZombies > 0) {
        document.querySelector("#block_zombies .nbr_zombies").innerHTML = newNbrZombies+" zombies";
        document.querySelector("#block_zombies .zombies_visual").innerHTML = '<span class="zombie">&#x1F9DF;</span>'.repeat(newNbrZombies);
        display("#action_zombies");
    } else {
        // If there is no more zombies, hide the buttons & infos about killing zombies
        hide("#action_zombies");
    }
}


/**
 * Update the content of the "humans" block next to the map
 * 
 * @param {int} coordX
 * @param {int} coordY
 */
async function updateBlockActionCitizens(coordX, coordY) {
    
    let block = document.querySelector("#block_citizens .citizens");
    
    // Update the data only one time per zone
    if(block.dataset.coordx !== coordX || block.dataset.coordy !== coordY) {
        
        let myCitizenId     = document.querySelector("#citizenId").innerHTML,
            myCitizenPseudo = document.querySelector("#citizenPseudo").innerHTML,
            mapId           = document.querySelector("#mapId").innerHTML;
            
        // Get the citizens of the map by calling the Azimutant's API
        _citizens = await getMapCitizensOnce(mapId);    
        
        // Keeps only the citizens who are in the player's zone
        citizensInMyZone = Object.values(_citizens).filter(citizen => citizen.coord_x == coordX 
                                                                    && citizen.coord_y == coordY
                                                                    && citizen.citizen_id != myCitizenId);
        // All the other citizens (not in my zone)
        citizensInOtherZones = Object.values(_citizens).filter(citizen => citizen.coord_x != coordX 
                                                                        && citizen.coord_y != coordY
                                                                        && citizen.citizen_id != myCitizenId);
        
        // Shows me in the list of the citizens
        if(citizensInMyZone.length <= 0) {
            // If the connected player is alone, show a generic text
            display("#block_citizens .greytext");
            block.innerHTML = "";
            
        } else {
            // Hide the generic text
            hide("#block_citizens .greytext");
            // Add the player's pseudo at the top of the list of citizens
            template = getHtmlActionBlockFellow(_citizens[myCitizenId], bigChips=true, displayActionButtons=false, displayItsMe=true);
            document.querySelector("#block_citizens #citizensInMyZone").appendChild(template);
        }
        
        // Shows the list of the other citizens in my zone
        for(let i in citizensInMyZone) {
            template = getHtmlActionBlockFellow(citizensInMyZone[i], bigChips=true);
            document.querySelector("#block_citizens #citizensInMyZone").appendChild(template);
        }
        
        // Shows the list of the other citizens in the zone
        document.querySelector("#block_citizens #citizensInOtherZones").innerHTML = "";
        for(let i in citizensInOtherZones) {
            template = getHtmlActionBlockFellow(citizensInOtherZones[i], bigChips=false, displayActionButtons=false);
            document.querySelector("#block_citizens #citizensInOtherZones").appendChild(template);
        }
        
        // Useful to know if the block is up-to-date after moving the player
        block.dataset.coordx = coordX;
        block.dataset.coordy = coordY;
    }
}


/**
 * Update the content of the "dig" block next to the map
 * 
 * @param {int} mapId
 * @param {int} coordX
 * @param {int} coordY
 */
async function updateBlockActionDig(mapId, coordX, coordY) {
    
    let block = document.querySelector("#items_ground .items_list");
    
    // Update the data only one time per zone
    if(Number(block.dataset.coordx) !== coordX
        || Number(block.dataset.coordy) !== coordY
        || block.innerHTML.length === 0
        ) {        
        // Clear the obsolete items list from the previous zone
        block.innerHTML = "";        
        // Get the items in the zone by calling the Azimutant's API
        _myZone = await getMyZoneOnce(mapId, coordX, coordY);
        // Set the digging button to grey if the player can't dig
        updateDigButtons(await _myZone.user_specific.is_visited_today);
        
        if(_myZone.items.length === 0) {
            // Show the default text if no items on the ground
            display("#items_ground .greytext");
        }
        else {
            // Hide the default text if there are items
            hide("#items_ground .greytext");
            populateItemsList("#items_ground .items_list", _myZone.items);
        }
        
        // Useful to know if the block is up-to-date after moving the player
        block.dataset.coordx = coordX;
        block.dataset.coordy = coordY;
    }
}


/**
 * Writes the HTML for the items in the bag or in the ground.
 * 
 * @param {string} domSelector The path in the DOM where to add the items
 * @param {array} itemsAmounts The list of items. Each item is a pair {itemId=>itemAmount}
 * @param {int} nbrSlots The total number of available slots
 * @returns {undefined}
 */
function populateItemsList(domSelector, itemsAmounts, nbrSlots=null) {
    
    let nbrItemsTotal = 0;
    
    for(let [itemId, itemAmount] of Object.entries(itemsAmounts)) {
        // Adds the item in the items list
        let item_caracs = _configsItems[itemId];
        for(let i=0; i<itemAmount; i++) {
            htmlAddGroundItem(domSelector, itemId, item_caracs);
        }
        nbrItemsTotal += itemAmount;
    }
    
    // Add the empty slots in the bag
    if(nbrSlots !== null) {
        for(let i=0; i<(nbrSlots-nbrItemsTotal); i++) {
            let tplEmptySlot = document.querySelector("#tplEmptySlot").content.cloneNode(true);
            document.querySelector("#bagbar .items_list").appendChild(tplEmptySlot);
        }
    }
}


/**
 * Adds an HTML entry in the ground items list
 * 
 * @param {string} domSelector The path in the DOM where to add the items
 * @param {int} itemId The ID of the item in the game (can't be your homemade ID)
 * @param {array} itemCaracs The caracteristics of the item, as return 
 *                           by the "item" API (name, description...)
 */
function htmlAddGroundItem(domSelector, itemId, itemCaracs) {
    
    document.querySelector(domSelector).prepend(htmlItem(itemId, itemCaracs));
}


/**
 * Builds the complete HTML to display an item (icon inside a square and with 
 * an explicative pop-up when clicking on it)
 * 
 * @param {int} itemId
 * @param {array} itemCaracs The characteristics of the given item, 
 *                           as returned by the "items" API
 * @returns {htmlItem.template} Document fragment containing the HTML of the item.
 *              innerText doesn't work with fragments, insert in the page like this:
 *              document.querySelector("#myDiv").prepend(htmlitem())
 */
function htmlItem(itemId, itemCaracs) {
    
    // Default values for the items. Useful if an item existing in the database
    // but is not in the list of the items of the current game.
    if(itemCaracs === undefined) {
        itemCaracs = {
            "name":"(Objet inconnu)",
            "icon_path":null,
            "icon_symbol":`<span class="red">#${itemId}</span>`,
            "descr_ambiance":"",
            "descr_purpose":"[Bug] Cet objet est inconnu.\
                             Signalez-le au responsable du jeu."
        };
    }
    
    // Gets a blank HTML template of an item entry
    let template = document.querySelector("#tplItem").content.cloneNode(true);
    let icon = getItemIcon(itemCaracs['icon_path'], itemCaracs['icon_symbol']),
        itemTypeClass = null,
        bgColor = "";
    
    if(itemCaracs["item_type"] === "resource" || itemCaracs["item_type"] === "resource_rare") {
        itemTypeClass = "type_resource";
        bgColor = "#7FB3D5";
    } else if(itemCaracs["item_type"] === "weapon") {
        itemTypeClass = "type_weapon";
        bgColor = "#EC7063";
    } else if(itemCaracs["item_type"] === "food" || itemCaracs["item_type"] === "water") {
        itemTypeClass = "type_booster";
        bgColor = "orange";
    }
//    else if(itemCaracs["item_type"] === "tool") {
//        bgColor = "#27AE60"; 
//    }
    
    // Populates the blank template with the item data
    template.querySelector('.item_label').style.background = `radial-gradient(white 0%, ${bgColor} 100%)`;
    template.querySelector('.form_drop button[name="params[item_id]"]').value  = itemId;
    template.querySelector('.form_pickup button[name="params[item_id]"]').value = itemId;
    template.querySelector('.icon').innerHTML = icon;
    template.querySelector('.details .icon').innerHTML = icon;
    template.querySelector('.item_name').innerHTML = itemCaracs['name'];
    template.querySelector('.descr_ambiance').innerHTML = itemCaracs['descr_ambiance'];
    template.querySelector('.descr_purpose').innerHTML  = itemCaracs['descr_purpose'];
//    template.querySelector('.item_amount').innerHTML = itemAmount;

    // Additionally, if the item is rare (no matter its type), add a gold frame
    if(itemCaracs["preciousness"] > 0) {
        template.querySelector('.item_label').classList.add("precious");
        template.querySelector('.item_label .preciousness').classList.remove("hidden");
    }
    // Display the type of item (resource, weapon...)
    if(itemTypeClass !== null) {
        template.querySelector(`.item_label .${itemTypeClass}`).classList.remove("hidden");
    }
    // Display if the item is heavy
    if(itemCaracs["heaviness"] > 0) {
        template.querySelector('.item_label .heaviness').classList.remove("hidden");
    }
    // Display the button to use the item as a weapon
    if(itemCaracs["item_type"] === "weapon") {
        template.querySelector("form[name='fight'] input[name='params[item_id]']").value = itemId;
    }
    
    return template;
}


/**
 * Generates the icon for an item
 * 
 * @param {string} iconPath The path to the image (PNG, GIF...), 
 *                         as returned by the "configs" API of Azimutant
 * @param {string} iconSymbol The code for the HTML icon (&#...), 
 *                          as returned by the "configs" API of Azimutant
 * @returns {string} HTML for the icon (<img> tag HTML symbol)
 */
function getItemIcon(iconPath, iconSymbol) {
    
    if(iconPath !== null) {
        // If an image file is set (PNG, GIF, display it as icon
        return `<img src="../resources/img/${iconPath}" alt="${iconSymbol}">`;
    }
    else if(iconSymbol !== null) {
        // If there is no file but a HTML symbol, display it as icon
        return iconSymbol;
    }
    else {
        // If nothng is set, display a "?" as icon
        return "&#10067;";
    }
}


/**
 * Refresh the numbers in the big round buttons next to the map
 * (move, zombies, humans...)
 * 
 * @param {int} coordX The X coordinate of the zone to get. If not set, will be 
 *                     by default the player's zone.
 * @param {int} coordY The Y coordinate of the zone to get. Ignored if coordX 
 *                     was not set.
 */
function updateRoundActionButtons(coordX=null, coordY=null) {
    
    // NB: forcing the X/Y coordinates is useful to get the data of the good zone 
    // (= the landing zone) after moving.
    let myZone = (coordX === null)
                  ? document.querySelector("#me").parentNode.dataset
                  : document.querySelector("#zone"+coordX+"_"+coordY+" .square_container").dataset;
    
    // Displays the number of citizens in the zone (current player excepted)
    updateRoundButtonDotNumber("round_citizens", myZone.citizens - 1);
    // Displays the number of items in the round button
    updateRoundButtonDotNumber("round_dig", myZone.items);    
    // Displays the number of zombies in the round button
    updateRoundButtonDotNumber("round_zombies", myZone.zombies);    
    // Displays "1" if there is a building or a city in the zone
    updateRoundButtonDotNumber("round_build", 0, true);
}


/**
 * Updates the number inside the dot next to a round action button
 * 
 * 
 * @param {string} roundButtonId The HTML ID of the round button to update
 * @param {int} amount The amount of stuff (zombies, citizens... depends on 
 *                     the function of the button)
 * @param {bool} forceHighlight Set to TRUE to make the button always active,
 *                              no matter the "amount" paramter
 */
function updateRoundButtonDotNumber(roundButtonId, amount, forceHighlight=false) {
    
    if(amount == 0) {
        // Don't show the dot number if 0 stuff in the zone
        hide(`#${roundButtonId} .dot_number`);
    } else {
        display(`#${roundButtonId} .dot_number`);
        document.querySelector(`#${roundButtonId} .dot_number`).innerHTML = amount;
    }
    
    // Highlights the big round button if there is stuff in the zone
    if(amount >= 1 || forceHighlight === true) {
        document.querySelector(`#${roundButtonId} input`).classList.remove("inactive");
    } else {
        document.querySelector(`#${roundButtonId} input`).classList.add("inactive");
    }
}


/**
 * Updates the movement paddle next to the map
 * 
 * @param {int} coordX The X coordinate of the player
 * @param {int} coordY The Y coordinate of the player
 */
function updateMovementPaddle(coordX, coordY) {
    
    // Updates the coordinates of the player in the movement paddle
    document.querySelector('form[name="move"] .coords').innerHTML = coordX+":"+coordY;
}


/**
 * Updates the map editor next to the map
 * 
 * @param {int} coordX The X coordinate of the player
 * @param {int} coordY The Y coordinate of the player
 */
function updateMapEditor(coordX, coordY) {
    
    // Sets the current coordinates of the player as the default zone to edit
    document.querySelector('#landform input[name="coord_x"]').value = coordX;
    document.querySelector('#landform input[name="coord_y"]').value = coordY;
}


function moveBuildingBlockBelowPaddle() {
    
    document.querySelector("#card_building").classList.remove("above_paddle");
    toggle("#card_building .ignore_button");
    display(["#card_dig", "#card_ap_cost", "#card_citizens"]);
}
