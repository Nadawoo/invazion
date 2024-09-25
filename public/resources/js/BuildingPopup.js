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

        if(event.target.closest(".square_container") !== null) {
            let dataset = event.target.closest(".square_container").dataset;
            let cityTypeId = dataset.citytypeid;
            // #12 = ID of the building type "city" in the Invazion's API
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
    }
    
    
    /**
     * Create the pop-up describing a building on the map.
     * If you need to open the pop-up after having created it, see openBuildingPopup()
     * 
     * @param {int} cityTypeId The ID of the building, as returned by the Invazion's API
     * @returns {undefined}
     */
    populateBuildingPopup(cityTypeId, nbrZombiesInZone, cycleLastVisit) {

        let building = _configsBuildings[cityTypeId];
        let findableItems = (_configsBuildingsFindableItems[cityTypeId] !== undefined) ? _configsBuildingsFindableItems[cityTypeId] : [];

        // Update the content of the pop-up
        let tplPopupBuilding = document.querySelector('#tplPopupBuilding').content.cloneNode(true),
            popup = document.querySelector("#popsuccess .content");
        popup.innerHTML = "";
        popup.appendChild(tplPopupBuilding);
        popup.querySelector(".building_name").innerHTML = building["name"];
        if(building["descr_ambiance"] !== "") {
            popup.querySelector(".descr_ambiance").innerHTML = building["descr_ambiance"];
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
//        if(_configsBuildings[cityTypeId]["is_explorable"] === 0) {
//            popup.querySelector(".block_explore").classList.add("hidden");
//            popup.querySelector(".block_modules").classList.add("hidden");
//        }
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
