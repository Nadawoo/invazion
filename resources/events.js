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


    // Displays/hides the tooltip of the zone when the mouse hovers the zone
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

    // Same thing when hovering the center of the movement paddle
    document.getElementById("central").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("central").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("central").addEventListener("click", function() {
        toggleTooltip(document.getElementById("my_hexagon"));
    });

    // Same thing when hovering the GPS on the smartphone
    document.getElementById("minimap").addEventListener("mouseover", function() {
        displayTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("minimap").addEventListener("mouseout", function() {
        hideTooltip(document.getElementById("my_hexagon"));
    });
    document.getElementById("minimap").addEventListener("click", function() {
        toggleTooltip(document.getElementById("my_hexagon"));
    });
}
