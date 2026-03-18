/**
 * Adds the lines between the connected cities on the map when clicking on a city.
 */
class CityConnections {
    
    
    /**
     * Draws a line between the cities connected over the map
     * 
     * @param {int} mapId The ID of the map for which you want the roads
     * @param {Object} cities The data of the cities, as returned by the "cities" API
     */
    async updateConnectedCitiesLines(mapId, cities) {
        
        const zombLib = new ZombLib(),
              json = await zombLib.callApi("GET", "connections", `action=get&map_id=${mapId}`),
              roads = json.datas.roads;
        
        const graphClass = new Graph();
        const graph = graphClass.buildGraph(roads);
        
        const drawnRoads = new Set();
        // For each city from the graph
        Object.keys(graph).forEach(sourceCityId => {
            // For each neighbor of the current city
            graph[sourceCityId].forEach(targetCityId => {
                // Don't draw the same road twice
                const key         = `${sourceCityId}To${targetCityId}`;
                const reversedKey = `${targetCityId}To${sourceCityId}`;
                
                if(drawnRoads.has(key) || drawnRoads.has(reversedKey)) return;

                const sourceCity = cities[sourceCityId];
                const targetCity = cities[targetCityId];
                const sourceZoneId = `zone${sourceCity.coord_x}_${sourceCity.coord_y}`;
                const targetZoneId = `zone${targetCity.coord_x}_${targetCity.coord_y}`;

                this.#updateLineBetweenZones(
                    `${targetZoneId}To${sourceZoneId}`,
                    `#${sourceZoneId}`,
                    `#${targetZoneId}`,
                    this.#getLineType(targetCity.city_type_id)
                );

                drawnRoads.add(key, reversedKey);
            });

        });
    }
    
    
    /**
     * Highlight the road to city
     * 
     * @param {type} event
     * @returns {undefined}
     */
    async highlightRoad(event) {

        const sourceCityId = document.querySelector("#me").parentNode.dataset.cityid,
              targetCityId = event.target.querySelector('.square_container').dataset.cityid;

        // Call the API to get the connections between cities
        _roads = await getMapRoadsOnce(mapId);

        const graphClass = new Graph();
        const graph = graphClass.buildGraph(_roads);
        const path = graphClass.getPath(graph, await _cities, sourceCityId, targetCityId);

        // For each city of the path
        for(let i=0; i<path.length-1; i++) {       
            let currentCity = _cities[path[i]],
                nextCity    = _cities[path[i+1]];

            let currentCityHtmlId = `zone${currentCity.coord_x}_${currentCity.coord_y}`,
                nextCityHtmlId    = `zone${nextCity.coord_x}_${nextCity.coord_y}`;

            let roadName         = `${currentCityHtmlId}To${nextCityHtmlId}`;
            let roadNameReversed = `${nextCityHtmlId}To${currentCityHtmlId}`;

            let road         = document.querySelector(`#mapSvg line[name="${roadName}"]:not(.animated-line)`);
            let roadReversed = document.querySelector(`#mapSvg line[name="${roadNameReversed}"]:not(.animated-line)`);

            // Hide the road "A to B". If not found, try in the reversed direction "B to A".
            if(road !== null) {
                road.classList.add("highlight");
            } else {
                roadReversed.classList.add("highlight");
            }
        }
    }
    
    
    /**
     * Cancel the highlighting of a road to a city
     * 
     * @returns {undefined}
     */
    turnoffRoad() {

        const highlightedRoads = document.querySelectorAll("#mapSvg .highlight");    
        highlightedRoads.forEach((road)=>{ road.classList.remove("highlight") });
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

        if(_cities !== null) {
            for(let city of Object.entries(_cities)) {
                let childCity = city[1];

                if(clickedCityId === null || childCity["connected_city_id"] !== null) {
                    this.#addCityframe(childCity.coord_x, childCity.coord_y, childCity.city_type_id, childCity.total_defenses);
                    this.#addNbrDefenses(childCity.coord_x, childCity.coord_y, childCity.city_type_id, childCity.total_defenses);
                    this.#addNbrItems(childCity.coord_x, childCity.coord_y);
                }
            }
        }
        
        if(clickedCityId !== null) {
            this.#highlightClickedCityframe(clickedCityId);
        }
    }
    
    
    /**
     * Add the label for the number of items on a city
     * 
     * @param {int} cityCoordX
     * @param {int} cityCoordY
     * @returns {undefined}
     */
    #addNbrItems(cityCoordX, cityCoordY) {
        
