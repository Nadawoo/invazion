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
    
    return $_SERVER['SERVER_NAME'] === 'invaziongame.localhost' ? TRUE : FALSE;
}
