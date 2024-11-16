/*
 * This script gathers the event listeners specific to the "action blocks" 
 * (move, dig...)
 * Put only functions here, no immediatly executable code.
 */

function listenToActionModeActions() {
     
    listenToMovementPaddle();
    listenToDigButton();
    listenToDropPickupItem();
    listenToExplorationButton();
    listenToLandform();
 }
 
 
function listenToMovementPaddle() {

   // Move the citizen on the map
    document.querySelector('#block_move [name="move"]').addEventListener("submit", async function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        moveCitizen(event.submitter.value);
 //        let myCityZoneId = await getMyCityZoneId();
 //        if(myCityZoneId !== null) {
 //            setTimeout(function() {updateLineBetweenZones("myCity", "#me", "#"+myCityZoneId);}, 1000);
 //        }
    });
}


function listenToDigButton() {
    
    // Digs a zone to find items
    document.querySelector('#block_dig form[name="dig"]').addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        dig();
    });
}


function listenToDropPickupItem() {
        
    // Drops or pick up an item from the player's bag
    document.querySelector('#block_dig').addEventListener("submit", function() {
        let formType = event.target.closest("form").className;
        // NB: this condition avoids interferences with the other forms in the block
        // (actions specific to the item, e.g. to eat a burger)
        if(formType === "form_drop") {
            // Desactivate the classic submission button (avoids reloading the page)
            event.preventDefault();
            dropItem(event.submitter);
        }
        else if(formType === "form_pickup") {
            event.preventDefault();
            pickupItem(event.submitter);
        }
    });
}


function listenToExplorationButton() {
    
    // [On PC] Open the details of a building when clicking on it
    document.getElementById("button_explore").addEventListener("click", function(){
            let buildingPopup = new BuildingPopup();
            buildingPopup.openBuildingPopup(event);
        },
        { passive: true }
    );
}


/**
 * The form to edit the ground type of a zone (lava, grass...)
 * @returns {undefined}
 */
async function listenToLandform() {
    
    document.getElementById("landform").addEventListener("submit", function() {
        // Desactivate the classic submission button (avoids reloading the page)
        event.preventDefault();
        
        let fields = document.getElementById("landform").elements,
            landType = event.submitter.value,
            x = fields["coord_x"].value,
            y = fields["coord_y"].value,
            radius = fields["radius"].value;
        
        updateLandType(landType, x, y, radius);
    });
}
