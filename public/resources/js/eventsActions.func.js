/*
 * This script gathers the event listeners specific to the "action blocks" 
 * (move, dig...)
 * Put only functions here, no immediatly executable code.
 */

function listenToActionModeActions() {
    
    listenToDropPickupItem();
}


function listenToDropPickupItem() {
        
    // Drops or pick up an item from the player's bag
    document.querySelector('#block_dig').addEventListener("submit", function(event) {
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
