<?php
/**
 * Methods relative to the configuration of the server where the game is hosted
 */
class Server {
    
    
    /**
     * Reads the file containing the parameters for the dev/prod servers
     * @return Object The parameters for the dev/prod servers
     */
    function get_config_file() {
        
        $file_contents = file_get_contents('../public/config.json');
        return json_decode($file_contents);
    }
    
    
    /**
     * Returns the URL of the central server of Azimutant (which contains the APIs 
     * and the documentation).
     * 
     * @return string
     */
    function official_server_root()
    {
        
        $configs = $this->get_config_file();

        return ($this->is_development_server()) ? $configs->dev->api_server_root : $configs->prod->api_server_root;
    }


    /**
     * Determines if the game runs on the production server or the local dev server.
     * 
     * @return bool True if we are on the development server
     *              False if we are on the production server
     */
    function is_development_server()
    {
        
        $configs = $this->get_config_file();
        $server_name = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_SPECIAL_CHARS);

        if($server_name === parse_url($configs->dev->gui_server_root, PHP_URL_HOST)) {
            return true;
        } elseif($server_name === parse_url($configs->prod->gui_server_root, PHP_URL_HOST)) {
            return false;
        } else {
            throw new Exception('[Azimutant] Environment for the current server "'.$server_name.'" '
                              . 'is not defined in /configs.json');
        }
    }   
}
