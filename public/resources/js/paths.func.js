/* 
 * Functions relative to the expeditions on the map.
 * Don't put executable code in this file, only functions.
 */


/**
 * Create a card for each expedition in the list of expeditions (lateral panel)
 * 
 * @param {object} pathsCourses The coordinates of all the zones constituting the path,
 *                               as returned by the Invazion's API
 * @param {object} pathsMembers The members of the expeditions, 
 *                              as returned by the Invazion's API
 * @returns {undefined}
 */
async function populatePathsPanel(pathsCourses, pathsMembers) {
    
    let htmlAvailableCitizens = await htmlcitizensForExpedition();
    
    // For each expedition
    for(let paths of Object.entries(pathsCourses)) {
        // Gets a blank HTML template of an expedition card
        let template = await document.querySelector("#tplPath").content.cloneNode(true);
            pathId = paths[0],
            htmlId = `path${pathId}`,
            stages = pathsCourses[pathId],
            members = pathsMembers[pathId],
            firstStage = stages[0],
            lastStage = stages.slice(-1)[0],
            nbrKilometers = stages.length - 1;
        
        // Default if the exepdition is drawn but has no member yet
        if(members === undefined) {
            members = [];
        }
    
        // Builds the list of the members of the current expedition
        let htmlMembers = "",
            htmlHumansIcons = "";
        for(let member of Object.values(members)) {
            let citizen = _citizens[member.citizen_id];
            let htmlCoords = `${citizen.coord_x}_${citizen.coord_y}`;
            
            htmlMembers += `<li class="card citizen${citizen.citizen_id}">
                                <h2 class="action_mode_button z-depth-1" data-coords="${htmlCoords}">
                                    <span class="avatar">&#x1F464;</span>
                                    ${citizen.citizen_pseudo}
                                    <button class="redbutton"
                                        title="Agir avec ce citoyen"
                                        onclick="switchToCitizen('${citizen.citizen_id}')"
                                        >
                                        <i class="material-icons">zoom_in_map</i>
                                    </button>
                                </h2>
                                <div style="display:flex;margin-left:0.3em">└ Sac :
                                    <ul class="items_list"></ul>
                                </div>
                            </li>`;
            htmlHumansIcons += `<img src="/resources/img/free/human.png" height="24">`;
        }
           
        // Populates the HTML template for the current expedition
        template.querySelector(".card").id = htmlId;
        template.querySelector(`#${htmlId} h2`).innerHTML = `Expédition ${firstStage.coord_x}:${firstStage.coord_y} <var class="tag">${nbrKilometers} km</var>`;
        template.querySelectorAll(`#${htmlId} .tab a`)[0].href = `#${htmlId}_path`;
        template.querySelectorAll(`#${htmlId} .tab a`)[1].href = `#${htmlId}_members`;
        template.querySelector(`#${htmlId} .nbr_members`).innerText = members.length;
        template.querySelector(`#${htmlId} .course`).id = `${htmlId}_path`;
        template.querySelector(`#${htmlId} .course .humans`).innerHTML = htmlHumansIcons;
        template.querySelector(`#${htmlId} .members`).id = `${htmlId}_members`;
        template.querySelector(`#${htmlId} .current_members`).innerHTML = htmlMembers;
        template.querySelector(`#${htmlId} .nbr_kilometers`).innerText = nbrKilometers;
        template.querySelector(`#${htmlId} .first_stage .coords`).innerText = `${firstStage.coord_x}:${firstStage.coord_y}`;
        template.querySelector(`#${htmlId} .last_stage .coords`).innerText   = `${lastStage.coord_x}:${lastStage.coord_y}`;
        template.querySelector(`#${htmlId} .first_stage .localize`).setAttribute("data-coords", `${firstStage.coord_x}_${firstStage.coord_y}`);
        template.querySelector(`#${htmlId} .last_stage .localize`).setAttribute("data-coords", `${lastStage.coord_x}_${lastStage.coord_y}`);
//        template.querySelector(`#${htmlId} input[name="params[path_id]"]`).value = pathId;
        
        if(pathsMembers[pathId] !== undefined) {
            let currentStageCoords = `${members[0]["coord_x"]}_${members[0]["coord_y"]}`;
            template.querySelector(`#${htmlId} .current_stage .localize`).setAttribute("data-coords", currentStageCoords);
        }
        
        document.querySelector(`#paths_panel .body`).append(template);
        
        // Display the content of the bag of each member of the expedition
        for(let member of Object.values(members)) {
            let citizen = _citizens[member.citizen_id];
            for(let bagItem of Object.entries(citizen.bag_items)) {
                document.querySelector(`#paths_panel .citizen${citizen.citizen_id} .items_list`).prepend( htmlItem(bagItem[0], _configsItems[bagItem[0]]) );
            }
        }
             
        // List of citizens available to populate the expedition
        if(members.length === 0) {
            display(`#${htmlId} .choose_members`);            
            document.querySelector(`#${htmlId} form[name="available_members"] ul`).innerHTML = htmlAvailableCitizens;
            document.querySelector(`#${htmlId} form input[name="path_id"`).value = pathId;
        }
    }
    
    // Activate the tabs of the card (not active on page load because 
    // Materialize.css doesn't apply on the <template>)
    M.Tabs.init(document.querySelectorAll("#paths_panel .tabs"));
    
    // Add event listeners on the buttons which center the map on a zone
    listenToLocationButtons(document.querySelectorAll("#paths_panel .localize"));
    // Center + zoom on the map
    listenToActionModeButtons(document.querySelectorAll("#paths_panel .action_mode_button"));
}


