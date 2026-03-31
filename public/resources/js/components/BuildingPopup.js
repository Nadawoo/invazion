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
        let cityId = dataset.cityid;
        let cityTypeId = dataset.citytypeid;
        
        // #12 = ID of the building type "city" in the Azimutant's API
        if(parseInt(cityTypeId) === 12) {
            let cityConnections = new CityConnections();
            cityConnections.updateConnectedCitiesLines(mapId);
            cityConnections.addCityframes(mapId, cityId);
            toggleCityframesView();
        }
        else if(cityTypeId !== "") {
            this.#populateBuildingPopup(cityId, cityTypeId, dataset.zombies, mapId, dataset.coordx, dataset.coordy);
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
    #populateBuildingPopup(cityId, cityTypeId, nbrZombiesInZone, mapId, coordX, coordY) {
        
        let building = _configsBuildings[cityTypeId];
        let findableItems = (_configsBuildingsFindableItems[cityTypeId] !== undefined) ? _configsBuildingsFindableItems[cityTypeId] : [];
        let buildingConfig = _configsBuildings[cityTypeId];
        let strings = new Strings();
        
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
            popup.querySelector(".descr_ambiance").innerHTML = strings.nl2br(building.descr_ambiance);
        }
        
        // Display/hide the button for exploring the building
        // TODO: Bug to fix building is considered "explored" as soon as the citizen 
        // enters the zone...
//        if(parseInt(cycleLastVisit) === getCurrentCycle()) {
//            popup.querySelector(".text_explored").classList.remove("hidden");
//            popup.querySelector(".text_unexplored").classList.add("hidden");
//        } else {
            // Add the list of items findable in this building
            let htmlItems = new Items();
            for(let itemId of findableItems) {
                popup.querySelector(".items_list").prepend(htmlItems.item(itemId, _configsItems[itemId]));
            }
//        }
        
        const groundItemsPromise = htmlItems.getGroundItems(mapId, coordX, coordY);
        
        // Add the ground items in the popup
        htmlItems.populateList(".block_ground_items .items_list", groundItemsPromise);
        
        // Display or not the items required for building the construction
        if(_configsBuildingsComponents[cityTypeId] === undefined) {
            popup.querySelector(".block_construction").classList.add("hidden");
        }
        else {
            // Display the components
            this.#populateBuildingComponentsTable(popup, cityTypeId, groundItemsPromise);
            // Hide the "Loading..." bar
            popup.querySelector(".block_construction .components .loader").remove();
        }
        
        
        // Add the line of zombies after the last invaded module
        let lastInvadedModuleId = this.getLastInvadedModuleId(nbrZombiesInZone);
        this.markInactiveModules(popup, lastInvadedModuleId);
        this.#updateModulesZombieRow(popup, nbrZombiesInZone, lastInvadedModuleId);
        
        // If the building can't be explored (city), hide the useless frames 
        // in the pop-up.
        if(buildingConfig["is_explorable"] === 0) {
            popup.querySelector(".block_explore").classList.add("hidden");
        } else {
            popup.querySelector(".block_plans").classList.add("hidden");
            popup.querySelector(".block_modules").classList.add("hidden");
        }
    }
    
    
    async #populateBuildingComponentsTable(popupSelector, cityTypeId, groundItemsPromise) {
        
        const htmlItems = new Items();
        const template = document.querySelector("#tplItemTableRow");
        const rowsFragment = document.createDocumentFragment();
        const groundItems = await groundItemsPromise;
        
        // Remove the item #23 (ID of the "actions points" item in the database)
        const { 23:_, ...buildingComponentsButAp } = _configsBuildingsComponents[cityTypeId];  
        
        Object.entries(buildingComponentsButAp).forEach(([itemIdString, amount]) => {
                const itemId = Number(itemIdString);
                const configItem = _configsItems[itemId];
                const tplItemTableRow = template.content.cloneNode(true);
                const amountOnGround = groundItems[itemId] || 0;
                
                // Name of the item
                const enoughIcon = `${amountOnGround >= amount ? "✅" : "❌"}`;
                tplItemTableRow.querySelector(".item_name").innerText = `${enoughIcon} ${configItem["name"]}`;
                
                // Repeat the item if required in multiple copies
                const itemsListSelector = tplItemTableRow.querySelector(".items_list");
                const itemsFragment = document.createDocumentFragment();
                for(let i = 0; i < amount; i++) {
                    // Gray the item if not enough on the ground
                    const isDisabled = (i >= amountOnGround);
                    itemsFragment.appendChild(htmlItems.item(itemId, configItem, 1, isDisabled));
                }
                itemsListSelector.appendChild(itemsFragment);
                
                rowsFragment.appendChild(tplItemTableRow);
            }
        );
        
        popupSelector.querySelector(".block_construction .components tbody").appendChild(rowsFragment);
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


    #updateModulesZombieRow(popup, nbrZombiesInBuilding, lastInvadedModuleId) {
        
        const table = popup.querySelector(".block_modules table"),
              zombiesRow = table.querySelector(".zombies_row"),
              nbrModules = table.querySelectorAll("tr").length;
        
        // Avoid error if zombies exceeds the number of existing rows
        lastInvadedModuleId = Math.min(nbrModules-1, lastInvadedModuleId);
        
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