//        if(buildingCarcs.is_explorable === 1) {
            
            let htmlCoords = cityCoordX+"_"+cityCoordY,
                zone = document.querySelector("#zone"+htmlCoords+" .square_container"),
                nbrItems = zone.dataset.items,
                cityTypeId = Number(zone.dataset.citytypeid),
                icon_size = 22;
                
            let htmlItems = new Items();
            
            let html = "";            
            if(Number(zone.dataset.cyclelastvisit) === 0) {
                html = "";
            }
            // If the building is a construction requiring components
            else if(cityTypeId in _configsBuildingsComponents) {    
                // Icons of the items required for building
                const htmlFindableItems = Object.keys(_configsBuildingsComponents[cityTypeId])
                                            .map(id => {
                                                const itemId = Number(id);
                                                
                                                if(!_configsItems[itemId]) {
                                                    var icon_path = null,
                                                        icon_symbol = null;
                                                } else {
                                                    var icon_path = _configsItems[itemId]["icon_path"],
                                                        icon_symbol = _configsItems[itemId]["icon_symbol"];
                                                }
                                                
                                                return `
                                                <span class="icon">
                                                    ${htmlItems.icon(itemId, icon_size)}
                                                </span>
                                                `;
                                            })
                                            .join('');
                                    
                // <div class="sharp_bubble ..." aria-label="..."></div>
                const div = document.createElement('div');
                div.className = 'sharp_bubble diggable animate__animated animate__pulse animate__infinite';
                div.setAttribute('aria-label', 'Liste des composants requis pour construire ce bâtiment');
                div.style.background = "orangered";
                div.innerHTML = `🛠️&nbsp; ${htmlFindableItems}`;
                // Add the bubble for digging above the city
                zone.querySelector(".cityframe").appendChild(div);
            }
            // If the building is explorable
            else if(Number(zone.dataset.cyclelastvisit) < getCurrentCycle()
                    && _configsBuildings[cityTypeId]["is_explorable"] === 1) {
                // Add the icons of the findable items above the building
                zone.querySelector(".cityframe").appendChild( itemsBubbleFragment(_configsBuildingsFindableItems[cityTypeId]) );
            }
            else if(nbrItems > 0) {
                html = `<span class="nbr_items">${nbrItems}</span>`;
            }
//            else {
////                let maxExplorations = 100;
////                // NB: #108 = ID of the item "Counter of explorations"
////                let nbrExplorationsDone = zones[htmlCoords]['items'][108] || 0;
////                let nbrExplorationsRemaining = maxExplorations-nbrExplorationsDone;
//                html = `<span class="nbr_items safe">&#x2705;</span>`;
//            }
            
            zone.querySelector(".cityframe").insertAdjacentHTML("afterbegin", html);
