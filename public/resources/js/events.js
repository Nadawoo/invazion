/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

var nbrExecutionsGetCyclicAttacks = 0;

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    let myCityZoneId = getMyCityZoneId();
    
    // Change the ground type of a zone (lava, grass...)
    listenToLandform();
    
    // If the player is drawing a path for an expedition on the map...
    document.querySelector("#startPathCreation").addEventListener("click",  function(){
        startPathCreation();
        _isPathDrawingActive = true;
        var currentStageId = 0;
        // ... then clicking on a zone creates a stage for the expedition
        document.querySelector("#map_body").addEventListener("click",  function(){
            currentStageId = addMapPathStage(event, currentStageId);
        });
        // Same thing for the mobile devices
        document.querySelector("#map_body").addEventListener("touchstart", function(){
            currentStageId = addMapPathStage(event, currentStageId);
        });
    });
    
    if(_isPathDrawingActive === false) {
        // Displays/hides the tooltip of any zone when the mouse hovers one
        listenToMapZones();
    }
    
    // When submitting the form to save a new expedition on the map
    document.querySelector("#formPathDrawing").addEventListener("submit",  function(){
        event.preventDefault();
        submitNewPath(event);
    });
    
    // Zoom/unzoom on the map
    let mapRange = document.querySelector("#zoom_range");    
    mapRange.addEventListener("input", function() {
        zoomMapRange(mapRange.value);
    });
    document.querySelector("#zoomMapStepIn").addEventListener("click", function() {
        event.preventDefault();
        zoomMapStep("in");
    });
    document.querySelector("#zoomMapStepOut").addEventListener("click", function() {
        event.preventDefault();
        zoomMapStep("out");
    });
    
    // Move the citizen on the map
    document.querySelector('#block_move [name="move"]').addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        moveCitizen(event.submitter.value);
        if(myCityZoneId !== null) {
            setTimeout(function() {updateLineBetweenZones("myCity", "#me", "#"+myCityZoneId);}, 1000);
        }
    });
    
    // Change cycle (trigger the midnight attack)
    document.querySelector('form[name="end_cycle"]').addEventListener("submit", function() {
        //event.preventDefault();
        displayMessageEndCycle();
    });
    
    // Digs a zone to find items
    document.querySelector('#block_dig form[name="dig"]').addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        dig();
    });
    // Drops or pick up an item from the player's bag
    document.querySelector('#block_dig').addEventListener("submit", function() {
        let formType = event.target.closest("form").className;
        // NB: this condition avoids interferences with the other forms in the block
        // (actions specific to the item, e.g. to eat a burger)
        if(formType === "form_drop") {
            // Desactivate the classic submission button (avoids reloading the page)
            event.preventDefault();
            dropItem(event.submitter);
        }
        else if(formType === "form_pickup") {
            event.preventDefault();
            pickupItem(event.submitter);
        }
    });
    
    // Move the members of an expedtion
    document.querySelector('#paths_panel').addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        // Move the expedition to the next zone
        let pathId = event.target.querySelector('input[name="params[path_id]"]').value;
        let json = callApi("GET", "paths", `action=move&path_id=${pathId}`);
    });
    
    // Displays/hides the notifications panel
//    document.getElementById("notifsButton").addEventListener("click", function(){
//
//        if (window.getComputedStyle(document.getElementById("notifsBlock")).display === 'none') {
//            display("notifsBlock");
//            updateDiscussionsNotifs();
//        }
//        else {
//            hide("notifsBlock");
//        }
//    });
//    document.getElementById("notifsClose").addEventListener("click", function(){
//        hide("notifsBlock");
//    });
        
    // Switch tabs in the communications panel
    document.querySelector("#wall .tabs a[href='#wallDiscuss']").addEventListener("click", initiateDiscussTab);
    document.querySelector("#wall .tabs a[href='#wallAttacks']").addEventListener("click", function() {
        // Updates the log of attacks
        getCyclicAttacks(nbrExecutionsGetCyclicAttacks);
        nbrExecutionsGetCyclicAttacks++;
    });
    document.querySelector("#wall .tabs a[href='#wallEvents']").addEventListener("click", function() {
        updateDiscussionsList("event");
        // Add the listener on the form to create a topic.
        // TODO: make a cleaner code with async
        setTimeout(listenToSendform, 100);
    });
//    document.querySelector("#wall .tabs a[href='#wallNotifications']").addEventListener("click", function() {
//        getLogEvents("notifications");
//        hideClasses(["iAmNotInvolved"]);
//    });
    
    // Show/hide the vertical panel for the discussions and events
    document.querySelector("#showWall").addEventListener("click", enlargeWall);
    document.querySelector("#wallHeader").addEventListener("click", enlargeWall);
    // In the isometric view of the city, a building deploys the "communications"
    if(document.querySelector("#city_iso") !== null) {
        document.querySelector("#city_iso .discuss").addEventListener("click", enlargeWall);
    }
    
    // If we are inside a city
    if (document.getElementById('city_container') !== null) {  
        // Filter the list of constructions inside the city (by defenses, by resources, etc.)
        // NB: don't use an HTML ID on <select> because Materialize.css would overwrite it.
        // See https://stackoverflow.com/questions/35786433/how-to-listen-on-select-change-events-in-materialize-css
        var elem = document.querySelector("#city_constructions select");
        elem.addEventListener("change", function() { filterConstructions(event.target.value); });
        
        // Open or close the city door
        var elem = document.querySelector("#city_door .change_city_door");
        elem.addEventListener("change", function() { changeCityDoor(event.target.checked); });
    }
}

// If the player is connected
if (document.getElementById("me") !== null) {
    
    // Map: switch to the "action" mode
    document.querySelector("#action_mode_button").addEventListener("click", switchToActionView);
    document.querySelector("#me").addEventListener("click", switchToActionView);
    // Map: switch to the "large map" mode
    document.querySelector("#map_mode_button").addEventListener("click", switchToMapView);
    
    let myHexagon = document.getElementById("me").closest(".hexagon");
    
    // Displays the tooltip of the player's zone when hovering the movement paddle
    let paddle = document.getElementById("central");
    paddle.addEventListener("mouseover", function() { displayTooltip(myHexagon); });
    paddle.addEventListener("mouseout",  function() { hideTooltip(myHexagon);    });
    paddle.addEventListener("click",     function() { toggleTooltip(myHexagon);  });

    // Same thing when hovering the GPS on the smartphone
    let minimap = document.getElementById("minimap");
    minimap.addEventListener("mouseover", function() { displayTooltip(myHexagon); });
    minimap.addEventListener("mouseout",  function() { hideTooltip(myHexagon);    });
    minimap.addEventListener("click",     function() { toggleTooltip(myHexagon);  });
}
