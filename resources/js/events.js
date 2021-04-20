/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

var nbrExecutionsGetCyclicAttacks = 0;

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
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
        display("discussions");
        hide(["notifications", "events", "attacks"]);
        activateDiscussionTab("tabWallDiscuss");
    });
    document.getElementById("tabWallAttacks").addEventListener("click", function() {
        display("attacks");
        hide(["discussions", "notifications", "events"]);
        activateDiscussionTab("tabWallAttacks");
        // Updates the log of attacks
        getCyclicAttacks(nbrExecutionsGetCyclicAttacks);
        nbrExecutionsGetCyclicAttacks++;
    });
    document.getElementById("tabWallEvents").addEventListener("click", function() {
        display("events");
        hide(["discussions", "notifications", "attacks"]);
        activateDiscussionTab("tabWallEvents");
        getLogEvents("events");
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

//If we are on the panel to eit the items available in game
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
            'block_solidity_custom'
            ]);
}


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