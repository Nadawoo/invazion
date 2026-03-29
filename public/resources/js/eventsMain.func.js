/*
 * This script gathers all the functions related to the event listeners.
 * Put only functions here, no immediatly executable code.
 */


/**
 * Listen to all the forms (avoids creating one event listenener per form)
 * 
 * @returns {undefined}
 */
function listenToForms() {

    document.addEventListener("submit", (event) => {      
        const formSelectors = {
            "dig":"#block_dig form[name=dig]",
            "explore": "form[name=explore_building]"
            };
            
        // Execute the "Explore building" action
        if(event.target.matches(formSelectors.explore)) {
            // Desactivate the classic submission button (avoids reloading the page)
            event.preventDefault();
            exploreBuilding();
        }
        else if(event.target.matches(formSelectors.dig)) {
            event.preventDefault();
            dig();
        }
    });
}


/**
 * Listen to all the "click" events
 * (avoids creating one event listener per button)
 * 
 * @returns {undefined}
 */
function listenToClick() {
    
    document.addEventListener("click", (event) => {
        
        const selectors = {
            "buildCity":"#builder button[name=build_city]",
            "teleport": "button[name=teleport]"
            };
        
        let hexagon = event.target.closest(".hexagon"),
            button = null;
        
        // Build a city on the map (not a construction inside a city)
        if(button = event.target.closest(selectors.buildCity)) {
            buildOnMap(Number(button.dataset.citytypeid));
        }
        else if(event.target.matches(selectors.teleport) === true) {
            // If we click on a teleportation button over a city, teleport the citizen
            let cityId = Number(event.target.closest(".square_container").dataset.cityid);
            teleportToCity(cityId);
        }
        else if(hexagon && hexagon.querySelector(".square_container").dataset.citytypeid !== "") {
            // If we click on a city, open the city pop-up
            let buildingPopup = new BuildingPopup();
            buildingPopup.openBuildingPopup(event);
        }
        else if(hexagon) {
            // Display the red tooltip above the zone
            triggerTooltip(hexagon);
        }
    },
    { passive: true }
    );
}


/**
 * Listen to all the "pointerdown" events
 * (avoids creating one event listener per button)
 * 
 * @returns {undefined}
 */
function listenToPointerdown() {
    
    // Close the radial menu when tapping anywhere out of the active hexagon
    document.addEventListener("pointerdown", (event) => {
        if(event.pointerType !== "touch") return;

        const radialMenu = new cityRadialMenu();
        const hexagon = event.target.closest(".hexagon");

        // Hide the road and close the previously open radial menu
        if(_roadActiveHexagon && _roadActiveHexagon !== hexagon) {
            radialMenu.close(_elementsToHideInRoadView);
        }
    },
    { passive: true }
    );
}


/**
 * Events concerning the form to create a new discussion.
 * Must be asynchronous because the form doesn't exist when the page loads.
 */
function listenToSendform() {
    // Create a new discussion thread
    document.getElementById("buttonNewTopic").addEventListener("click", function(event) {
        toggleSendform(event);
    });
    document.getElementById("hideSendform").addEventListener("click", function(event) {
        toggleSendform(event);
    });
    document.getElementById("sendform").addEventListener("submit", function(event) {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        createDiscussion();
    });

    // Clear the error messages if the user writes in the form
    document.getElementById("sendform").addEventListener("input", function() {
         document.getElementById("errorNewTopicPseudo").innerHTML  = "";
         document.getElementById("errorNewTopicMessage").innerHTML = "";
    });
}


/**
 * Allows to move the map by dragging it with the mouse
 * 
 * @returns {ScrollBooster|listenToMapDragging.sb}
 */
