<?php
require 'is_development_server.php';

/**
 * Returns the URL of the central server of InvaZion (which contains the APIs 
 * and the documentation).
 * 
 * @return string
 */
function official_server_root()
{
    
    if(is_development_server()) {
        
        // Only the main developer of InvaZion has access to this
        return 'http://invazion.localhost';
    }
    else {
        
        return 'https://invazion.nadazone.fr';
    }
}
