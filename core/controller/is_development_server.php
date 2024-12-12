<?php
/**
 * Détermine si on est sur le site en ligne ou sur un serveur local
 * (serveur de développement)
 * 
 * @return bool     TRUE si on est sur le serveur de développement
 *                  FALSE dans les autres cas
 */
function is_development_server()
{
    
    $server = new Server();
    $configs = $server->get_config_file();
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
