/**
 * Generates the graphical interface for a dersert building (outpost...)
 */
class BuildingPopup {
    
    
    /**
     * Open the pop-up describing a building on the map
     * 
     * @param {object} event
     * @returns {undefined}
     */
    openBuildingPopup(event) {
        
        var zone = (event.target.closest(".square_container") === null)
                    ? document.querySelector("#me").closest(".square_container")
                    : event.target.closest(".square_container");

        let dataset = zone.dataset;
        let cityTypeId = dataset.citytypeid;
        // #12 = ID of the building type "city" in the Azimutant's API
        if(parseInt(cityTypeId) === 12) {
            let cityId = event.target.closest(".square_container").dataset.cityid;
            let cityConnections = new CityConnections();
            cityConnections.updateConnectedCitiesLines(mapId);
            cityConnections.addCityframes(mapId, cityId);
            toggleCityframesView();
        }
        else if(cityTypeId !== "") {
            this.populateBuildingPopup(cityTypeId, dataset.zombies, dataset.cyclelastvisit);
            window.location.href = "#popsuccess";
        }
    }
    
    
    /**
     * Create the pop-up describing a building on the map.
     * If you need to open the pop-up after having created it, see openBuildingPopup()
     * 
     * @param {int} cityTypeId The ID of the building, as returned by the Azimutant's API
     * @returns {undefined}
     */
    populateBuildingPopup(cityTypeId, nbrZombiesInZone, cycleLastVisit) {

        let building = _configsBuildings[cityTypeId];
        let findableItems = (_configsBuildingsFindableItems[cityTypeId] !== undefined) ? _configsBuildingsFindableItems[cityTypeId] : [];
        let buildingConfig = _configsBuildings[cityTypeId];
        
        // Update the content of the pop-up
        let tplPopupBuilding = document.querySelector('#tplPopupBuilding').content.cloneNode(true),
            popup = document.querySelector("#popsuccess .content");
        popup.innerHTML = "";
        popup.appendChild(tplPopupBuilding);
        popup.querySelectorAll(".building_name").forEach(
            element => element.innerHTML = building.name
        );
        
        // Icon of the building
        if(buildingConfig["icon_path"] !== null) {
            popup.querySelector(".block_building .icon").innerHTML = 
                `<img src="/resources/img/${buildingConfig["icon_path"]}"
                      alt="Icône du bâtiment" height="80">`;
        } else {
            popup.querySelector(".block_building .icon").innerHTML = buildingConfig["icon_html"];
        }
        
        if(building["descr_ambiance"] !== "") {
            popup.querySelector(".descr_ambiance").innerHTML = nl2br(building.descr_ambiance);
        }
        
        // Display/hide the button for exploring the building
        if(parseInt(cycleLastVisit) === getCurrentCycle()) {
            popup.querySelector(".text_explored").classList.remove("hidden");
            popup.querySelector(".text_unexplored").classList.add("hidden");
        } else {
            // Add the list of items findable in this building
            for(let itemId of findableItems) {
                popup.querySelector(".items_list").prepend(htmlItem(itemId, _configsItems[itemId]));
            }
        }

        // Add the line of zombies after the last invaded module
        let lastInvadedModuleId = this.getLastInvadedModuleId(nbrZombiesInZone);
        this.markInactiveModules(popup, lastInvadedModuleId);
        this.updateModulesZombieRow(popup, nbrZombiesInZone, lastInvadedModuleId);
        
        // If the building can't be explored (city), hide the useless frames 
        // in the pop-up.
        if(_configsBuildings[cityTypeId]["is_explorable"] === 0) {
            popup.querySelector(".block_explore").classList.add("hidden");
        } else {
            popup.querySelector(".block_plans").classList.add("hidden");
            popup.querySelector(".block_modules").classList.add("hidden");
        }
    }

    
    getLastInvadedModuleId(nbrZombiesInBuilding) {
        // TODO: temporary hardcoded value for the tests. Number of zombies that are
        // needed for invaded one module of a building.
        let defensesPerModule = 5;
        
        return Math.floor((nbrZombiesInBuilding-1) / defensesPerModule);
    }


    markInactiveModules(popup, lastInvadedModuleId) {

        let tableRows = popup.querySelectorAll(".block_modules table tr:not(.zombies_row)");
        tableRows.forEach((row, id) => {
            let isModuleActive = (id >= lastInvadedModuleId) ? true : false;
            // Green or red background for the active/inactive modules
            let htmlClass = (isModuleActive === true) ? 'active' : 'inactive';
            row.classList.add(htmlClass);
            // Put a cross as icon for the module if it is invaded by zombies
            if(isModuleActive === false) {
                row.querySelector(".icon").innerHTML = "&#x274C;";
            }
        });
    }


    updateModulesZombieRow(popup, nbrZombiesInBuilding, lastInvadedModuleId) {
        
        let table = popup.querySelector(".block_modules table");
        let zombiesRow = table.querySelector(".zombies_row");
        
        if(parseInt(nbrZombiesInBuilding) === 0) {
            zombiesRow.remove();
        }
        else {
            let zombiesRowClone = zombiesRow.cloneNode(true);
            // Write the amount of zombies inside the building
            zombiesRowClone.querySelector(".nbr_zombies").innerHTML = nbrZombiesInBuilding;
            // Move the row of zombies after the moduls which are out of order
            table.querySelectorAll("tr")[lastInvadedModuleId].insertAdjacentElement('afterend', zombiesRowClone);
            zombiesRow.remove();
        }
    }
}
