/**
 * This script gathers all the actions automatically executed as soon as the page loads.
 * Don't put functions here (see scripts.js) nor events listeners (see events.js)
 */

// Permanently stores the results returned by the Invazion's APIs.
var _citizens = null;
var _myZone = null;
// Get the unvariable data of the game (building names...) stored in the HTML
var _configsBuildings = JSON.parse(document.querySelector("#configs .buildings").innerHTML);
var _configsItems     = JSON.parse(document.querySelector("#configs .items").innerHTML);
var _configsMap       = JSON.parse(document.querySelector("#configs .map").innerHTML);
// Permanently stores the result of the API whichs gives the discussions list 
var _jsonDiscussionApi = null;


// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Default map to show if the visitor is not connected
    let mapId = 1;
    
    // Only if the visitor is connected
    if(document.querySelector("#citizenId").innerHTML !== "") {
    
        let mapId = document.querySelector("#gameData #mapId").innerHTML;
        // Place the current player on the appropriate zone of the map
        addMeOnMap();
        // Add a location sign above the city of the player
        addCityLocationMarker();
        
        // Get informations about the current zone through the "data-*" HTML attributes
        let zoneData = document.querySelector("#me").parentNode.dataset;
        let myHexagon = document.getElementById("me").closest(".hexagon");
           
        // Highlights the player's location on page load
        displayTooltip(myHexagon);
        // Updates the coordinates of the player in the movement paddle
        updateMovementPaddle(zoneData.coordx, zoneData.coordy);
        // Updates the distance to the city displayed under the movement paddle
        updateCityDistance(zoneData.coordx, zoneData.coordy);     
        // Displays the button to enter if there is a city in the zone
        updateEnterBuildingButton(zoneData.citytypeid);
        // Updates the coordinates of the player in the land editor
        updateMapEditor(zoneData.coordx, zoneData.coordy);
        // Update the numbers in the big buttons next to the map
        updateRoundActionButtons(zoneData.coordx, zoneData.coordy);
        // Display an alert over the movement paddle if the player is blocked
        updateBlockAlertControl(zoneData.controlpointscitizens, zoneData.controlpointszombies);
        // Display the actions for fighting against zombies
        showFightingZombiesButtons(zoneData.zombies);

        // Restore the display of the player zone over the map before the page was refreshed
        if (getCookieConfig('show_zone') === 1) {
            toogleMyZone();
        }
    }
    
    // Place the citizens on the appropriate zones of the map
    addCitizensOnMap(mapId);  
    
    // Place on the map the buildings and cities
    replaceBuildingsPlaceholders();    
    
    
    // Restore the display of the action button before the page was refreshed
//    toggleActionBlock(getCookieConfig("round_button"));
    
    // By default, the list of objects in the bag and on the ground are reduced
    // UNUSED : the panel is now replaced by the big action button at the right of the map
//    if (getCookie('showitemspanel') === null || getCookie('showitemspanel') === '0') {
//        toggleItemsPanel();
//    }
      
    // Displays the active tab of the in-game smartphone
    activatePhoneTab();
    
    // Countdown before the next zombie attack
//    attackCountdown();
//    setInterval(attackCountdown, 1000);
        
    // Server-sent events to update the map in real time
    var timestamp = Math.floor(Date.now()/1000);
    setTimeout(function() {
        // NB: keep the ".php" extension, otherwise it will give a "CORS error" with the local API version
        let evtSource = new EventSource(getOfficialServerRoot()+"/api/sse.php");
        evtSource.onmessage = async function(event) { 
            timestamp = await UpdateMapRealtime(event, timestamp);
        };
    }, 1000);
}


// If we are inside a city
if (document.getElementById('city_container') !== null) {    
    // By default, loads the first tab of the city
    var search_params = new URLSearchParams(window.location.search);
    switchCitySubmenu(search_params.get('tab'));
}


// Countdown to escape once the humans have lost the control of the zone
if (document.getElementById("controlCountdown") !== null) {
    setInterval(controlCountdown, 1000);
}


//If we are on the panel to edit the items available in game
if (document.getElementById('editConfig') !== null) {
    
    // On page load, we hide by default all the secondary options of the form
    hide([  'block_findable',
            'block_findable_advanced',
            'block_compo',
            'block_apgain',
            'block_malus',
            'block_healing',
            'block_weapon',
            'block_bag',
            'block_drop',
            'block_loads',
            'block_solidity_custom',
            'block_killing_rate'
            ]);
}
