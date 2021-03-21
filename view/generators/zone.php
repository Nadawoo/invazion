<?php
/**
 * Generates the HTML for a limited list of zones.
 * Useful for the real time refreshing (refresh the modified zones).
 */

require_once '../../controller/autoload.php';
safely_require('/controller/official_server_root.php');
safely_require('/controller/SortGameData.php');
safely_require('/ZombLib.php');

header('content-type:application/json');

// You must give all these parameters in the url when you call the generator
$map_id     = filter_input(INPUT_GET, 'map_id',     FILTER_VALIDATE_INT);
$newerthan  = filter_input(INPUT_GET, 'newerthan',  FILTER_VALIDATE_INT);
$citizen_id = filter_input(INPUT_GET, 'citizen_id', FILTER_VALIDATE_INT);

$map           = new HtmlMap();
$sort          = new SortGameData();
$api           = new ZombLib(official_server_root().'/api');
$html_zones    = [];
$player_coords = [];
$player_pseudo = null;

// Get the modified zones
$zones = $api->call_api('maps', 'get', ['map_id'=>$map_id, 'newerthan'=>$newerthan]);

// If zones have been modified
if ($zones['metas']['error_code'] === 'success') {
    
    // Get the citizens in the modified zones
    $citizens = $api->call_api('citizens', 'get', ['map_id'=>$map_id, 'zones'=>array_keys($zones['datas']['zones'])])['datas'];
    $citizens_by_coord = $sort->sort_citizens_by_coord($citizens);
    
    // Data of the connected player
    if (isset($citizens[$citizen_id])) {
        $player_pseudo = $citizens[$citizen_id]['citizen_pseudo'];
        $player_coords = [$citizens[$citizen_id]['coord_x'], $citizens[$citizen_id]['coord_y']];
        $player_city_id = $citizens[$citizen_id]['city_id'];
    }
    
    // Build the HTML of the modified zones
    foreach ($zones['datas']['zones'] as $coords=>$zone) {
    
        // Parse the string of coords list
        list($col, $row) = array_map('intval', explode('_', $coords));
        // Pseudo of one of the citizens in the zone
        $fellow_pseudo = (isset($citizens_by_coord[$coords])) ? $citizens_by_coord[$coords][0]['citizen_pseudo'] : null;
        $is_player_in_zone = $map->is_player_in_zone([$col, $row], $player_coords);
        
        $html_zones[$coords] = $map->hexagonal_zone($col, $row, $zone, $is_player_in_zone, 
                                                    $player_pseudo, $player_city_id, $fellow_pseudo);
    }
}


echo json_encode($html_zones);
