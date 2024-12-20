/**
 * This script gathers all the actions automatically executed as soon as the page loads.
 * Don't put functions here (see scripts.js) nor events listeners (see events.js)
 */

// Set the language of the game here. Available values: "fr", "en".
// TODO: guess the language automatically by geolocalizing the user.
let lang = "fr";

// Permanently stores the results returned by the Azimutant's APIs.
var _citizens = null;
var _cities = null;
var _myZone = null;
var _jsonMap = null;
var _isPathDrawingActive = false;
var _userEmail = null;
// Permanently stores the result of the API whichs gives the discussions list 
var _jsonDiscussionApi = null;
var _scrollBoosterInstance = null;

var _defaultMapZoomPercent = 130;


// Main burger menu (uses Materialize.css)
document.addEventListener('DOMContentLoaded', function() {
    // Activate the lateral main menu of the site
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);
    // Activate the <select> tags
    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems);
    // Activate the tabs
    var elems = document.querySelectorAll('.tabs');
    var instances = M.Tabs.init(elems);    
    // Activate the Materialize's tootips
    var elems = document.querySelectorAll('.tooltipped');
    var instances = M.Tooltip.init(elems);
    // Activate the collapsible texts
    var elems = document.querySelectorAll('.collapsible');
    var instances = M.Collapsible.init(elems);
    
    // Load the tutorial
    var elems = document.querySelectorAll('.tap-target');
    var tutorialInstances = M.TapTarget.init(elems, []);    
    
    // Fix #2 (part 1/2): the features placed in absolute-positioned block enlarge the page
    // If the element is in the #actions_block panel, the tutorial will create 
    // an horizontal scrollbar, even when the tutorial is not displayed. 
    // Cause: Materialize seems not to like placing tap-target inside blocks having 
    // an absolute position.
//    for(let elem of elems) {
//        // For the features we have marked as needing the fix (marked by the homemade "fix-position" class),
//        // go back to the .tap-target-wrapper parent (created on-the-fly by Materialize)
//        // and modify its position to keep it inside the width of the page.
//        if(elem.classList.contains("fix-position") === true) {
//            elem.parentNode.style.left = 0;
//            // Remove the "abolute position", because it shifts the attack bar
//            // (normally fixed at the bottom) ou of the screen
//            elem.parentNode.style.position = "";
//        }
//    }
    
//    document.querySelector("#launchTutorial").addEventListener("click", function() {
//        launchTutorial(elems, tutorialInstances, 0);
//    });
  });
// Initialize collapsible (uncomment the lines below if you use the dropdown variation)
// var collapsibleElem = document.querySelector('.collapsible');
// var collapsibleInstance = M.Collapsible.init(collapsibleElem, options);


