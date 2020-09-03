/**
 * Put all the events listeners in this file.
 * Put nothing else than events listeners.
 */

// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Displays/hides the notifications panel
    document.getElementById("notifsButton").addEventListener("click", function(){

        if (window.getComputedStyle(document.getElementById("notifsBlock")).display === 'none') {
            updateDiscussionsNotifs();
            document.getElementById("notifsBlock").style.display = 'block';
        }
        else {
            document.getElementById("notifsBlock").style.display = 'none';
        }
    });
    document.getElementById("notifsClose").addEventListener("click", function(){
        document.getElementById("notifsBlock").style.display = 'none';
    });


    // Switches the action "Display my zone"/"Display the map"
    document.getElementById("backToMap").addEventListener("click", function(){

        if (getCookieConfig('show_zone') === 1) {
            setCookieConfig('show_zone', 0);
        } else {
            setCookieConfig('show_zone', 1);
        }
        toggle('my_zone');
        toggle('displayMyZone');
        toggle('hideMyZone');
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
    
    // Filter discussions/events in the city
    document.getElementById("tabWallAll").addEventListener("click", function() {
        displayClasses(["discuss", "event"]);
        switch_tab("tabWallAll", ["tabWallDiscuss", "tabWallEvents"]);
    });
    document.getElementById("tabWallDiscuss").addEventListener("click", function() {
        displayClasses(["discuss"]);
        hideClasses(["event"]);
        switch_tab("tabWallDiscuss", ["tabWallAll", "tabWallEvents"]);
    });
    document.getElementById("tabWallEvents").addEventListener("click", function() {
        displayClasses(["event"]);
        hideClasses(["discuss"]);
        switch_tab("tabWallEvents", ["tabWallAll", "tabWallDiscuss"]);
    });
    
    
    // Shows/hides the vertical panel for the discussions and events
    document.getElementById("enlarge_wall").addEventListener("click", function() {
        
        let minBarHeight = "2.5rem",
            maxBarHeight = "100%";
            
        if (document.getElementById("floating_wall").style.height !== maxBarHeight) {
            // Enlarges the panel...
            document.getElementById("floating_wall").style.height = maxBarHeight;
            document.getElementById("enlarge_wall").getElementsByClassName("arrow")[0].style.transform = "rotate(+180deg)";
            // ... and loads the discussions if not already loaded
            if (document.getElementById("discussions").innerHTML === "") {
                updateDiscussionsList();
            }
        }
        else {
            document.getElementById("floating_wall").style.height = minBarHeight;
            document.getElementById("enlarge_wall").getElementsByClassName("arrow")[0].style.transform = "rotate(0)";
        }
    });
    
    // Create a new discussion thread
    document.getElementById("buttonNewTopic").addEventListener("click", function() {
        toggleSendform();
    });
    document.getElementById("hideSendform").addEventListener("click", function() {
        toggleSendform();
    });
    document.getElementById("sendform").addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        createDiscussion();
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
