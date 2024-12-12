<?php
/**
 * Methods relative to the configuration of the server where the game is hosted
 */
class Server {
    
    
    function get_config_file() {
        
        $file_contents = file_get_contents('../public/config.json');
        return json_decode($file_contents);
    }
}