// If we are on the main game page (those elements don't exist on the connection page)
if (document.getElementById('map') !== null) {
    
    // Default map to show if the visitor is not connected
    var mapId = document.querySelector("#gameData #mapId").innerHTML;
    // Get the unvariable data of the game (building names...) stored in the HTML
    var _configsBuildings = JSON.parse(document.querySelector("#configs .buildings").innerHTML);
    var _configsBuildingsFindableItems = JSON.parse(document.querySelector("#configs .buildings_findable_items").innerHTML);
    var _configsItems     = JSON.parse(document.querySelector("#configs .items").innerHTML);
    var _configsMap       = JSON.parse(document.querySelector("#configs .map").innerHTML);
    
    _jsonMap = getMapZonesOnce(mapId);
    
    // Place on the map the buildings and cities
    _cities = addCitiesOnMap(mapId);
    let cityConnections = new CityConnections();
    cityConnections.addCityframes(mapId);
    // Place the citizens on the appropriate zones of the map
    _citizens = addCitizensOnMap(mapId).then(() => {
        if(isCitizenInGame() === true) { addMeOnMap(); }
        });
    // Display the zombie cores on the map (item ID #106)
    displayItemOnMap(106);
    
    // Update the red counters at the bottom of the map
    updateCitiesCounter();
    updateZombieCoresCounter();
    
    // Allows to move the map by dragging it with the mouse
    _scrollBoosterInstance = listenToMapDragging();
    
    // Only if the visitor is connected
    if(isCitizenInGame() === true) {
        setTimeout(function() {
            var myCityZoneId = getMyCityZoneId();
            centerMapOnMe();
            // Add a location sign above the city of the player
            addCityLocationMarker(myCityZoneId);
            // Start in the "action" mode (centered on the current player)
//            setTimeout(switchToActionView, 500);

            // Draws a line between the player and his city
//            if(myCityZoneId !== null) {
//                updateLineBetweenZones("myCity", "#me", "#"+myCityZoneId);
//            }
            // Add the number of defenses required in the tasks list
            let tasks = new Tasks();
            tasks.populateTaskDefenses();
            
            // Ask for chosing a citizen speciality (builder, digger...)
            let citizenId = Number(document.querySelector("#citizenId").innerText);
            if(Number(_citizens[citizenId]["last_specialization_cycle"]) < getCurrentCycle()) {
                window.location.hash = "#popspecialize";
            }
        }, 1000);
    } else {
        document.querySelector("#views_bar").classList.add("hidden");
        document.querySelector("#map_navigation").classList.add("hidden");
        document.querySelector("#tasks_button").classList.add("hidden");
    }
    
    // Restore the display of the action button before the page was refreshed
//    toggleActionBlock(getCookieConfig("round_button"));
    
    // By default, the list of objects in the bag and on the ground are reduced
    // UNUSED : the panel is now replaced by the big action button at the right of the map
//    if (getCookie('showitemspanel') === null || getCookie('showitemspanel') === '0') {
//        toggleItemsPanel();
//    }
      
    // Countdown before the next zombie attack
//    attackCountdown();
//    setInterval(attackCountdown, 1000);
    
    // The map looks better at 130% than with smaller zones
    zoomMapRange(_defaultMapZoomPercent);
    
    // Server-sent events to update the map in real time
    var timestamp = Math.floor(Date.now()/1000);
    setTimeout(async function() {
        // NB: keep the ".php" extension, otherwise it will give a "CORS error" with the local API version
        let evtSource = new EventSource(await getOfficialServerRoot()+"/api/sse.php");
        // NB: common pitfall: the unamed SSE events (= no "event:" line in the SSE message)
        // can be catched in javascript with "evtSource.onmessage". But when the events are named,
        // like Azimutant does, they *must* be catched with addEventListener(). 
        // Onmessage *never* works with named events.
        evtSource.addEventListener("updatezones", async function(event) { 
            timestamp = await updateMapRealtime(event, timestamp);
        });
    }, 1000);
}


// If we are inside a city
if (document.getElementById('city_container') !== null) {    
    // By default, loads the first tab of the city
    var search_params = new URLSearchParams(window.location.search);
    var urlTabId = search_params.get('tab');
    var cityMenuId = (urlTabId !== null) ? urlTabId : "cityMenuCity";
    switchCitySubmenu(cityMenuId);
    
    // If we are on the landing page of the city (= no city block open)
    if(cityMenuId === "cityMenuCity" ) {
        display("#city_submenus");
        display("#city_defenses");
    }
}


// Countdown to escape once the humans have lost the control of the zone
if (document.getElementById("controlCountdown") !== null) {
    setInterval(controlCountdown, 1000);
}


//If we are on the panel to edit the items available in game
if (document.getElementById('editConfig') !== null) {
    
    // On page load, we hide by default all the secondary options of the form
    hide([  '#block_findable',
            '#block_findable_advanced',
            '#block_compo',
            '#block_apgain',
            '#block_malus',
            '#block_healing',
            '#block_weapon',
            '#block_bag',
            '#block_drop',
            '#block_loads',
            '#block_solidity_custom',
            '#block_killing_rate'
            ]);
}


// If we are on the connection page
if(document.querySelector("#connectionForm") !== null) {
    // Prefill the "email" field
    setTimeout(function() {
        _userEmail = getCookie("email");
        if(_userEmail !== null) {
            document.querySelector("#connectionForm input[name='email']").value = _userEmail;
        }
    }, 1000);
}


// Translate the game in the appropriate language
translator = new Translator();
translator.translate(lang);
