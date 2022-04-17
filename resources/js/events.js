/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

var nbrExecutionsGetCyclicAttacks = 0;

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Change the ground type of a zone (lava, grass...)
    listenToLandform();
    
    // Displays/hides the tooltip of any zone when the mouse hovers one
    listenToMapZones();
    
    
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


    // Switches the action "Display my zone"/"Display the map"
    document.getElementById("backToMap").addEventListener("click", function(){        
        toogleMyZone();
        // Memorizes the display to restore it if the page is refreshed
        let newCookieValue = (getCookieConfig("show_zone") === 1) ? 0 : 1;
        setCookieConfig("show_zone", newCookieValue);
    });
    
    
    // Switch tabs in the communications panel
    document.getElementById("tabWallDiscuss").addEventListener("click", function() {
        display("wallDiscuss");
        hide(["wallPhone", "wallNotifications", "wallEvents", "wallAttacks"]);
        activateDiscussionTab("tabWallDiscuss");
        updateDiscussionsList();
        // Add the listener on the form to create a topic.
        // TODO: make a cleaner code with async
        setTimeout(function() { listenToSendform(); }, 100);
    });
    document.getElementById("tabWallAttacks").addEventListener("click", function() {
        display("wallAttacks");
        hide(["wallPhone", "wallDiscuss", "wallNotifications", "wallEvents"]);
        activateDiscussionTab("tabWallAttacks");
        // Updates the log of attacks
        getCyclicAttacks(nbrExecutionsGetCyclicAttacks);
        nbrExecutionsGetCyclicAttacks++;
    });
    document.getElementById("tabWallEvents").addEventListener("click", function() {
        display("wallEvents");
        hide(["wallPhone", "wallDiscuss", "wallNotifications", "wallAttacks"]);
        activateDiscussionTab("tabWallEvents");
        getLogEvents("wallEvents");
    });
    document.getElementById("tabWallPhone").addEventListener("click", function() {
        display("wallPhone");
        hide(["wallDiscuss", "wallNotifications", "wallEvents", "wallAttacks"]);
        activateDiscussionTab("tabWallPhone");
    });
//    document.getElementById("tabWallNotifications").addEventListener("click", function() {
//        display("notifications");
//        hide(["discussions", "events", "attacks"]);
//        activateDiscussionTab("tabWallNotifications");
//        getLogEvents("notifications");
//        hideClasses(["iAmNotInvolved"]);
//    });
    
    // Show/hide the vertical panel for the discussions and events
    document.getElementById("enlarge_wall").addEventListener("click", function() {
        enlargeWall();
    });
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
