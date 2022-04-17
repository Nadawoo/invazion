<?php
/**
 * Generates the HTML for the log of zombies attacks
 * Useful for loading it with asynchronous javacript.
 */

require_once '../../../core/controller/autoload.php';
safely_require('../../../core/controller/official_server_root.php');
safely_require('../../../core/ZombLib.php');

header('content-type:application/json');

$type       = filter_input(INPUT_GET, 'type',   FILTER_SANITIZE_STRING);
$sort       = filter_input(INPUT_GET, 'sort',   FILTER_SANITIZE_STRING);

$api            = new ZombLib(official_server_root().'/api');
$htmlLogAttacks = new HtmlLogAttacks();
$html_attacks   = [];


// Call the server API to get the raw data of the attacks log
$json_api = $api->call_api('events', 'get', ['type'=>$type, 'sort'=>$sort]);


// Build the HTML of the log
foreach($json_api['datas'] as $key=>$attack_data) {
    
    $zombies_overflow = $attack_data['zombies']-$attack_data['defenses'];
    
    // If the city door was open during the attack
    if($attack_data['is_door_closed'] === 0) {        
        $html_attacks[$key] = $htmlLogAttacks->get_log_entry('attack_door_open', $attack_data);
    }
    // If all the zombies have been repelled
    elseif($zombies_overflow <= 0) {        
        $html_attacks[$key] = $htmlLogAttacks->get_log_entry('attack_repulsed', $attack_data);
    }
    // If zombies entered in the city
    else {        
        $html_attacks[$key] = $htmlLogAttacks->get_log_entry('attack_not_repulsed', $attack_data);
    }
}


echo json_encode($html_attacks);
