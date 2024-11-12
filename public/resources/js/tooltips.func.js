/* 
 * Functions related to the tooltips on the map
 * Don't put executable code in this file, only functions.
 */


/**
 * Display/hide the tooltip over a zone of the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
function triggerTooltip(hexagon) { 
    // If the tooltip is not already displayed
    if(hexagon !== null && hexagon.querySelector(".bubble").classList.contains("block") === false) {
        // Display the tooltip
        displayTooltip(hexagon);
        // Will hide the tooltip when the mouse leaves the hexagon
        hexagon.addEventListener("mouseleave",
                                ()=>hideTooltip(hexagon),
                                { passive: true }
        );
    }
}


/**
 * Display the tooltip over a zone of the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
function displayTooltip(hexagon) {
    // Displays the tooltip
    hexagon.querySelector(".bubble").classList.add("block");
    populateTooltip(hexagon);
    // Shifts the zone tooltip to the left if it overflows the map on the right
    handleTooltipOverflow(hexagon);
}


/**
 * Hide the tooltip of a zone of the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
function hideTooltip(hexagon) {
    
    hexagon.querySelector(".bubble").classList.remove("block");
}


/**
 * Switch the display/hide of the tooltip on the map
 * 
 * @param {object} hexagon
 * @returns {undefined}
 */
function toggleTooltip(hexagon) {
    
    if(hexagon !== null) {
        let activeTooltip = document.querySelector("#map .bubble.block"),
            newTooltip = hexagon.querySelector(".bubble");
        // Hide the eventual currently displayed tooltip
        if(activeTooltip !== null) {
            hideTooltip(activeTooltip.closest(".hexagon"));
        }
        // Display the new tooltip
        if(newTooltip !== activeTooltip) {
            newTooltip.classList.toggle("block");
            handleTooltipOverflow(hexagon);
        }
    }
}


/**
 * Add the data inside the tooltip (number of zombies in the zone...)
 * 
 * @param {type} hexagon
 * @returns {undefined}
 */
function populateTooltip(hexagon) {
    
    let dataset     = hexagon.querySelector(".square_container").dataset,
        nbrItems    = Number(dataset.items);
        nbrZombies  = Number(dataset.zombies),
        nbrCitizens = Number(dataset.citizens),
        cityTypeId  = Number(dataset.citytypeid);

    let htmlItems    = (nbrItems > 0)    ? `<br>• ${plural(nbrItems, "objet")} au sol` : "",
        htmlZombies  = (nbrZombies > 0)  ? `<br>• ${plural(nbrZombies, "zombie")} dans la zone` : "",
        htmlCitizens = (nbrCitizens > 0) ? `<br>• ${plural(nbrCitizens, "humain")} dans la zone` : "";

    let htmlRoleplay = "";
    
    if(hexagon.querySelector("#me") !== null) {
        htmlRoleplay = '<h5 class="name">Vous êtes ici&nbsp;!</h5>';
    } else if(cityTypeId !== 0) {
        // Add the description of the building in the bubble 
        let buildingCarcs = _configsBuildings[cityTypeId],
            buildingName = buildingCarcs.name,
            buildingDescr = buildingCarcs.descr_ambiance;
        
        htmlRoleplay = `<h5 class="name">${buildingName}</h5>
                        <hr>
                        <div class="descr_ambiance">${nl2br(buildingDescr)}</div>`;
    } else {
        htmlRoleplay = "Zone explorable";
    }
    
    hexagon.querySelector(".bubble .coords").innerText = `[${dataset.coordx}:${dataset.coordy}]`;
    hexagon.querySelector(".bubble .roleplay").innerHTML = htmlRoleplay;
    hexagon.querySelector(".bubble .inventory").innerHTML = htmlItems + htmlZombies + htmlCitizens;
}


/**
 * Shift the zone tooltip to the left if it overflows the map on the right
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
