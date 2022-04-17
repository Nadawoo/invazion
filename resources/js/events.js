/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

var nbrExecutionsGetCyclicAttacks = 0;

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Change the ground type of a zone (lava, grass...)
    listenToLandform();
    
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

        if (getCookieConfig("show_zone") === 1) {
            setCookieConfig("show_zone", 0);
        } else {
            setCookieConfig("show_zone", 1);
        }
        toggleHide("my_zone");
        toggleHide("displayMyZone");
        toggleHide("hideMyZone");
    });


    // Displays/hides the tooltip of any zone when the mouse hovers one
    document.getElementById("map").addEventListener("mouseover", function(){
        displayTooltip(event.target.closest(".hexagon"));
    });
    document.getElementById("map").addEventListener("mouseout",  function(){
        hideTooltip(event.target.closest(".hexagon"));
    });
    // The onclick event is required for the mobile devices (no notion of "hover" there)
    document.getElementById("map").addEventListener("click", function(){
        toggleTooltip(event.target.closest(".hexagon"));
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
    
    // Displays the tooltip of the player's zone when hovering the movement paddle
    document.getElementById("central").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("me").closest(".hexagon"));
    });
    document.getElementById("central").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("me").closest(".hexagon"));
    });
    document.getElementById("central").addEventListener("click", function() {
        toggleTooltip(document.getElementById("me").closest(".hexagon"));
    });

    // Same thing when hovering the GPS on the smartphone
    document.getElementById("minimap").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("me").closest(".hexagon"));
    });
    document.getElementById("minimap").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("me").closest(".hexagon"));
    });
    document.getElementById("minimap").addEventListener("click", function() {
        toggleTooltip(document.getElementById("me").closest(".hexagon"));
    });
}

//If we are on the panel to edit the items available in game
if (document.getElementById('editConfig') !== null) {
    
    // On page load, we hide by default all the secondary options of the form
    hide([  'block_findable',
            'block_findable_advanced',
            'block_compo',
            'block_apgain',
            'block_malus',
            'block_healing',
            'block_weapon',
            'block_bag',
            'block_drop',
            'block_loads',
            'block_solidity_custom',
            'block_killing_rate'
            ]);
}
