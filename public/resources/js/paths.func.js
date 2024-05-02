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
                                <h2 class="action_mode_button" data-coords="${htmlCoords}"
                                    title="Cliquez pour prendre le contrôle de ce citoyen"
                                    onclick="switchToCitizen('${citizen.citizen_id}')">
                                    <span class="avatar">&#x1F464;</span>
                                    ${citizen.citizen_pseudo}
                                    <i class="material-icons" style="position:absolute;right:0.1em">zoom_in_map</i>
                                </h2>
                                <div style="display:flex;margin-left:0.3em">└ Sac :
                                    <ul class="items_list"></ul>
                                </div>
                            </li>`;
            htmlHumansIcons += `<img src="/resources/img/free/human.png" height="24">`;
        }
        
        // Populates the HTML template for the current expedition
        template.querySelector(".card").id = htmlId;
        template.querySelector(`#${htmlId} h2`).innerHTML = `Expédition ${pathId} <var class="tag">${nbrKilometers} km</var>`;
        template.querySelectorAll(`#${htmlId} .tab a`)[0].href = `#${htmlId}_path`;
        template.querySelectorAll(`#${htmlId} .tab a`)[1].href = `#${htmlId}_members`;
        template.querySelector(`#${htmlId} .nbr_members`).innerText = members.length;
        template.querySelector(`#${htmlId} .course`).id = `${htmlId}_path`;
        template.querySelector(`#${htmlId} .course .humans`).innerHTML = htmlHumansIcons;
        template.querySelector(`#${htmlId} .members`).id = `${htmlId}_members`;
        template.querySelector(`#${htmlId} .members`).innerHTML = htmlMembers;
        template.querySelector(`#${htmlId} .nbr_kilometers`).innerText = nbrKilometers;
        template.querySelector(`#${htmlId} .first_stage .coords`).innerText = `${firstStage.coord_x}:${firstStage.coord_y}`;
        template.querySelector(`#${htmlId} .last_stage .coords`).innerText   = `${lastStage.coord_x}:${lastStage.coord_y}`;
        template.querySelector(`#${htmlId} .first_stage .localize`).setAttribute("data-coords", `${firstStage.coord_x}_${firstStage.coord_y}`);
        template.querySelector(`#${htmlId} .last_stage .localize`).setAttribute("data-coords", `${lastStage.coord_x}_${lastStage.coord_y}`);
        template.querySelector(`#${htmlId} input[name="params[path_id]"]`).value = pathId;
        
        if(pathsMembers[pathId] !== undefined) {
            let currentStageCoords = `${members[0]["coord_x"]}_${members[0]["coord_y"]}`;
            template.querySelector(`#${htmlId} .current_stage .localize`).setAttribute("data-coords", currentStageCoords);
        }
        
        document.querySelector(`#paths_panel`).append(template);
        
        // Display the content of the bag of each member of the expedition
        for(let member of Object.values(members)) {
            let citizen = _citizens[member.citizen_id];
            for(let bagItem of Object.entries(citizen.bag_items)) {
                document.querySelector(`#paths_panel .citizen${citizen.citizen_id} .items_list`).prepend( htmlItem(bagItem[0], _configsItems[bagItem[0]]) );
            }
        }
    }
    
    // Activate the tabs of the card (not active on page load because 
    // Materialize.css doesn't apply on the <template>)
    M.Tabs.init(document.querySelectorAll("#paths_panel .tabs"));
    
    // Add event listeners on the buttons which center the map on a zone
    listenToLocationButtons(document.querySelectorAll("#paths_panel .localize"));
    // Centers + zoom on the map
    document.querySelector("#paths_panel .action_mode_button").addEventListener("click", switchToActionView);
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
            nbrKilometers = pathsCourses[pathId].length - 1;
        
        // Default if the exepdition is drawn but has no member yet
        if(members === undefined) {
            members = [];
        }
        
        addPathsBarInactivePath(pathId, nbrKilometers, members.length);
        addPathsBarActivePath(pathId);
    }
}


/**
 * Generate the HTML block for a not-yet-clicked expedition
 * (= without the action buttons "move" and "dig")
 * 
 * @param {int} pathId
 * @param {int} nbrKilometers
 * @param {int} nbrMembers
 * @returns {undefined}
 */
async function addPathsBarInactivePath(pathId, nbrKilometers, nbrMembers) {
    
    // Get a blank HTML template for an inactive expedition block
    let template = await document.querySelector("#tplPathsBarInactivePath").content.cloneNode(true);
    let htmlId = `barPath${pathId}`;
    
    template.querySelector(".path").id = htmlId;
    template.querySelector(`#${htmlId} .path_id`).innerHTML = pathId;
    template.querySelector(`#${htmlId} .nbr_kilometers`).innerHTML = nbrKilometers;
    template.querySelector(`#${htmlId} .nbr_members`).innerHTML = nbrMembers;

    document.querySelector("#paths_bar .paths").append(template); 
}


/**
 * Generate the HTML block for an acitve expecition
 * (= with the action buttons "move" and "dig")
 * 
 * @param {int} pathId
 * @returns {undefined}
 */
async function addPathsBarActivePath(pathId) {
    
    // Get a blank HTML template  for an active expedition block
    let template = await document.querySelector("#tplPathsBarActivePath").content.cloneNode(true);
    let inactiveHtmlId = `barPath${pathId}`,
        activeHtmlId = `activeBarPath${pathId}`;
    
    template.querySelector(".path").id = activeHtmlId;
    template.querySelector("h2 .path_id").innerHTML = pathId;
    template.querySelector(`.form_dig_path input[name="params[path_id]"]`).value = pathId;
    template.querySelector(`.form_move_path input[name="params[path_id]"]`).value = pathId;
    
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
    let pathId = event.target.closest(".path").querySelector(".path_id").innerText;
    let inactiveHtmlId = `barPath${pathId}`,
        activeHtmlId = `activeBarPath${pathId}`;
    
    //  Resets the view
    hideClasses(["active"], "paths_bar");
    unhideClasses(["inactive"], "paths_bar");
    // Display the activated block
    hideIds(inactiveHtmlId);
    unhideId(activeHtmlId);
    // Display the path stages on the map for the selected expedition
    hideClasses([`path_stage`]);
    unhideClasses([`path_stage[data-pathid="${pathId}"]`]);
}


/**
 * Draw the course of each expedition on the map
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
                zone = document.querySelector("#zone"+htmlCoords+" .square_container");
                
            zone.insertAdjacentHTML("afterbegin", `<div class="path_stage hidden" data-pathid="${pathId}">${stageId}</div>`);
        }
    }
}