function listenToMapDragging() {
    // Warning: don't set the "viewport" on #map, otherwise the floating blocks
    // disposed inside #map (connection block, action blocks, navigation...)
    // won't be tappable on mobile.
    const viewport = document.querySelector('#map_viewport');
    const content = document.querySelector('#map_body_wrapper');
    const image = document.querySelector('#map_body');

    const sb = new ScrollBooster({
        viewport,
        content,
        scrollMode: 'transform',
        direction: 'all',
        bounce: true, // Set to true to allow dragging the map above its limits
        emulateScroll: false // true = scroll inside the viewport, false = scroll the whole page
    });

    image.addEventListener('load', () => {
        // Set viewport position to the center of an image
        const offsetX = image.scrollWidth - viewport.offsetWidth;
        const offsetY = image.scrollHeight - viewport.offsetHeight;
        sb.setPosition({
            x: offsetX / 2,
            y: offsetY / 2
        });
    });
    
    return sb;
}


/**
 * Add event listeners on the buttons which center the map on a zone.
 * For the buttons which center AND zoom the map, see listenToActionModeButtons().
 * 
 * @param {object} node The HTML node containing the buttons
 *                      ex: document.querySelectorAll("#paths_panel .localize");
 * @returns {undefined}
 */
function listenToLocationButtons(node) {

    for (var i=0; i<node.length; i++) {
        node[i].addEventListener("click", function() {
            let htmlCoords = event.target.parentNode.dataset.coords;
            let tooltip = new Tooltip();
            centerMapOnZone(`zone${htmlCoords}`);
            tooltip.toggle(document.querySelector(`#zone${htmlCoords}`));
        });
    }
}


/**
 * Add event listeners on the buttons which center the map on a zone then zoom on it.
 * For the buttons which just center the map, see listenToLocationButtons().
 * 
 * @param {type} node
 * @returns {undefined}
 */
function listenToActionModeButtons(node) {

    for(var i=0; i<node.length; i++) {
        node[i].addEventListener("click", function() {
            switchToActionView();
        });
    }
}


/**
 * Event listener for the #action_mode_button (HTML ID, not class)
 * @returns {undefined}
 */
async function listenToMainActionModeButton() {
    
    // Zoom on the map to the player
    document.querySelector("#action_mode_button").addEventListener("click", switchToActionView);
}


/**
 * Filter the list of constructions inside the city (by defenses, by resources, etc.)
 * 
 * @param {string} selectedValue The HTML value of the <option> selected 
 *                               in the <select> menu
 * @returns {undefined}
 */
function filterConstructions(selectedValue) {
    if(selectedValue === "none") {
        hide("#constructions .defenses");
    } else if(selectedValue === "effects") {
        display("#constructions .defenses");
        display(".construction_defenses");
        hide([".components"]);
    } else if(selectedValue === "components") {
        display("#constructions .defenses");
        display(".components");
        hide(".construction_defenses");
    } else {
        console.log("Error: unknown option value ('"+selectedValue+"') in #city_constructions <select>");
    }
}


/**
 * Add a stage on a zone of the map. Useful when drawing the path of an expedition.
 * 
 * @param {Object} event
 * @param {int} currentStageId The number (0, 1, 2...) of the last stage added 
 *                             on the map
 * @returns {int} The number of the new stage created, or the previous one if
 *                we have deleted a stage
 */
