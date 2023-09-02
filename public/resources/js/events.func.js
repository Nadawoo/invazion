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
            y = fields["coord_y"].value;
        
        updateLandType(landType, x, y);
    });
}


/**
 * Displays/hides the tooltip of any zone when the mouse hovers one
 */
function listenToMapZones() {
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
}


/**
 * Allows to move the map by dragging it with the mouse
 * 
 * @returns {ScrollBooster|listenToMapDragging.sb}
 */
function listenToMapDragging() {

    const viewport = document.querySelector('#map');
    const content = document.querySelector('#map_body_wrapper');
    const image = document.querySelector('#map_body');

    const sb = new ScrollBooster({
        viewport,
        content,
        scrollMode: 'transform',
        bounce: false, // Set to true to allow dragging the map above its limits
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
