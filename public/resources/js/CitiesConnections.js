/**
 * Adds the lines between the connected cities on the map when clicking on a city.
 */
class CityConnections {
    
    
    /**
     * Draws a line between the cities connected over the map
     * 
     * @param {int} mapId
     */
    async updateConnectedCitiesLines(mapId) {

        _cities = await getMapCitiesOnce(mapId);

        for(let city of Object.entries(_cities)) {
            let childCity = city[1];
            
            if(childCity["connected_city_id"] !== null) {
                let parentCity = _cities[childCity["connected_city_id"]],
                    childCityZoneId  = `zone${childCity["coord_x"]}_${childCity["coord_y"]}`,
                    parentCityZoneId = `zone${parentCity["coord_x"]}_${parentCity["coord_y"]}`;
                
                this.#updateLineBetweenZones(
                                    `${childCityZoneId}To${parentCityZoneId}`,
                                    `#${parentCityZoneId}`,
                                    `#${childCityZoneId}`,
                                     this.#getLineType(childCity.city_type_id)
                                     );
            }
        }
    }
    
    
    /**
     * Set a CSS class for the animated line between two connected cities.
     * Useful to set its color, width, etc. in CSS.
     * 
     * @param {int} cityTypeId The ID of the city (not the ID of the instance
     *                         of the city), as returned by the Azimutant's API
     * @returns {string}
     */
    #getLineType(cityTypeId) {
        
        let lineType = "defenses";
        
        if(cityTypeId === 228) {
            // #228 = the ID of the "Zombie core"
            lineType = "zombie_core defenses";
        } else if(cityTypeId === 235) {
            // #235 = the ID of the "Heliport"
            lineType = "transportations";
        }
        
        return lineType;
    }
    
    
    /**
     * Draws a line in the existing <svg> to replace the previous line.
     * NB: the order of the zones given in parameters (origin/destination) doesn't 
     * really matter, as the result will be the same (a line between the 2 points).
     * 
     * @param {string} lineName   The alias of the line to treat, stored in <line name="..."> 
     * @param {string} origHtmlId The HTML ID of the first zone to connect with the line.
     *                            Don't forget the hashtag (e.g. "#zone3_10")
     * @param {string} destinHtmlId The HTML ID of the second zone to connect with the line.
     *                            Don't forget the hashtag (e.g. "#zone8_2")
     * @returns {undefined}
     */
    #updateLineBetweenZones(lineName, origHtmlId, destinHtmlId, lineType="") {
        
        let orig   = getZonePositions(origHtmlId);
        let destin = getZonePositions(destinHtmlId);

        // Erase the existing line in the <svg>
        let lineNodes = document.querySelectorAll(`#mapSvg line[name=${lineName}]`);
        lineNodes.forEach(lineNode => lineNode.remove());

        // Create an animated line between origin and destination
        // Create the base line
        let baseLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
        baseLine.setAttribute("name", lineName);
        baseLine.setAttribute("x1", orig.x);
        baseLine.setAttribute("y1", orig.y);
        baseLine.setAttribute("x2", destin.x);
        baseLine.setAttribute("y2", destin.y);
        baseLine.setAttribute("class", lineType);
        document.querySelector("#mapSvg").appendChild(baseLine);

        // Create the animated line
        let animatedLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
        animatedLine.setAttribute("name", lineName);
        animatedLine.setAttribute("x1", orig.x);
        animatedLine.setAttribute("y1", orig.y);
        animatedLine.setAttribute("x2", destin.x);
        animatedLine.setAttribute("y2", destin.y);
        animatedLine.setAttribute("class", `animated-line ${lineType}`);
        document.querySelector("#mapSvg").appendChild(animatedLine);
    }
    
    
    /**
     * Add frames around the cities on the map
     * 
     * @param {int} mapId The ID of the concerned map
     * @param {int} clickedCityId The ID of the parent city. Frames will be added 
     *                  around it and all the cities connectcted to it.
     *                  If not set, frames will be added around all the buildings
     *                  on the map.
     */
    async addCityframes(mapId, clickedCityId=null) {
        
        _cities = await getMapCitiesOnce(mapId);

        for(let city of Object.entries(_cities)) {
            let childCity = city[1];
            
            if(clickedCityId === null || childCity["connected_city_id"] !== null) {
                let nbrDefenses = _configsBuildings[childCity.city_type_id].defenses;
                this.#addCityframe(childCity["coord_x"], childCity["coord_y"], childCity.city_type_id, nbrDefenses);
            }
        }

        this.#highlightClickedCityframe(clickedCityId);
    }


    /**
     * Add a white frame around the desert buildings connected to a city
     * 
     * @param {int} cityCoordX The X coordinate of the building on wich to add the frame
     * @param {int} cityCoordY The Y coordinate
     * @param {int} cityTypeId The ID of the type of the building, as returned by
     *                         the Azimutant's API
     *                         Ex: 12 if the building is an "Outpost" type
     * @param {int} cityDefenses The total amount of defenses of the building
     */
    #addCityframe(cityCoordX, cityCoordY, cityTypeId, cityDefenses) {

        let zone = document.querySelector(`#zone${cityCoordX}_${cityCoordY} .square_container`),
            isExplored = (parseInt(zone.closest(".square_container").dataset.cyclelastvisit) === getCurrentCycle()),
            nbrZombies = zone.closest(".square_container").dataset.zombies,
            cssClassPulse = "animate__animated animate__pulse animate__infinite",
            label = "",
            cssClass = "";
        
        if(cityTypeId === 234) {
            // #234 = the ID of the city type "Drugstore" in the Azimutant's API
            cssClass = `boosts resources`;
            label = `&#x26A1;`;
        } else if(cityTypeId === 235) {
            // #235 = the ID of the "Heliport"
            cssClass = "transportations";
            label = "&#x1F681;";           
        } else if([236, 237, 238, 239].includes(cityTypeId)) {
            // #236 = the ID of the "Training room", #237 = the "Collector",
            // #238 = the "Multiplier", #239 = the "Power plant"
            cssClass = "technical resources";
            label = "&#x2699;&#xFE0F;";           
        } else if([11, 12].includes(cityTypeId)) {
            // #12 = the ID of the "City", #11 = Outpost
            cssClass = (cityDefenses === 0) ? "defenses nolabel" : "defenses";
            label = `${cityDefenses}&#x1F6E1;&#xFE0F;`;           
        } else if(cityTypeId === 228) {
            // #228 = the ID of the "Zombie core"
            cssClass = `zombie_core defenses ${cssClassPulse}`;
            label = `${nbrZombies}&#x1F9DF;`;           
        } else if(cityTypeId === 233) {
            // #11 = the ID of the "Undiscovered building"
            cssClass = `undiscovered`;
            label = `&#x1F50D;`;           
        } else if(cityTypeId === 5) {
            // #5 = The ID of the "Wood storage"
            cssClass = `resources`;
            label = `&#x1FAB5;`;
        } else {
            label = `&#x2753;`;
        }
        
        // #228 = the ID of the "Zombie core", #11 = Outpost
        if(isExplored === true) {
            cssClass += " explored";
        }
        if(isExplored === true && ![11, 228].includes(cityTypeId)) {
            label = "&#x2705;";
        }
        
        if(zone.querySelector(".cityframe") === null) {
            zone.insertAdjacentHTML("afterbegin",
                `<div class="cityframe ${cssClass}">
                    <span class="dot_notif">!</span>
                    <div class="label hidden">${label}</div>
                    <div class="frame"></div>
                </div>`
            );
        }
    }
    
    
    /**
     * Turn to gold the label of the parent city (where the other buildings
     * are connected)
     * 
     * @param {int} clickedCityId
     */
    #highlightClickedCityframe(clickedCityId) {
        
        let cityData = _cities[clickedCityId],
            cityDiv = document.querySelector(`#zone${cityData.coord_x}_${cityData.coord_y}`);

        cityDiv.querySelector(`.cityframe`).classList.add("gold");
        cityDiv.querySelector(`.cityframe`).classList.add("animate__animated", "animate__pulse", "animate__infinite");
        cityDiv.querySelector(`.label`).innerHTML = `${cityData.total_defenses}&#x1F6E1;&#xFE0F;`;
    }
}