function addMapPathStage(event, currentStageId) {
    
    let hexagon = event.target.closest(".hexagon");
    
    // Avoid JS error if we click in the #map but at a place with no zone
    // (can occur because #map is currently wider than the real zones)
    if(hexagon === null) {
        return currentStageId;
    }
    
    let square = hexagon.querySelector(".square_container");
        coords = `${square.dataset.coordx}_${square.dataset.coordy}`;

    let remove = false;
    // If whe re-click on a zone with a stage previously added...
    if(square.querySelector(`.path_stage[data-pathid="new"]`) !== null) {
        // ... and only if this is the last stage of the path 
        // (this condition avoids creating holes in the numbering)
        let stagesInZone = square.querySelectorAll(`.path_stage[data-pathid="new"]`);
        let clickedStage = stagesInZone[stagesInZone.length-1];
        // NB: we take care about selecting the *last* stage inserted in the zone
        if(parseInt(stagesInZone[stagesInZone.length-1].innerText) === currentStageId-1) {
            // ... remove this stage from the hidden form
            document.querySelector(`#formPathDrawing input[value="${coords}"]`).remove();
            // ... revove the stage from the map
            clickedStage.remove();
            currentStageId--;
            
            remove = true;
        }
    }

    // Else, if the player clicks on a stage that is not the last one 
    // (legit when the player wants to pass several times through the same zone),
    // or if he click on a zone with no stage
    if(remove === false) {
        // Add the stage in the HTML form which will create the expédition            
        document.querySelector("#formPathDrawing .fields").insertAdjacentHTML("beforeend",
            `<input type="hidden" name="zones[]" value="${coords}">`);
        // Display the stage on the map
        hexagon.style.opacity = 1;
        square.insertAdjacentHTML("beforeend",
            `<div class="path_stage pulse" data-pathid="new">${currentStageId}</div>`);
        currentStageId++;
        
        // Steps of the tutorial to help the player to trace his expedition
        if(currentStageId === 1) {
            hide("#formPathDrawing .place_first_stage");
            display("#formPathDrawing .place_second_stage");
        }
        else if(currentStageId === 2) {
            hide("#formPathDrawing .place_second_stage");
            display("#formPathDrawing .place_other_stages");
        }
        else if(currentStageId === 6) {
            hide("#formPathDrawing .place_other_stages");
            display("#formPathDrawing .make_a_loop");
        }
        else if(parseInt(square.querySelector('.path_stage[data-pathid="new"]').innerText) === 0) {
            hide("#formPathDrawing .make_a_loop");
            display("#formPathDrawing .save_stages");
        }
    }
    
    return currentStageId;
}


/**
 * Show/hide the vertical panel for the discussions and events
 */
async function enlargeWall() {
    
    let minBarHeight = "2.5rem",
        maxBarHeight = "100vh";

    if (document.querySelector("#floating_wall").style.height !== maxBarHeight) {
        // Enlarges the panel
        document.querySelector("#floating_wall").style.height = maxBarHeight;
        document.querySelector("#wallHeader .arrow").style.transform = "rotate(+180deg)";
        document.querySelector("#floating_wall").style.zIndex = 60;
    }
    else {
        // Reduces the panel
        document.querySelector("#floating_wall").style.height = minBarHeight;
        document.querySelector("#wallHeader .arrow").style.transform = "rotate(0)";
        document.querySelector("#floating_wall").style.zIndex = 0;
    }
    
    // Loads the discussions tab by default
    initiateDiscussTab();
    
    listenToDiscussTabs();
}


function listenToMapLegendSwitches() {
    
    // When we (des)activate a switch button
    document.querySelector("#map_legend_items .switches").addEventListener("change", function() {
        // Uncheck all other switches previously activated
        document.querySelectorAll("#map_legend_items .switches input").forEach(element => {
            if(element !== event.target) {
                element.checked = false;
            }
        });
        // Delete all the markers already placed on the map
        deleteMapMarkers();
        // Add the location markers on the map for the wanted item type 
        // (boosts, resources...)        
        if(event.target.checked === true) {
            toggleMapItemMarker(event.target.getAttribute("name"));
        }
    });
}


/**
 * Highlight the road leading to a city when hovering the city
 * 
 * @param {type} hexagon HTML node of the heaxagon containing a city
 * @returns {undefined}
 */
function listenToRoads(hexagon) {
    
    const radialMenu = new cityRadialMenu();
    
    hexagon.addEventListener("pointerenter", async (event)=>{
        if(event.pointerType !== "mouse") return;
        
        // Open the radial menu of the newly clicked hexagon
        radialMenu.open(hexagon, event, _elementsToHideInRoadView);
    });
    hexagon.addEventListener("pointerdown", (event) => {
        if(event.pointerType !== "touch") return;
        
        // Hide the road and close the previously open radial menu
        if (_roadActiveHexagon && _roadActiveHexagon !== hexagon) {
            radialMenu.close(_elementsToHideInRoadView);
        }
        
        // Open the radial menu of the newly clicked hexagon
        radialMenu.open(hexagon, event, _elementsToHideInRoadView);
    });
    
    hexagon.addEventListener("pointerleave", (event)=>{
        if(event.pointerType !== "mouse") return;
        
        // Hide the road and close the previously open radial menu
        radialMenu.close(_elementsToHideInRoadView);
    });
}
