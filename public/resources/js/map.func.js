/* 
 * Javascript functions concerning the map of the game.
 * Put only functions in this file, no directly executable code.
 */


/**
 * Returns the HTML ID of the zone where the player's city is.
 * 
 * @returns {string} The ID of the zone, e.g. "zone10_8"
 */
function getMyCityZoneId() {

    let myCityId = document.querySelector("#gameData #cityId").innerHTML, 
        result = null;
    
    if(myCityId !== "") {
        let myCityNode = document.querySelector(`[data-cityid="${myCityId}"]`);
        result = myCityNode.parentNode.id;
    }
    
    return result; 
}
    
    
/**
 * Gets the X and Y position of a zone on the screen (e.g. 297, 153).
 * Useful to draw an SVG line between two zones.
 * Not to be confused with the coordinates shown on the map (e.g. [7:12])
 * 
 * @param {string} zoneHtmlId The HTML ID of the zone whose coordinates you want.
 *                            Don't forget the hashtag (e.g. "#zone5_11")
 * @returns {dict} zonePositions The X and Y position of the zone
 */
function getZonePositions(zoneHtmlId) {
    
    let mapRect  = document.querySelector("#map_body").getBoundingClientRect();
    let zoneRect = document.querySelector(zoneHtmlId).getBoundingClientRect();
    
    let zonePositions = {
        // "- mapRect.x" => substracts the space between the top of the window and
        // the top of the map
        // "+ zoneRect.width/2" => places the point at the center of the zone
        "x" : Math.round(zoneRect.x - mapRect.x + zoneRect.width/2),
        "y" : Math.round(zoneRect.y - mapRect.y + zoneRect.height/2)
        };
       
    return zonePositions;
}


/**
 * Draws a line in the existing <svg> to replace the previous line.
 * The order of the zones given in parameters doesn't matter.
 * 
 * @param {string} origHtmlId The HTML ID of the first zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone3_10")
 * @param {string} destinHtmlId The HTML ID of the second zone to connect with the line.
 *                            Don't forget the hashtag (e.g. "#zone8_2")
 * @returns {undefined}
 */
function updateLineBetweenZones(origHtmlId, destinHtmlId) {
          
    let orig   = getZonePositions(origHtmlId);
    let destin = getZonePositions(destinHtmlId);
    
    // Erases the existing line in the <svg>
    document.querySelector("#mapSvg").innerHTML = "";
    
    // Draws the new line in the same <svg>
    let line = document.createElementNS("http://www.w3.org/2000/svg", "line");
    line.setAttribute("x1", orig.x);
    line.setAttribute("y1", orig.y);
    line.setAttribute("x2", destin.x);
    line.setAttribute("y2", destin.y);
    document.querySelector("#mapSvg").append(line);
}
