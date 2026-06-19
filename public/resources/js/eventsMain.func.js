/*
 * This script gathers all the functions related to the event listeners.
 * Put only functions here, no immediatly executable code.
 */

import { ZombLib } from "./lib/ZombLib.js";
import { BuildingPopup } from "./components/BuildingPopup.js";
import { CityRadialMenu } from "./components/CityRadialMenu.js";
import { Items } from "./components/Items.js";
import { Move } from "./services/Move.js";
import { Clipboard } from "./utils/Clipboard.js";
import { moveBuildingBlockBelowPaddle, updateBlockAction } from "./actionBlocks.func.js";
import { togglePathsBar } from "./paths.func.js";
import {
    addZombiesInZone,
    closePopup,
    dig,
    displayToast,
    dropItem,
    exploreBuilding,
    killZombies,
    pickupItem,
    populateDefensesDetails,
    searchItemOnMap,
    toggleActionBlock,
    toggleBag,
    toggleMapMarker,
    toggleStatus,
    updateLandType
    }
    from "./misc.func.js";
import { initiateDiscussTab, listenToDiscussTabs, toggleSendform } from "./discussions.func.js";
import {
    isActionViewActive,
    switchToActionView,
    triggerTooltip,
    zoomMapRange
    }
    from "./mapInit.func.js";
import {
    buildOnMap,
    centerMapOnMe,
    resetMapView,
    toggleCityframesView,
    toggleMapExplorationsView,
    toggleMapItemsView,
    toggleMapNeighborhoodView,
    toggleMapZombiesView,
    toggleMapItemMarker,
    zoomMapStep
    }
    from "./mapUse.func.js";

const inputState = {
    dragStartX: 0,
    dragStartY: 0,
    isDragging: false
};

/**
 * Listen to all the forms (avoids creating one event listenener per form)
 * 
 * @returns {undefined}
 */
