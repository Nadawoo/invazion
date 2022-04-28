<?php
/*
 * Generates the HTML for the action block "Humans" next to the map
 */

require_once '../../core/controller/autoload.php';
safely_require('/core/controller/SortGameData.php');
safely_require('/core/controller/official_server_root.php');
safely_require('/core/ZombLib.php');

header('content-type:application/json');

// You must give all these parameters in the url when you call the generator
$map_id     = filter_input(INPUT_GET, 'map_id',     FILTER_VALIDATE_INT);
$coord_x    = filter_input(INPUT_GET, 'coord_x',    FILTER_VALIDATE_INT);
$coord_y    = filter_input(INPUT_GET, 'coord_y',    FILTER_VALIDATE_INT);
$citizen_id = filter_input(INPUT_GET, 'citizen_id', FILTER_VALIDATE_INT);


$api    = new ZombLib(official_server_root().'/api');
$layout = new HtmlLayout();
$sort   = new SortGameData();
$coord  = $coord_x.'_'.$coord_y;

$citizens  = $api->call_api('citizens', 'get', ['map_id'=>$map_id,
                                                'zones' =>$coord
                                                ])['datas'];

$zone_fellows = $sort->sort_citizens_by_coord($citizens)[$coord];
$html_fellows = $layout->block_zone_fellows($zone_fellows, $citizen_id);

echo json_encode(['datas' => $html_fellows]);
