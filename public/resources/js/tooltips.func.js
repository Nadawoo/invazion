/* 
 * Functions related to the tooltips on the map
 * Don't put executable code in this file, only functions.
 */


/**
 * Displays the tooltip over a zone of the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function displayTooltip(hexagon) { 
    
    if (hexagon !== null) {
        // Displays the tooltip
        hexagon.querySelector(".bubble").classList.add("block");
        // Shifts the zone tooltip to the left if it overflows the map on the right
        handleTooltipOverflow(hexagon);
    }
}


/**
 * Hides the tooltip of a zone of the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function hideTooltip(hexagon) {
    
    if (hexagon !== null) {
        hexagon.querySelector(".bubble").classList.remove("block");;
    }
}


/**
 * Switches the display/hide of the tooltip on the map
 * @param {object} hexagon
 * @returns {undefined}
 */
function toggleTooltip(hexagon) {
    
    if (hexagon !== null) {
        hexagon.querySelector(".bubble").classList.toggle("block");
        handleTooltipOverflow(hexagon);
    }
}


/**
 * Shifts the zone tooltip to the left if it overflows the map on the right
 * 
 * @param {type} hexagon The HTML of the zone where the tooltip is
 * @returns {undefined}
 */
function handleTooltipOverflow(hexagon) {
    
    let tooltipBounding = hexagon.querySelector(".bubble").getBoundingClientRect();
    let mapBounding    = document.querySelector("#map").getBoundingClientRect();
    if (tooltipBounding.right > mapBounding.right) {
        hexagon.querySelector(".bubble").style.left        = "-15em";
        hexagon.querySelector(".triangle_down").style.left = "16em";
    }
    else if (tooltipBounding.left < mapBounding.left) {
        hexagon.querySelector(".bubble").style.left        = "0.5em";
        hexagon.querySelector(".triangle_down").style.left = "0.5em";
    }
}
