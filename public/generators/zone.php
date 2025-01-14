<?php
/**
 * Generates the HTML for a limited list of zones.
 * Useful for the real time refreshing (refresh the modified zones).
 */

require_once '../../core/controller/autoload.php';
safely_require('/core/model/Server.php');
safely_require('/core/controller/SortGameData.php');
safely_require('/core/ZombLib.php');

header('content-type:application/json');

// You must give all these parameters in the url when you call the generator
$map_id     = filter_input(INPUT_GET, 'map_id',     FILTER_VALIDATE_INT);
$newerthan  = filter_input(INPUT_GET, 'newerthan',  FILTER_VALIDATE_INT);
$citizen_id = filter_input(INPUT_GET, 'citizen_id', FILTER_VALIDATE_INT);

$server = new Server();
$official_server_root = $server->official_server_root();
$map           = new HtmlMap();
$sort          = new SortGameData();
$api           = new ZombLib($official_server_root.'/api');
$html_zones    = [];
$player_coords = [];

// Get the modified zones
$zones = $api->call_api('maps', 'get', ['map_id'=>$map_id, 'newerthan'=>$newerthan]);

// If zones have been modified
if ($zones['metas']['error_code'] === 'success') {
    
    // Build the HTML of the modified zones
    foreach ($zones['datas']['zones'] as $coords=>$zone) {
    
        // Parse the string of coords list
        list($col, $row) = array_map('intval', explode('_', $coords));
        
        $html_zones[$coords] = $map->hexagonal_zone($col, $row, $zone, null, 1);
    }
}
else {
    $html_zones = $zones;
}


echo json_encode($html_zones);
