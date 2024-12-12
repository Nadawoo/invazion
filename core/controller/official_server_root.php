<?php
safely_require('/core/model/Server.php');
require_once 'is_development_server.php';

/**
 * Returns the URL of the central server of Azimutant (which contains the APIs 
 * and the documentation).
 * 
 * @return string
 */
function official_server_root()
{
    
    $server = new Server();
    $configs = $server->get_config_file();
    
    return (is_development_server()) ? $configs->dev->api_server_root : $configs->prod->api_server_root;
}
