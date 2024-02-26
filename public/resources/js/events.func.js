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
    document.getElementById("buttonNewTopic").addEventListener("focus", function() {
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