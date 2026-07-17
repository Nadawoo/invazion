import { switchToMapView } from "../mapUse.func.js";
import {
    getItemCoords,
    getItemTypeCoords,
    isActionViewActive
    }  from "../mapInit.func.js";
import {
    closePopup,
    itemsBubbleFragment
    } from "../misc.func.js";


export class MapMarkers {
    
   /**
    * 
    * @returns {undefined}
    */
    switchToMarkersView() {
        
        // Close the popup to go back to the map
        closePopup();
        if(isActionViewActive()) {
            switchToMapView();
        }
        // Hide the useless elements on the map to focus on the item bubbles
        hide([".sharp_bubble", ".nbr_defenses", ".nbr_items", ".healthbar", ".zombies"]);
    }
   
   
    /**
      * Add/remove a location mark on the zones containing the given item
      * 
      * @param {int|string} itemId The ID of the item to mark.
      *                            Set it to "boost" to get the coordinates of the zones 
      *                            containg items giving action points (water, food...)
      * @returns {undefined}
      */
    async toggleMapItemMarker(itemId) {

        // If the items are not already marked, get their coordinates
        if(window.areMapMarkersActive !== true) {
            // Remove the eventual previously created markers, as they can mark 
            // an other type of item (boosts, resources...)
            this.deleteLocationMarkers();
            
            const itemsCoords = Number.isInteger(itemId)
                                ? getItemCoords(itemId)
                                : getItemTypeCoords(itemId);

            for(let coords of Object.values(await itemsCoords)) {
                let zone = document.querySelector("#map_body #zone"+coords);
                let square = zone.querySelector(".square_container");
                zone.style.opacity = 1;
                
                if(Number.isInteger(itemId)) {
                    // If we look for a specific item
                    square.appendChild( itemsBubbleFragment([itemId]) );
                }
                else {
                    // If we look for a type of item (weapon, resource, food...)
                    square.innerHTML += '<img src="resources/img/free/map_location.svg" class="location animate__animated animate__slideInDown">';
                }
            }
            
            display("#map_body .location");
            window.areMapMarkersActive = true;
        }
        else {    
            // Hide the icons added by the previous call to the function
            hide("#map_body .location");
            window.areMapMarkersActive = false;
        }
    }
    
    
    /**
     * Remove all the "location.svg" markers from the map, whatever they mark
     * (boosts, resources...)
     */
    deleteLocationMarkers() {

        document.querySelectorAll("#map_body .location").forEach(element => 
            element.remove()
        );

        window.areMapMarkersActive = false;
    }
}

