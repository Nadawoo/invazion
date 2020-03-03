<?php
/**
 * Classe générique pour générer la structure HTML d'une page du site.
 * Les éléments spécifiques au jeu ne figurent pas ici mais dans des classes dédiées.
 */
class HtmlPage
{
    
    /**
     * En-tête HTML des pages
     * 
     * @param  string $css_path Le chemin vers la feuille CSS voulue.
     * @return string
     */
    function page_header($css_path=NULL)
    {
        
        $css_link = ($css_path !== NULL) 
                    ? '<link rel="stylesheet" type="text/css" href="'.$css_path.'?v1.6">'
                    : '';
        
        return '<!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="resources/main.css?v2.9">
                <link rel="stylesheet" type="text/css" href="resources/layout.css?v1.5">
                <link rel="stylesheet" type="text/css" href="resources/map.css?v1.8">
                <link rel="stylesheet" type="text/css" href="resources/myzone.css?v1.1">
                <link rel="stylesheet" type="text/css" href="resources/city.css?v1.61">
                <link rel="stylesheet" type="text/css" href="resources/popup.css?v1.1">
                ' . $css_link . '
                <title>InvaZion, le jeu de zombies</title>
            </head>
            <body>
            
            <h1>InvaZion</h1>
            <div class="slogan">Le projet de Hordes-like collaboratif</div>
            <nav id="sitemenu">
                <a href="index">Jouer</a>&nbsp;<span class="circle">&cir;</span>
                <a href="https://invazion.nadazone.fr/discuss">Discuter</a>&nbsp;<span class="circle">&cir;</span>
                <a href="https://invazion.nadazone.fr/project">Le projet</a>&nbsp;<span class="circle">&cir;</span>
                <a href="https://invazion.nadazone.fr/customise-the-game">Créez votre version du jeu</a>&nbsp;<span class="circle">&cir;</span>
                <a href="https://invazion.nadazone.fr/apis-list">API</a><span class="circle">&cir;</span>
                <a href="http://invazion.wikidot.com" target="_blank">Wiki</a>
            </nav>';
    }
    
    
    /**
     * Pied HTML des pages
     * 
     * @return string
     */
    function page_footer()
    {
        
        return '    <script type="text/javascript" src="resources/scripts.js?v1.5"></script>
                </body>
            </html>';
    }
    
}