//        }
    }
    
    
    /**
     * Add the "number of defenses" label above each city
     * 
     * @param {int} cityCoordX
     * @param {int} cityCoordY
     * @param {int} cityTypeId
     * @param {int} cityDefenses
     * @returns {undefined}
     */
    #addNbrDefenses(cityCoordX, cityCoordY, cityTypeId, cityDefenses) {
        
        // #233 is the ID for the "Undiscovered building" in the API.
        let undiscoveredBuildingId = 233,
            zombieCoreId = 228,
            zombieBaseId = 230,
            roadConnectionId = 244;
        
        let htmlCoords = cityCoordX+"_"+cityCoordY,
            zone = document.querySelector("#zone"+htmlCoords+" .square_container"),
            cityframe = zone.querySelector(".cityframe"),
            htmlNbrDefenses = "";
                        
        if(cityTypeId === zombieBaseId) {
            // Add the number of zombies of the daily attack 
            htmlNbrDefenses = `<div class="nbr_defenses" style="background:red">${zone.dataset.zombies} <img src="resources/img/motiontwin/zombie.gif" alt="&#x1F9DF;"></div>`;
        }
        else if(cityTypeId === roadConnectionId) {
            // Add the cost in action points above the raod connection
            let apCost = Math.max(1, Math.floor(zone.dataset.zombies/2));
            htmlNbrDefenses = `<div class="sharp_bubble" aria-label="Coût pour traverser cette zone">&nbsp;-${apCost}<span role="img" aria-label="points d'action">⚡</span></div>`;
        }
        else if(cityTypeId === zombieCoreId) {
            // Add a special marker on the zombie cores
            htmlNbrDefenses = `<div class="nbr_items" style="background:lightgrey"><span class="icon">&#x2757;</span></div>`;
        }
        else if(cityTypeId !== undiscoveredBuildingId) {
            // Add the number of defenses above each city, excepted above
            // the undiscovered ones
            let nbrDefenses = cityDefenses,
                nbrZombiesNextAttack = zone.dataset.zombies,
                defensesExcedent = nbrDefenses - nbrZombiesNextAttack;
            
            // Display the label above the map only if the city has at least one defense
            if(defensesExcedent <= 0 && nbrZombiesNextAttack > 0) {
                // Submerged by the zombies
                htmlNbrDefenses = `<div aria-label="Ce bâtiment est irrémédiablement submergé par les zombies" class="nbr_defenses">&#x1F480;</div>`;
            }
//                else if(defensesExcedent >= 0) {
//                    htmlNbrDefenses = `<div class="nbr_defenses safe">&#x2705;</div>`;
//                }
            else if(nbrDefenses > 0) {
                // Display the number of defenses for the city if not zero
                htmlNbrDefenses = `<div class="nbr_defenses animate__animated animate__zoomIn hidden">&nbsp;+${defensesExcedent}<span role="img" aria-label="Défenses">🛡️</span></div>`;
            }
        }
        
        cityframe.insertAdjacentHTML("afterbegin", htmlNbrDefenses);
        cityframe.dataset.defenses = cityDefenses;
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

        let hexagon = document.querySelector(`#zone${cityCoordX}_${cityCoordY}`),
            zone = hexagon.querySelector(`.square_container`),
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
            cssClass = `zombie_core defenses ${cssClassPulse} `;
            label = `${nbrZombies}&#x1F9DF;`;           
        } else if(cityTypeId === 233) {
            // #11 = the ID of the "Undiscovered building"
            cssClass = `undiscovered`;
            label = `&#x1F50D;`;           
        } else if(cityTypeId === 244) {
            // #244 = The ID of the "Road connection"
            cssClass = `road_connection`;
            label = ``;
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
            zone.insertAdjacentHTML("beforeend",
                `<div class="cityframe ${cssClass}" role="none">
                    <div class="healthbar hidden animate__animated animate__fadeIn"></div>
                    <span class="dot_notif">!</span>
                    <div class="label hidden">${label}</div>
                    <div class="frame"></div>
                </div>`
            );
        }
        
        // If the city is occupied by the zombies
        if(nbrZombies > 1 && nbrZombies > cityDefenses) {
            // Make the red circle bigger as the zombies are numerous in the zone
            let scale = 0.7 + Math.min(0.9, Math.floor(nbrZombies/2)*0.1);
            scale = Math.min(1.3, scale);
            hexagon.querySelector(".zombies").style.transform = `scale(${scale})`;
            hexagon.classList.add("submerged");
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