export function listenToSubmit() {
    
    document.addEventListener("submit", (event)=>{
        const formSelectors = {
            "createGame": "form[name=create_game]",
            "dig":"#block_dig form[name=dig]",
            "dropItem":"form[name=form_drop]",
            "pickupItem":"form[name=form_pickup]",
            "explore": "form[name=explore_building]",
            "move": "#block_move form[name=move]",
            "joinGame": "form[name=join_game]",
            "updateLandType": "#landform"
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
        else if(event.target.matches(formSelectors.dropItem)) {
            event.preventDefault();
            dropItem(event.submitter);
        }
        else if(event.target.matches(formSelectors.pickupItem)) {
            event.preventDefault();
            pickupItem(event.submitter);
        }
        else if(event.target.matches(formSelectors.move)) {
            event.preventDefault();
            const move = new Move();
            move.walk(event.submitter.value);
        }
        else if(event.target.matches(formSelectors.updateLandType)) {
            event.preventDefault();
        
            const fields = event.target.elements,
                  landType = event.submitter.value,
                  x = fields["coord_x"].value,
                  y = fields["coord_y"].value,
                  radius = fields["radius"].value;

            updateLandType(landType, x, y, radius).then((json) => 
                displayToast(json.metas.error_message, json.metas.error_class)
            );
        }
        else if(event.target.matches(formSelectors.joinGame)) {
            event.preventDefault();
            
            const zombLib = new ZombLib(),
                  cookies = new Cookies(),
                  formData = new FormData(event.target);
            const mapId = formData.get("params[map_id]"),
                  token = cookies.getCookie('token');
            
            zombLib.callApi("GET", "games", `action=join&map_id=${mapId}&token=${token}`)
                .then((json) => {
                    // If the player is already in a game, simply redirect to the map page
                    if(json.metas.error_code === "already_in_game"
                       && event.target.closest("#myGames .games_list") !== null
                       ) {
                       window.location.href = "/index#Outside";
                    }
                    else if(json.metas.error_code === "success") {
                        window.location.href = "/index#Outside";
                    }
                    else {
                        displayToast(json.metas.error_message, json.metas.error_class);
                    }
                });
        }
    });
}


export function listenToInput() {
    
    document.addEventListener("input", (event)=>{
        
        if(event.target.dataset.action === "zoomRange") {
            zoomMapRange(event.target.value);
        }
    },
    { passive: true }
    );
}


/**
 * Listen to all the "click" events
 * (avoids creating one event listener per button)
 * 
 * @returns {undefined}
 */
export function listenToClick() {
    
    document.addEventListener("click", (event)=>{
        
        const selectors = {
            "buildCity":"#builder button[name=build_city]",
            "enlargeWall": "enlargeWall",
            };
        
        const target = event.target;
        const action = target.dataset.action;
        let hexagon = target.closest(".hexagon"),
            button = target.closest("button");
            
        const actionViewActive = isActionViewActive();
        
        // Build a city on the map (not a construction inside a city)
        if(target.closest(selectors.buildCity)) {
            buildOnMap(Number(target.closest(selectors.buildCity).dataset.citytypeid));
        }
        else if(action === "drive") {
            // If we click on a "drive" button over a city (move on the roads)
            const path = target.dataset.path;
            const move = new Move();
            move.driveToCity(path);
        }
        else if(button?.dataset.action === "openBuildingPopup") {
            let buildingPopup = new BuildingPopup();
            buildingPopup.openBuildingPopup(event);
        }
        else if(action === "teleport") {
            // If we click on a teleportation button over a city, teleport the citizen
            const cityId = Number(target.closest(".square_container").dataset.cityid);
            const move = new Move();
            move.teleportToCity(cityId);
        }
        else if(action === "addRoad") {
            _newRoadSource = Number(hexagon.querySelector(".square_container").dataset.cityid);
            displayToast("Sélectionnez la ville de destination de la route", "info");
            
            document.querySelector("#map").dataset.viewmode = "addRoad";
        }
        else if(action === "moveBuildingBlockBelowPaddle") {
            moveBuildingBlockBelowPaddle();
        }
        else if(action === "killZombies") {
            killZombies("fight");
        }
        else if(action === "killMassZombies") {
            killZombies("bigfight");
        }
        else if(action === "repelZombies") {
            killZombies("repel");
        }
        else if(action === "closeCityframesBar") {
            toggleCityframesView();
        }
        else if(action === "populateDefensesDetails") {
            populateDefensesDetails();
        }
        else if(action === "togglePathsBar") {
            togglePathsBar();
        }
        else if(!actionViewActive && button?.dataset.action === "switchToActionView") {
            switchToActionView();
        }
        else if(button?.dataset.action === "toggleBag") {
            toggleBag(); 
        }
        else if(button?.dataset.action === "toggleStatus") {
            toggleStatus(); 
        }
        else if(button?.dataset.action === "copyTextarea") {
            const clipboard = new Clipboard();
            clipboard.copyTextarea(button.dataset.target);
        }
        else if(actionViewActive && hexagon && hexagon.querySelector("#me") === null) {
            toggleActionBlock("move");
        }
        else if(actionViewActive && action === "switchActionBlock") {
            const blockName = target.dataset.name;
            toggleActionBlock(blockName);
            updateBlockAction(blockName);
        }
        else if(actionViewActive && button?.dataset.action === "switchActionBlock") {
            const blockName = button.dataset.name;
            toggleActionBlock(blockName);
            updateBlockAction(blockName);
        }
        else if(action === "addZombiesInZone") {
            addZombiesInZone();
        }
        else if(target.closest("#map_navigation")) {            
            const button = target.closest("button");
            
            if(button?.dataset.action === "zoomMapStepIn") {
                zoomMapStep("in");
            }
            else if(button?.dataset.action === "zoomMapStepOut") {
                zoomMapStep("out");
            }
            else if(button?.dataset.action === "centerMapOnMe") {
                centerMapOnMe();
            }
            else if(action === "switchMapView") {
                const view = target.dataset.view;

                resetMapView();
                // Highlight the active view in the menu
                target.classList.add("active");

                if(view === "neighborhood") {
                    toggleMapNeighborhoodView();
                }
                else if(view === "explorations") {
                    toggleMapExplorationsView();
                }
                else if(view === "items") {
                    toggleMapItemsView();
                }
                else if(view === "zombies") {
                    toggleMapZombiesView();
                    toggleMapItemMarker(106);
                } else if(view === "realMap") {
                    toggleMapMarker();
                }
            }
        }
        else if(action === "createGame") {
            event.preventDefault();
            
            const zombLib = new ZombLib(),
                  cookies = new Cookies(),
                  token = cookies.getCookie('token');
            
            zombLib.callApi("GET", "games", `action=create&token=${token}`)
                .then((json) => {
                    displayToast(json.metas.error_message, json.metas.error_class);
                });
        }
        else if(target.closest(".item_label")){
            const itemLabel = target.closest(".item_label");
            
            if(action === "searchItemOnMap") {
                searchItemOnMap(event);
            }
            else if(action === "closeTooltip") {
                const items = new Items();
                items.toggleTooltip(event);
            }
            else if(itemLabel.dataset.action === "toggleTooltip") {
                const items = new Items();
                items.toggleTooltip(event);
            }
        }
        else if(action === "closePopup" || target.closest("a")?.dataset.action === "closePopup") {
            closePopup();
        }
        else if(action === selectors.enlargeWall || button?.dataset.action === "enlargeWall") {
            enlargeWall();
        }
        else if(hexagon && hexagon.querySelector(".square_container").dataset.citytypeid !== "") {
            
            if(_newRoadSource !== null) {
                // Trace a new road
                const newRoadTarget = Number(hexagon.querySelector(".square_container").dataset.cityid),
                      cookies = new Cookies(),
                      token = cookies.getCookie("token"),
                      zombLib = new ZombLib();
                      
                const json = zombLib.callApi("GET", "connections", `action=add&source=${_newRoadSource}&target=${newRoadTarget}&token=${token}`);                
                json.then(result => displayToast(result.metas.error_message, result.metas.error_class));
                
                // Exit the "Add road" view mode
                _newRoadSource = null;
                document.querySelector("#map").dataset.viewmode = "";
            }
        }
        else if(_newRoadSource !== null && hexagon.querySelector(".square_container").dataset.cityid === "") {
            // Exit the "Add road" view mode
            _newRoadSource = null;
            document.querySelector("#map").dataset.viewmode = "";
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
export function listenToPointerdown() {
    
    // Close the radial menu when tapping anywhere out of the active hexagon
    document.addEventListener("pointerdown", (event)=>{
        
        inputState.startX = event.clientX;
        inputState.startY = event.clientY;
        inputState.isMapDragging = false;
    },
    { passive: true }
    );
}


export function listenToPointermove() {
    
    document.addEventListener("pointermove", (event) => {
        // Detect if the user clicked for dragging rather than for activating something
        const dx = event.clientX - inputState.startX;
        const dy = event.clientY - inputState.startY;

        if(Math.abs(dx) > 10 || Math.abs(dy) > 10) {
            inputState.isMapDragging = true;
        }
    });
}


export function listenToPointerup() {
    
    document.addEventListener("pointerup", (event)=>{
        
        if(event.pointerType !== "touch") return;
        
        // Don't trigger actions if the click just intends to drag the map
        if(inputState.isMapDragging) {
            inputState.isMapDragging = false;
            return;
        }
        
        const radialMenu = new CityRadialMenu();
        const hexagon = event.target.closest(".hexagon");
        const container = hexagon?.querySelector(".square_container");
        
        markZoneAsSelected(hexagon);
        
        // Hide the road and close the previously open radial menu
        if(_roadActiveHexagon && _roadActiveHexagon !== hexagon) {
            radialMenu.close(_elementsToHideInRoadView);
        }
        
        // Open the radial menu of the newly clicked hexagon
        if(hexagon !== null && hexagon.querySelector(".square_container").dataset.cityid !== "") {
            radialMenu.open(hexagon, event, _elementsToHideInRoadView);
        }
    },
    { passive: true }
    );
}


function markZoneAsSelected(hexagon) {
    
    // Remove the "selected" mark on the eventual previously selected zone
    document.querySelector(".square_container.selected")?.classList.remove("selected");
    
    // Mark the newly selected zone with "selected"
    if(hexagon !== null) {
        hexagon.querySelector(".square_container").classList.add("selected");
    }
}


/**
 * Events concerning the form to create a new discussion.
 * Must be asynchronous because the form doesn't exist when the page loads.
 */
export function listenToSendform() {
    // Create a new discussion thread
    document.getElementById("buttonNewTopic").addEventListener("pointerup", (event)=>{
        toggleSendform(event);
    });
    document.getElementById("hideSendform").addEventListener("pointerup", (event)=>{
        toggleSendform(event);
    });
    document.getElementById("sendform").addEventListener("submit", (event)=>{
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        createDiscussion();
    });

    // Clear the error messages if the user writes in the form
    document.getElementById("sendform").addEventListener("input", (event)=>{
         document.getElementById("errorNewTopicPseudo").innerHTML  = "";
         document.getElementById("errorNewTopicMessage").innerHTML = "";
    });
}


/**
 * Allows to move the map by dragging it with the mouse
 * 
 * @returns {ScrollBooster|listenToMapDragging.sb}
 */
export function listenToMapDragging() {
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

    image.addEventListener('load', (event)=>{
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
export function listenToLocationButtons(node) {

    for (var i=0; i<node.length; i++) {
        node[i].addEventListener("click", (event)=>{
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
export function listenToActionModeButtons(node) {

    for(var i=0; i<node.length; i++) {
        node[i].addEventListener("click", (event)=>{
            switchToActionView();
        });
    }
}


/**
 * Filter the list of constructions inside the city (by defenses, by resources, etc.)
 * 
 * @param {string} selectedValue The HTML value of the <option> selected 
 *                               in the <select> menu
 * @returns {undefined}
 */
export function filterConstructions(selectedValue) {
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
export async function enlargeWall() {
    
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


export function listenToMapLegendSwitches() {
    
    // When we (des)activate a switch button
    document.querySelector("#map_legend_items .switches").addEventListener("change", (event)=>{
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
export function listenToRoads(hexagon) {
    
    const radialMenu = new CityRadialMenu();
    
    hexagon.addEventListener("pointerenter", async (event)=>{
        if(event.pointerType !== "mouse") return;
        if(isActionViewActive() === true) return;
        
        // Open the radial menu of the newly clicked hexagon
        radialMenu.open(hexagon, event, _elementsToHideInRoadView);
    });
    
    hexagon.addEventListener("pointerleave", (event)=>{
        if(event.pointerType !== "mouse") return;
        if(isActionViewActive() === true) return;
        
        // Hide the road and close the previously open radial menu
        radialMenu.close(_elementsToHideInRoadView);
    });
}
