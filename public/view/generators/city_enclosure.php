<?php
/**
 * Call this page to generate the HTML elements of the city enclosure 
 * (personal chest, city repository...)
 * Useful to refresh the data of the city interface in javascript.
 */

require_once '../../../core/controller/autoload.php';
safely_require('/core/controller/official_server_root.php');
safely_require('/core/ZombLib.php');

header('content-type:application/json');


/**
 * You must give all these parameters in the url when you call this generator
 */
$get['map_id']     = filter_input(INPUT_GET, 'map_id',     FILTER_VALIDATE_INT);
// The ID of the destination city
$get['city_id']    = filter_input(INPUT_GET, 'city_id',    FILTER_VALIDATE_INT);
// Coordinates of the citizen
$get['coord_x']    = filter_input(INPUT_GET, 'coord_x',    FILTER_VALIDATE_INT);
$get['coord_y']    = filter_input(INPUT_GET, 'coord_y',    FILTER_VALIDATE_INT);


// Display an error if the parameters in the calling URL are invalid
foreach($get as $key=>$value) {
    
    if($value === null) {
        // TODO: raise errors in a cleaner way (with try/catch or error in the API result)
        echo '[Error] The parameter "'.$key.'" is missing. Add it in the URL.';
        exit;
    }
    elseif($value === false) {
        echo '[Error] The parameter "'.$key.'" in the URL is invalid. Check that '
           . 'its value is in the expected type (numeric, string...).';
        exit;
    }
}


$api        = new ZombLib(official_server_root().'/api');
$layout     = new HtmlLayout();
$enclosure  = new HtmlCityEnclosure();
$coord      = $get['coord_x'].'_'.$get['coord_y'];

// Get the updated data of the items in the zone
$configs         = $api->call_api('configs', 'get')['datas'];
$zone_api_result = $api->call_api('maps', 'get', ['map_id'=>$get['map_id'], 'zones'=>$coord]);
$zone_datas      = $zone_api_result['datas']['zones'][$coord];

// Generate the HTML of the player's personal chest
$html['zone_items'] = $layout->block_zone_items($configs['items'], $zone_datas);


echo json_encode([
        'metas' => [],
        'datas' => [
            'html_home_storage' => $enclosure->block_home_storage($html['zone_items']),
            'html_city_storage' => $enclosure->block_city_storage($html['zone_items']),
            ]
        ]);
