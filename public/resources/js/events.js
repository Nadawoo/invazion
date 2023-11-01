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
    
    // Displays/hides the tooltip of any zone when the mouse hovers one
    listenToMapZones();
    
    // Allows to move the map by dragging it with the mouse
    let scrollBoosterInstance = listenToMapDragging();
    
    // Zoom/unzoom on the map
    let mapRange = document.querySelector("#zoom_range");    
    mapRange.addEventListener("input", function() {
        zoomMapRange(mapRange.value, scrollBoosterInstance);
    });
    document.querySelector("#zoomMapStepIn").addEventListener("click", function() {
        event.preventDefault();
        zoomMapStep("in", scrollBoosterInstance);
    });
    document.querySelector("#zoomMapStepOut").addEventListener("click", function() {
        event.preventDefault();
        zoomMapStep("out", scrollBoosterInstance);
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
    
    // Displays/hides the notifications panel
    document.getElementById("notifsButton").addEventListener("click", function(){

        if (window.getComputedStyle(document.getElementById("notifsBlock")).display === 'none') {
            display("notifsBlock");
            updateDiscussionsNotifs();
        }
        else {
            hide("notifsBlock");
        }
    });
    document.getElementById("notifsClose").addEventListener("click", function(){
        hide("notifsBlock");
    });
        
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
    document.getElementById("enlarge_wall").addEventListener("click", enlargeWall);
    // In the isometric view of the city, a building deploys the "communications"
    if(document.querySelector("#city_iso") !== null) {
        document.querySelector("#city_iso .discuss").addEventListener("click", enlargeWall);
    }
}

// If the player is connected
if (document.getElementById("me") !== null) {
    
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
