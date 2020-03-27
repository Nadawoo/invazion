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
                <link rel="stylesheet" type="text/css" href="resources/css/sitelayout.css?v3.0">
                <link rel="stylesheet" type="text/css" href="resources/css/gamelayout.css?v3.2">
                <link rel="stylesheet" type="text/css" href="resources/css/map.css?v1.8">
                <link rel="stylesheet" type="text/css" href="resources/css/smartphone.css?v1.1">
                <link rel="stylesheet" type="text/css" href="resources/css/myzone.css?v1.2">
                <link rel="stylesheet" type="text/css" href="resources/css/city.css?v1.61">
                <link rel="stylesheet" type="text/css" href="resources/css/popup.css?v1.1">
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
                <br>
                <br>
                <a href="https://discord.gg/2GRPTyM" target="_blank" title="Pour parler du jeu, faire des propositions... Ou tout simplement discuter :)">
                    <img src="/resources/img/thirdparty/Discord-Logo-Black.png" alt="discord_logo" style="height:2em;margin-bottom:-0.6em;"> Discord
                </a>
                <a href="https://github.com/Nadawoo/invazion" target="_blank" title="Le code source du jeu est disponible sur Github">
                    <img src="/resources/img/thirdparty/GitHub-Mark-32px.png" alt="github_logo" style="height:1.7em;margin-bottom:-0.4em;"> Github
                </a>
            </nav>';
    }
    
    
    /**
     * Pied HTML des pages
     * 
     * @return string
     */
    function page_footer()
    {
        
        return '    <script type="text/javascript" src="resources/scripts.js?v1.6"></script>
                </body>
            </html>';
    }
    
}
