import { switchToMapView } from "../mapUse.func.js";
import {
    getItemCoords,
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

        let markerType = "generic",
            markerValue = 1;

        // If the items are not already marked, get their coordinates
        if(window.areMapMarkersActive !== true) {

            var itemsCoords = [];
            if(Number.isInteger(itemId)) {
                markerType = "itemid";
                markerValue = itemId;
                itemsCoords = getItemCoords(itemId);
            } else {
                markerType = itemId;
                itemsCoords = getItemCoords(markerType);
            }

            for(let coords of Object.values(await itemsCoords)) {
                let zone = document.querySelector("#map_body #zone"+coords);
                zone.style.opacity = 1;
                zone.dataset["marker"+markerType] = markerValue;
            }
        }

        this.toggleMapMarkerByType(markerType);
    }
    
    
    /**
     *  Displays the location icon on every zone which contains the specified element
     *  
     *  @param {string} objectToMark The alias of the object to mark on the map.
     *                               See the dictionary "markableObjects" in the present 
     *                               function to know the available aliases.
     */
    toggleMapMarkerByType(objectToMark) {

        // Here are listed the DOM selectors to mark the zones you want (e.g. class name...)
        var markableObjects = {
            "items":    ".square_container:not([data-items='0'])",
            "citizens": ".square_container:not([data-citizens='0'])",
            "boost":    "#map_body [data-markerboost='1']",
            "resource": "#map_body [data-markerresource='1']",
            "itemid":   "#map_body [data-markeritemid]",
            "generic":  "#map_body [data-markergeneric='1']"
            };

        if (window.areMapMarkersActive !== true) {
            // Remove the eventual previously created markers, as they can mark 
            // an other type of item (boosts, resources...)
            this.deleteMarkers();
            // Add the HTML for the icons in the zones
            document.querySelectorAll(markableObjects[objectToMark]).forEach(element => {
                if(objectToMark === "itemid") {
                    // Bubble with the icon of the item
                    const itemId = element.dataset.markeritemid;
                    element.querySelector(".square_container").appendChild( itemsBubbleFragment([itemId]) );
                }
                else {
                    // Generic location pin marker
                    element.innerHTML += '<img src="resources/img/free/map_location.svg" class="location animate__animated animate__slideInDown">';
                }
            });

            display("#map_body .location");
    //        hide("#map_body .nbr_defenses");
            window.areMapMarkersActive = true;
        }
        else {    
            // Hides the icons added by the previous call to the function
            hide("#map_body .location");
            window.areMapMarkersActive = false;
        }
    }
    
    
    /**
     * Remove all the "location.svg" markers from the map, whatever they mark
     * (boosts, resources...)
     */
    deleteMarkers() {

        document.querySelectorAll("#map_body .location").forEach(element => 
            element.remove()
        );

        window.areMapMarkersActive = false;
    }
}