/**
 * HTML list of the citizens that you can add as members of an expedition
 * 
 * @returns {string} HTML
 */
async function htmlcitizensForExpedition() {
    
    htmlAvailableCitizens = "";

    for(let citizenId in await _citizens) {
        let citizen = _citizens[citizenId];
        let htmlCoords = `${citizen.coord_x}_${citizen.coord_y}`;

        htmlAvailableCitizens += `<li class="card citizen${citizen.citizen_id}">
                            <h2 data-coords="${htmlCoords}" style="background:lightgrey">
                                <label>
                                    <input type="checkbox" class="filled-in" name="citizens_ids[]" value="${citizen.citizen_id}">
                                    <span>${citizen.citizen_pseudo}</span>
                                </label>
                                <button class="redbutton"
                                    title="Agir avec ce citoyen"
                                    onclick="switchToCitizen('${citizen.citizen_id}');return false"
                                    >
                                    <i class="material-icons">zoom_in_map</i>
                                </button>
                            </h2>
                            <div style="display:flex;margin-left:0.3em">└ Sac :
                                <ul class="items_list"></ul>
                            </div>
                        </li>`;
    }
    
    return htmlAvailableCitizens;
}


/**
 * Create a card for each expedition in the list of expeditions (lateral panel)
 * 
 * @param {object} pathsCourses The coordinates of all the zones constituting the paths, 
 *                              as returned by the Invazion's API
 * @param {object} pathsMembers The members of the expeditions, 
 *                              as returned by the Invazion's API
 * @returns {undefined}
 */
async function populatePathsBar(pathsCourses, pathsMembers) {
    
    // For each expedition
    for(let path of Object.entries(pathsCourses)) {
        let pathId = path[0],
            members = pathsMembers[pathId],
            firstStage = pathsCourses[pathId][0],
            nbrKilometers = pathsCourses[pathId].length - 1;
        
        // Default if the exepdition is drawn but has no member yet
        if(members === undefined) {
            members = [];
        }
        
        addPathsBarInactivePath(pathId, firstStage["coord_x"], firstStage["coord_y"], nbrKilometers, members.length);
        addPathsBarActivePath(pathId, members.length, firstStage["coord_x"], firstStage["coord_y"]);
    }
}


