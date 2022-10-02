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
        
        let mapId = document.querySelector("#mapId").innerHTML,
            myZone = document.querySelector("#me").parentNode.dataset;
        updateBlockActionDig(mapId, myZone.coordx, myZone.coordy); 
    }
}


/**
 * Update the content of the action block "Move"
 * @param {int} newNbrZombies The number of zombies in the zone after the action
 */
function updateBlockActionMove(newNbrZombies) {

    // Update the red alert frame above the movement paddle
    let alertControlNbrZombies = document.querySelector("#alert_control .nbr_zombies");
    if(alertControlNbrZombies !== null) {
        alertControlNbrZombies.innerHTML = newNbrZombies;
        if(newNbrZombies <= 0) {
            hideIds("alert_control");
        }
    }
    
    // Update the details about movement cost (action points)
    if(newNbrZombies <= 0) {
        document.querySelector("#movement_cost").innerHTML = '<span style="font-size:0.85em">Déplacement gratuit<br>(aucun zombie alentour)';
    }
}


/**
 * Update the HTML displaying the action points after consuming AP
 * 
 * @param {int} actionsPointsLost The amount of AP to decrease
 */
function updateActionPointsBar(actionsPointsLost) {
    // Update the number of AP in the hidden data storage
    document.querySelector("#actionPoints").innerHTML -= actionsPointsLost;
    
    let currentAP   = document.querySelector("#actionPoints").innerHTML,
        maxAP       = document.querySelector("#maxActionPoints").innerHTML;
    
    let htmlCurrentAP = '&#x26A1'.repeat(currentAP),
        htmlConsumedAP  = '<span style="opacity:0.3">'+('&#x26A1;'.repeat(maxAP-currentAP))+'</span>';
    
    // Update the HTML gauge displaying the number of action points
    document.querySelector("#apBar").innerHTML = htmlCurrentAP + htmlConsumedAP;
}


/**
 * Display an alert over the movement paddle if the player is blocked
 * 
 * @param {int} nbrZombies The number of zombies in the player's zone
 */
function updateBlockAlertControl(nbrZombies) {
    
    document.querySelector("#alert_control").style.display = (nbrZombies > 0) ? "block" : "none";
}


/**
 * Toggles the digging button to active/inactive if the player can't dig here
 * 
 * @param {int} is_visited_today Values "1" if the player has already visited 
 *                               the zone today(comes from the Invazion's API) 
 */
function updateDigButton(is_visited_today) {
    
    let digButton = document.querySelector('#block_dig form[name="dig"] .redbutton');
    
    if(is_visited_today === 1) {
        digButton.classList.add("inactive");
    } else {
        digButton.classList.remove("inactive");
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
        document.querySelector("#action_zombies").style.display = "block";
    } else {
        // If there is no more zombies, hide the buttons & infos about killing zombies
        document.querySelector("#action_zombies").style.display = "none";
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
            
        // Get the citizens of the map by calling the Invazion's API
        _citizens = await getMapCitizensOnce(mapId);    
        
        // Keep only the citizens who are in the player's zone
        zoneCitizens = Object.values(_citizens).filter(citizen => citizen.coord_x == coordX 
                                                               && citizen.coord_y == coordY
                                                               && citizen.citizen_id != myCitizenId);
        
        if(zoneCitizens.length <= 0) {
            // If the conected player is alone, show a generic text
            document.querySelector("#block_citizens .greytext").style.display = "block";
            block.innerHTML = "";
            
        } else {
            // Hide the generic text
            document.querySelector("#block_citizens .greytext").style.display = "none";
            // Add the player's pseudo at the top of the list of citizens
            let template = document.querySelector("#tplActionBlockFellowMe").content.cloneNode(true);
            template.querySelector(".pseudo").innerHTML = myCitizenPseudo;
            document.querySelector("#block_citizens ol").appendChild(template);
        }
        
        // Show the list of the other citizens in the zone
        for(let i in zoneCitizens) {
            
            let citizen = zoneCitizens[i],
                template = document.querySelector("#tplActionBlockFellow").content.cloneNode(true);
                
            // Populate the template with the citizen's data
            template.querySelector(".pseudo").innerHTML = citizen.citizen_pseudo;
            template.querySelector('form[name="attack"] input[name="params[target_id]"]').value = citizen.citizen_id;
            template.querySelector('form[name="heal"] input[name="params[target_id]"]').value = citizen.citizen_id;
            
            // Switch buttons attack/heal according to the wounds of the player
            if(citizen.is_wounded === 0) {
                template.querySelector('form[name="heal"]').style.display = "none";
            } else {
                template.querySelector('form[name="attack"]').style.display = "none";
            }
            
            // Add the new template to the list of citiziens
            document.querySelector("#block_citizens ol").appendChild(template);
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
    
    let block = document.querySelector('#block_dig form[name="items_ground"] .items_list');
    
    // Update the data only one time per zone
    if(block.dataset.coordx !== coordX || block.dataset.coordy !== coordY) {
    
        let noItemsText = document.querySelector('#block_dig form[name="items_ground"] .greytext');
        // Clear the obsolete items list from the previous zone
        block.innerHTML = "";        
        // Get the items in the zone by calling the Invazion's API
        _myZone = await getMyZoneOnce(mapId, coordX, coordY);        
        // Set the digging button to grey if the player can't dig
        updateDigButton(_myZone.user_specific.is_visited_today);
        
        if(_myZone.items === null) {
            // Show the default text if no items on the ground
            noItemsText.style.display = "block";
        }
        else {
            // Hide the default text if there are items
            noItemsText.style.display = "none";
            
            for(let [itemId, itemAmount] of Object.entries(_myZone.items)) {
                // Adds the item in the items list
                let item = _configsItems[itemId];
                htmlAddGroundItem(itemId, item.icon_symbol, item.name, itemAmount);
            }
        }
        
        // Useful to know if the block is up-to-date after moving the player
        block.dataset.coordx = coordX;
        block.dataset.coordy = coordY;
    }
}


/**
 * Adds an HTML entry in the ground items list
 * 
 * @param {int} itemId The ID of the item in the game (can't be your homemade ID)
 * @param {string} itemIconSymbol An HTML entity if there is no real image for the item
 * @param {string} itemName
 * @param {int} itemAmount The number of occurrences of this item in the zone
 */
function htmlAddGroundItem(itemId, itemIconSymbol, itemName, itemAmount) {
    
    // Gets a blank HTML template of an item entry
    let template = document.querySelector("#tplActionBlockItem").content.cloneNode(true),
        block = document.querySelector('#block_dig form[name="items_ground"] .items_list');

    // Populates the blank template with the item data
    template.querySelector('button[name="params[item_id]"]').value = itemId;
    template.querySelector('img').src = `../resources/img/copyrighted/items/${itemId}.png`;
    template.querySelector('img').alt = itemIconSymbol;
    template.querySelector('.item_name').innerHTML = itemName;
    template.querySelector('.item_amount').innerHTML = itemAmount;
    
    // Adds the new template to the list of items
    block.prepend(template);
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
        document.querySelector(`#${roundButtonId} .dot_number`).style.display = "none";
    } else {
        document.querySelector(`#${roundButtonId} .dot_number`).style.display = "block";
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