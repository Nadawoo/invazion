/**
 * This class places the citizens on the map
 */
class MapCitizens {
    
    /**
     * Place the citizens on the map. They are not loaded by the PHP to speed up
     * the loading of the map.
     * 
     * @param {Int} mapId
     * @returns {Array} 
     */
    async addCitizensOnMap(mapId) {

        // Get the citizens of the map by calling the Azimutant's API
        _citizens = await getMapCitizensOnce(mapId);

        // Place the citizens on the appropriate zones
        for(let citizenId in _citizens) {
            let citizen = _citizens[citizenId],
                htmlCoords = citizen.coord_x+"_"+citizen.coord_y,
                zone = document.querySelector("#zone"+htmlCoords+" .square_container");
                
            // Don't add the citizen if an other citizen is already placed in the zone
            if(zone.querySelector(".map_citizen") === null && zone.dataset.zombies < 1 && zone.dataset.cityid < 1) {

                let nbrCitizens = zone.dataset.citizens;
                let htmlCitizens = this.#htmlCitizens(nbrCitizens, citizen.citizen_pseudo);

                zone.insertAdjacentHTML("afterbegin", htmlCitizens);

                // Delete the "&nbsp;" required on the empty zones 
    //            if(zone.querySelector(".empty") !== null) {
    //                zone.querySelector(".empty").remove();
    //            }
            }
        }

        return _citizens;
    }
    
    
    /**
     * Generate the HTML images for X citizens in a zone of a map
     * with natural positions (not just in one line) and avoids overlaps.
     * 
     * @param {Int} nbrCitizens
     * @param {String} playerPseudo 
     * @returns {String} HTML
     */
    #htmlCitizens(nbrCitizens, playerPseudo) {

        // Set of predefined positions for the images of the citizens 
        // in a zone, to avoid overlaps.
        // - First key = the number of citizens in the zone
        // - Subkeys = citizen 1, citizen 2, citizen 3...
        var positions = {
            1: { 1:{"top":"revert-layer", "left":"inherit"} },
            2: { 1:{"top":"-2.3em", "left":"0.3em"},
                 2:{"top":"-1.8em", "left":"1em"}
                },
            3: { 1:{"top":"-2.5em", "left":"0.3em"},
                 2:{"top":"-2.3em", "left":"1.4em"},
                 3:{"top":"-1.8em", "left":"0.9em"}
                },
            4: { 1:{"top":"-2.7em", "left":"0.3em"},
                 2:{"top":"-2.3em", "left":"1.4em"},
                 3:{"top":"-1.8em", "left":"0.3em"},
                 4:{"top":"-1.7em", "left":"1.3em"}
                },
            5: { 1:{"top":"-2.7em", "left":"0.3em"},
                 2:{"top":"-2.4em", "left":"0.7em"},
                 3:{"top":"-2.7em", "left":"1.6em"},
                 4:{"top":"-1.8em", "left":"0.3em"},
                 5:{"top":"-1.7em", "left":"1.3em"}
                },
            };

        // If there are too many citziens for the cases set, assume that
        // we use the maximal number of citizens.
        let maxCitizens = Object.keys(positions).length;
        nbrCitizens = (nbrCitizens > maxCitizens) ? maxCitizens : nbrCitizens;
        
        let label = (nbrCitizens > 1) ? "[Groupe]" : playerPseudo;
        
        var citizensIcons = "";
        for(let i=0; i<nbrCitizens; i++) {
            let top  = positions[nbrCitizens][i+1]["top"],
                left = positions[nbrCitizens][i+1]["left"];
            citizensIcons += `<img src="/resources/img/free/human.png" height="48"
                         style="position:absolute;top:${top};left:${left}">`;
        }

        return `<div class="map_citizen">
                    <span class="nbr_defenses">${label}</span>
                    ${citizensIcons}
                </div>
                <div class="halo inactive">&nbsp;</div>
                <div class="overlay hidden"></div>
                `;
   }
}
