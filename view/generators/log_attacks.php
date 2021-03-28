<?php
/**
 * Generates the HTML for the log of zombies attacks
 * Useful for loading it with asynchronous javacript.
 */

require_once '../../controller/autoload.php';
safely_require('/controller/official_server_root.php');
safely_require('/ZombLib.php');

header('content-type:application/json');

$action     = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
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
        $title   = $htmlLogAttacks->attack_door_open($attack_data)['title'];
        $message = $htmlLogAttacks->attack_door_open($attack_data)['message'];
    }
    // If all the zombies have been repelled
    elseif($zombies_overflow <= 0) {        
        $title   = $htmlLogAttacks->attack_repulsed($attack_data)['title'];
        $message = $htmlLogAttacks->attack_repulsed($attack_data)['message'];
    }
    // If zombies entered in the city
    else {        
        $title   = $htmlLogAttacks->attack_not_repulsed($attack_data)['title'];
        $message = $htmlLogAttacks->attack_not_repulsed($attack_data)['message'];
    }
    
    // Add the citizens dead from other causes than zombies (infection...)
    $message .= $htmlLogAttacks->other_deaths(); 
    
    // For the javascript treatment, the HTML elements are splitted
    $html_attacks[$key] = [ 'datetime_utc'  => $attack_data['datetime_utc'],
                            'title'         => $title,
                            'message'       => $message,
                            ];
}


echo json_encode($html_attacks);
