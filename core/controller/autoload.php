<?php
/**
 * Page à inclure dans toutes les pages du site
 * (contient les réglages de base)
 */


// Définit l'encodage en UTF8 pour PHP. Permet notamment d'afficher les accents 
// correctement dans les messages du forum (souci de PHP et pas de BDD puisqu'ils 
// sont enregistrés sans erreur dans la BDD)
ini_set('default_charset', 'utf-8');

// Définit explicitement le fuseau horaire pour qu'il ne pas varie pas selon le serveur
date_default_timezone_set('Europe/Paris');

// Met les dates en français (par exemple avec strftime())
// en leur appliquant le fuseau défini par date_default_timezone_set()
setlocale(LC_TIME, '');


// Autoloader (to use a class without explicitly including the file)
// @para sting $class The name of the class. Must be th same name as the file containing the class.
spl_autoload_register(function ($class) {
    require dirname(__DIR__).'/view/'.$class.'.php';
});


/**
 * Utiliser cette fonction à la place des "require/include" de PHP :
 * - Règle le problème de la portée des variables dans les inclusions
 * (nativement, une variable créée dans un script inclus est utilisable dans 
 * le script appelant. Ici, elle sera enfermée dans la fonction.)
 * - Permet d'utiliser des chemins absolus (/blabla) quelle que soit la racine
 * sur le seveur de dev (évite notamment des erreurs une fois le site en ligne)

 */
function safely_require($filepath)
{
    
    $root = '';
    
    // Si on demande un chemin absolu, on part de la racine du site
    if (substr($filepath, 0, 1) === '/') {
        
        // NB : filter_input() serait plus adapté que filter_var()
        // mais il ne fonctionne pas avec $_SERVER chez OVH à ce jour
        $public_root = filter_var($_SERVER['DOCUMENT_ROOT'], FILTER_SANITIZE_STRING);
        // Remove the eventual final "/public" at the end of the path, 
        // as the domain name points to the subfolder /public and not to 
        // the real root folder
        $root = preg_replace('!/public$!', '', $public_root);
    }
    
    require_once $root . $filepath;
}