/**
 * Generate the HTML block for a not-yet-clicked expedition
 * (= without the action buttons "move" and "dig")
 * 
 * @param {int} pathId
 * @param {int} firstStageX
 * @param {int} firstStageY
 * @param {int} nbrKilometers
 * @param {int} nbrMembers
 * @returns {undefined}
 */
async function addPathsBarInactivePath(pathId, firstStageX, firstStageY, nbrKilometers, nbrMembers) {
    
    // Get a blank HTML template for an inactive expedition block
    let template = await document.querySelector("#tplPathsBarInactivePath").content.cloneNode(true);
    let htmlId = `barPath${pathId}`;
    
    template.querySelector(".path").id = htmlId;
    template.querySelector(`#${htmlId}`).setAttribute("data-pathid", pathId);
    template.querySelector(`#${htmlId} .path_name`).innerText = `${firstStageX}:${firstStageY}`;
    template.querySelector(`#${htmlId} .nbr_kilometers`).innerText = nbrKilometers;
    template.querySelector(`#${htmlId} .nbr_members`).innerHTML = nbrMembers+" membres";
    
    if(nbrMembers === 0) {
        template.querySelector(`#${htmlId} .nbr_members`).innerHTML = "&#x26A0;&#xFE0F; 0 membre";
        template.querySelector(`#${htmlId} .nbr_members`).classList.add("alert");
    }

    document.querySelector("#paths_bar .paths").append(template); 
}


/**
 * Generate the HTML block for an acitve expecition
 * (= with the action buttons "move" and "dig")
 * 
 * @param {int} pathId
 * @param {int} nbrMembers
 * @param {int} firstStageX
 * @param {int} firstStageY
 * @returns {undefined}
 */
async function addPathsBarActivePath(pathId, nbrMembers, firstStageX, firstStageY) {
    
    // Get a blank HTML template  for an active expedition block
    let template = await document.querySelector("#tplPathsBarActivePath").content.cloneNode(true);
    let inactiveHtmlId = `barPath${pathId}`,
        activeHtmlId = `activeBarPath${pathId}`;
    
    template.querySelector(".path").id = activeHtmlId;
    template.querySelector("h2 .path_name").innerText = `${firstStageX}:${firstStageY}`;
    template.querySelector('form[name="dig_path"] input[name="params[path_id]"]').value = pathId;
    template.querySelector('form[name="move_path"] input[name="params[path_id]"]').value = pathId;
    
    if(nbrMembers === 0) {
        template.querySelector('form[name="dig_path"]').classList.add("hidden");
        template.querySelector('form[name="move_path"]').classList.add("hidden");
        template.querySelector('form[name="expert_path"]').classList.add("hidden");
        template.querySelector('form[name="populate_path"]').classList.remove("hidden");
    }
    
    document.querySelector(`#${inactiveHtmlId}`).after(template);
}


/**
 * Switch between the expeditions when clicking in the paths bar
 * 
 * @param {type} event
 * @returns {undefined}
 */
async function activatePathsBarPath(event) {
    
    // Get the data from the inactive expedition card
    let pathId = event.target.closest(".path").getAttribute("data-pathid");
    
    //  Resets the view
    hide("#paths_bar .active");
    display("#paths_bar .inactive");
    // Display the activated block
    hide(`#barPath${pathId}`);
    display(`#activeBarPath${pathId}`);
    // Display the path stages on the map for the selected expedition
    hide(".path_stage");
    display(`.path_stage[data-pathid="${pathId}"]`);
    
    // Center the map on the first stage of the selected expedition
    firstStageZoneHtmlId = document.querySelector(`#map .path_stage[data-pathid="${pathId}"]`).closest(".hexagon").id;
    centerMapOnZone(firstStageZoneHtmlId);
}


