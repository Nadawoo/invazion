/**
 * This script gathers all the actions automatically executed as soon as the page loads.
 * Don't put functions here (see scripts.js) nor events listeners (see events.js)
 */



// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
       
    // Restore the display of the player zone over the map before the page was refreshed
    if (getCookieConfig('show_zone') === 1) {
        toogleMyZone();
    }
    
    // Restore the display of the action button before the page was refreshed
    toggleActionBlock(getCookieConfig("round_button"));
    
    // By default, the list of objects in the bag and on the ground are reduced
    // UNUSED : the panel is now replaced by the big action button at the right of the map
//    if (getCookie('showitemspanel') === null || getCookie('showitemspanel') === '0') {
//        toggleItemsPanel();
//    }
     
    // Displays the active tab of the in-game du smartphone
    activatePhoneTab();
    
    
    // Countdown before the next zombie attack
    attackCountdown();
    setInterval(attackCountdown, 1000);
        
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
