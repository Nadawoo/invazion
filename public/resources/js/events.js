/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

var nbrExecutionsGetCyclicAttacks = 0;

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    let myCityZoneId = setTimeout(getMyCityZoneId, 1000);
    
    // Needed to unregister event listeners
    const controller = new AbortController();
    const { signal } = controller;
    
    // Function to add a path stage when clickong on a zone.
    // Not put in a named fnction because we need to get the returned currentStageId
    var currentStageId = 0;
    var listenToAddMapPathStage = function(){
        currentStageId = addMapPathStage(event, currentStageId);
    };
    
    // Change the ground type of a zone (lava, grass...)
    listenToLandform();
    
    // If the player is drawing a path for an expedition on the map...
    document.querySelector("#startPathCreation").addEventListener("click",  function(){
        startPathCreation();
        _isPathDrawingActive = true;
        
        // ... then clicking on a zone creates a stage for the expedition
        // (for PC and touchscreen)
        // NB: the {signal} parameter will allow to unregister the listener with a abort()
        document.querySelector("#map_body").addEventListener("click", listenToAddMapPathStage, { signal });
        document.querySelector("#map_body").addEventListener("touchstart", listenToAddMapPathStage, { signal });
    });
    
    if(_isPathDrawingActive === false) {
        // Displays/hides the tooltip of any zone when the mouse hovers one
        listenToMapZones();
    }
    
    // When submitting the form to save a new expedition on the map
    document.querySelector("#formPathDrawing").addEventListener("submit",  function(){
        event.preventDefault();
        submitNewPath(event, controller);
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
    
    // Actions in the horizontal bar of expeditions
    document.querySelector('#paths_bar').addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        let formName = event.target.closest("form").getAttribute("name");
        
        if(formName === "move_path") {
            movePath(event);
        }
        else if(formName === "populate_path") {
            window.location.replace("#poppopulatepath");
        }
        else {
            displayToast("[Bug] Action inconnue dans la barre d'expéditions. "
                         + "Signalez-le au responsable du site.",
                         "critical");
        }
    });
    
    // Add members to an expedition (vertical paths panel)
    // NB: can't listen directly to the form because it's generated by javascript
    document.querySelector('#paths_panel').addEventListener("submit", function() {
        event.preventDefault();
        if(event.target && event.target.matches('form[name="available_members"]')) {
            addPathMembers(event);
        }
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
if(isCitizenInGame() === true) {
    
    // Button to zoom the map on the player
    listenToMainActionModeButton();
    
//    let myHexagon = document.getElementById("me").closest(".hexagon");
    
    // Displays the tooltip of the player's zone when hovering the movement paddle
//    let paddle = document.getElementById("central");
//    paddle.addEventListener("mouseover", function() { displayTooltip(myHexagon); });
//    paddle.addEventListener("mouseout",  function() { hideTooltip(myHexagon);    });
//    paddle.addEventListener("click",     function() { toggleTooltip(myHexagon);  });

    // Same thing when hovering the GPS on the smartphone
//    let minimap = document.getElementById("minimap");
//    minimap.addEventListener("mouseover", function() { displayTooltip(myHexagon); });
//    minimap.addEventListener("mouseout",  function() { hideTooltip(myHexagon);    });
//    minimap.addEventListener("click",     function() { toggleTooltip(myHexagon);  });
}
