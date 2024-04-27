/* 
 * Functions relative to the expeditions on the map.
 * Don't put executable code in this file, only functions.
 */


/**
 * Create a card for each expedition in the list of expeditions (lateral panel)
 * 
 * @param {object} pathsCourses The coordinates of all the zones constituting the path.
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
            let htmlCoords = `${member.coord_x}_${member.coord_y}`;
            htmlMembers += `<li>&#x1F464; <strong>${member.citizen_pseudo}</strong> <a class="localize" data-coords="${htmlCoords}"><i class="material-icons">my_location</i></a></li>`;
            htmlHumansIcons += `<img src="/resources/img/free/human.png" height="24">`;
        }
        
        // Populates the HTML template for the current expedition
        template.querySelector(".card").id = htmlId;
        template.querySelector(`#${htmlId} h2`).innerHTML = `Expédition ${pathId} <var style="">${nbrKilometers} km</var>`;
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
        
        document.querySelector(`#paths_panel`).append(template);
    }
    
    // Activate the tabs of the card (not active on page load because 
    // Materialize.css doesn't apply on the <template>)
    M.Tabs.init(document.querySelectorAll("#paths_panel .tabs"));
    
    // Add event listeners on the buttons which center the map on a zone
    listenToLocationButtons(document.querySelectorAll("#paths_panel .localize"));
}


/**
 * Draw the course of each expedition on the map
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
                
            zone.insertAdjacentHTML("afterbegin", `<div class="path_stage">${stageId}</div>`);
        }
    }
}
