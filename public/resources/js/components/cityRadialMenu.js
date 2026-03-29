
class cityRadialMenu {
    
    /**
     * Open the radial menu over a city (Go to/See...)
     * 
     * @param {Object} hexagon
     * @param {Event} event
     * @param {Array} elementsToHide
     * @returns {undefined}
     */
    async open(hexagon, event, elementsToHide) {
        
        const connections = new CityConnections();
        _roadActiveHexagon = hexagon;

        // Highlight the road to the city
        let path = await connections.highlightRoad(event);

        // If there is a road to highlight
        if(path !== null && path !== undefined) {
            // Mark the clicked city frame as active (useful for adding a visible
            // border around the city)
            const cityframe = event.target.closest("#map_body .hexagon").querySelector(".cityframe");
            cityframe.classList.add("active");
            
            // Hide the useless elements to enlighten the GUI
            hide(elementsToHide);
            // Display the "Go to" menu
            const radialMenu = hexagon.querySelector(".radial_menu");
            if(radialMenu !== null && radialMenu.closest(".square_container").querySelector("#me") === null) {
                radialMenu.classList.remove("hidden");
            }

            // Stop the execution of the display() below if the mouse leaves then 
            // hovers again the city
            if(_roadDisplayTimeout) {
                clearTimeout(_roadDisplayTimeout);
                _roadDisplayTimeout = null;
            }

            // Display the cost in action points above each city of the path
            path.forEach((cityId)=> {
                const moveCost = document.querySelector(`#map_body .square_container[data-cityid="${cityId}"] .move_cost`);
                if(moveCost !== null) {
                    moveCost.classList.remove("hidden");
                }
            });
        } else {
            display(elementsToHide);
        }
    }
    
    
    /**
     * Close the radial menu around a city (Go to, See...)
     * 
     * @param {type} otherElementsToHide
     * @returns {undefined}
     */
    close(otherElementsToHide) {

        const connections = new CityConnections();
        connections.turnoffRoad();

        const oldMenu = _roadActiveHexagon.querySelector(".radial_menu");
        oldMenu?.classList.add("hidden");

        hide("#map_body .move_cost");
        display(otherElementsToHide);
        
        // Hide the city frame previously marked as active
        document.querySelectorAll("#map_body .cityframe.active").forEach((cityframe) => 
            cityframe.classList.remove("active")
        );
        
        _roadActiveHexagon = null;
    }
}
