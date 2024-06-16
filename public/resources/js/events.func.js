/*
 * This script gathers all the functions related to the event listeners.
 * Put only functions here, no immediatly executable code.
 */


/**
 * Events concerning the form to create a new discussion.
 * Must be asynchronous because the form doesn't exist when the page loads.
 */
function listenToSendform() {
    // Create a new discussion thread
    document.getElementById("buttonNewTopic").addEventListener("click", function() {
        toggleSendform(event);
    });
    document.getElementById("hideSendform").addEventListener("click", function() {
        toggleSendform(event);
    });
    document.getElementById("sendform").addEventListener("submit", function() {
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
 * The form to edit the ground type of a zone (lava, grass...)
 * @returns {undefined}
 */
async function listenToLandform() {
    document.getElementById("landform").addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        
        let fields = document.getElementById("landform").elements,
            landType = event.submitter.value,
            x = fields["coord_x"].value,
            y = fields["coord_y"].value,
            radius = fields["radius"].value;
        
        updateLandType(landType, x, y, radius);
    });
}


/**
 * Displays/hides the tooltip of any zone when the mouse hovers one
 */
function listenToMapZones() {
    // Check whether the user moves his finger rather than tapping
    var touchmoved = false;
    document.getElementById("map_body").addEventListener("touchmove", function(){
        touchmoved = true;
    });
    
    // [On PC] Show/hide toolip on hovering the zone
    document.getElementById("map_body").addEventListener("mouseover", function(){
            displayTooltip(event.target.closest(".hexagon"));
        },
        { passive: true }
    );
    document.getElementById("map_body").addEventListener("mouseout",  function(){
            hideTooltip(event.target.closest(".hexagon"));
        },
        { passive: true }
    );
    // [On PC] Open the details of a building when clicking on it
    document.getElementById("map_body").addEventListener("click", function(){
            openBuildingPopup(event);
        },
        { passive: true }
    );
    
    // [On mobile] Open the tooltip when tapping on a zone without building,
    // or open the pop-up if the zone contains a building
    document.getElementById("map_body").addEventListener("touchstart", function(){
            toggleTooltip(event.target.closest(".hexagon"));
        },
        { passive: true }
    );
    document.getElementById("map_body").addEventListener("touchend", function(){
            if(touchmoved === false) {
                openBuildingPopup(event);
            }
            toggleTooltip(event.target.closest(".hexagon"));
            touchmoved = false;
        },
        { passive: true }
    );
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
            centerMapOnZone(`zone${htmlCoords}`);
            toggleTooltip(document.querySelector(`#zone${htmlCoords}`));
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
    document.querySelector("#map_mode_button").addEventListener("click", switchToMapView);
}


/**
 * Event listener when clicking on the player on his map zone
 * @returns {undefined}
 */
function listenToMeOnMap() {
    
    document.querySelector("#me").addEventListener("click", switchToActionView);
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
        hideClasses(["defenses"], "constructions");
    } else if(selectedValue === "effects") {
        unhideClasses(["defenses"], "constructions");
        unhideClasses(["construction_defenses"]);
        hideClasses(["components"]);
    } else if(selectedValue === "components") {
        unhideClasses(["defenses"], "constructions");
        unhideClasses(["components"]);
        hideClasses(["construction_defenses"]);
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
            hideClasses(["place_first_stage"], "formPathDrawing");
            unhideClasses(["place_second_stage"], "formPathDrawing");
        }
        else if(currentStageId === 2) {
            hideClasses(["place_second_stage"], "formPathDrawing");
            unhideClasses(["place_other_stages"], "formPathDrawing");
        }
        else if(currentStageId === 6) {
            hideClasses(["place_other_stages"], "formPathDrawing");
            unhideClasses(["make_a_loop"], "formPathDrawing");
        }
        else if(parseInt(square.querySelector('.path_stage[data-pathid="new"]').innerText) === 0) {
            hideClasses(["make_a_loop"], "formPathDrawing");
            unhideClasses(["save_stages"], "formPathDrawing");
        }
    }
    
    return currentStageId;
}