/**
 * Create a new expedition
 * 
 * @returns {undefined}
 */
function startPathCreation() {
    
    // Hide the useless elements overloading the map
    hide(['#paths_bar', '#resizeMap', '#attack_bar']);
    hide('#map .bubble');
    // Start the steps to create an expedition
    display('#formPathDrawing');
}


/**
 * Draw the course of each existing expedition on the map
 * (hidden by default)
 * 
 * @param {object} pathsCourses The coordinates of all the zones constituting the path.
 * @returns {undefined}
 */
function drawPathsOnMap(pathsCourses) {
    
    // For each expedition to draw on the map
    for(let paths of Object.entries(pathsCourses)) {
        let pathId = paths[0];
        let pathStages = paths[1];
        
        // For each zone (stage) constituting the expedition
        for(let stages of Object.entries(pathStages)) {
            let stageId = stages[0],
                stageCoords = stages[1],
                htmlCoords = stageCoords.coord_x+"_"+stageCoords.coord_y,
                zone = document.querySelector("#zone"+htmlCoords);
                
            zone.querySelector(".square_container").insertAdjacentHTML("afterbegin", `<div class="path_stage hidden" data-pathid="${pathId}">${stageId}</div>`);
            zone.style.opacity = 1;
        }
    }
}


/**
 * Send to Invazion's API the form containing the stage of the new path to create
 * 
 * @param {Object} event
 * @returns {undefined}
 */
async function submitNewPath(event, controller) {
    
    let token = getCookie('token');
    let formData = new FormData(event.target);
    let zonesList = formData.getAll('zones[]');
    let zonesString = zonesList.join('&zones[]=');
    
    // Send the data to the Invazion's API
    let json = await callApi("GET", "paths", `action=add&zones[]=${zonesString}&token=${token}`);
    
    // Display the message of result (success or error) in a toast
    displayToast(json.metas.error_message, json.metas.error_class);
    
    // If path successfully register, hide the bar for drawing a path
    if(json.metas.error_code === "success") {
        // Display again the GUI elements previously masked
        hide("#formPathDrawing");
        display(["#paths_bar", "#resizeMap", "#attack_bar", "#map .bubble"]);
        
        // Reset the form with the list of stages
        document.querySelector("#formPathDrawing .fields").innerText = "";
        
        // Unregister the event listener on the zone which adds stages by clicking
        // Details here: https://macarthur.me/posts/options-for-removing-event-listeners/
        controller.abort();
    }
}


/**
 * Attach citizens to a path
 * 
 * @param {object} event
 * @returns {undefined}
 */
async function addPathMembers(event) {
    
    let token = getCookie('token'),
        formData = new FormData(event.target),
        pathId = formData.get('path_id'),
        selectedCitizensIds = formData.getAll('citizens_ids[]'),
        citizensString = selectedCitizensIds.join('&citizens_ids[]=');
    
    // Send the data to the Invazion's API
    let json = await callApi("GET", "paths", `action=add_members&path_id=${pathId}&citizens_ids[]=${citizensString}&token=${token}`);
    
    // Display the message of result (success or error) in a toast
    displayToast(json.metas.error_message, json.metas.error_class);
    
    // Removes the form to select members in the card of the path
    if(json.metas.error_class) {
        document.querySelector(`#path${pathId} .choose_members`).remove();
        document.querySelector(`#path${pathId} .nbr_members`).innerText = selectedCitizensIds.length;
    }
}


async function movePath(event) {
    
    let pathId = event.target.querySelector('input[name="params[path_id]"]').value;
    
    let json = await callApi("GET", "paths", `action=move&path_id=${pathId}`);

    // Display the eventual error in a toast
    if(json.metas.error_code !== "success") {
        displayToast(json.metas.error_message, json.metas.error_class);
    }
    else {
        updateMeAfterMoving(json.datas.new_coord_x, json.datas.new_coord_y);
    }
}
