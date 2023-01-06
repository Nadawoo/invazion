<?php
safely_require('/core/controller/official_server_root.php');

/**
 * Classe générique pour générer la structure HTML d'une page du site.
 * Les éléments spécifiques au jeu ne figurent pas ici mais dans des classes dédiées.
 */
class HtmlPage
{
    
    /**
     * Sets HTTP headers to secure the website
     */
    function http_headers()
    {
        // Disallows third-party sites to display the site in an <iframe>
        // Doc : https://infosec.mozilla.org/guidelines/web_security#x-frame-options
        header("Content-Security-Policy: frame-ancestors 'none'");
        header("X-Frame-Options: DENY");
    }
    
    
    /**
     * En-tête HTML des pages
     * 
     * @param  string $css_path Le chemin vers la feuille CSS voulue.
     * @return string
     */
    function page_header($css_path=NULL)
    {
        
        $this->http_headers();
        
        $css_link = ($css_path !== NULL) 
                    ? '<link rel="stylesheet" type="text/css" href="'.$css_path.'?v1.6">'
                    : '';
        
        $metas = [
            'canonical'     => "https://invaziongame.nadazone.fr",
            'page_title'    => "InvaZion, le jeu de zombies",
            'meta_title'    => "Jeu de survie collaboratif. Explorer, construire, résister",
            'description'   => "Jeu de survie collaboratif inspiré de Hordes.fr. "
                             . "Explorez le désert en équipe ou en solo, dénichez les précieuses ressources, "
                             . "construisez des défenses et survivez à l'attaque quotidienne !",
            'image'         => "https://invaziongame.nadazone.fr/resources/img/free/screenshots/map.png",
            'image_alt'     => "Preview of the game's map. A player has revealed some zones "
                             . "with different grounds (sand, woods, water...), the others are "
                             . "still in the obscurity.",
            'image_width'   => "426",
            'image_height'  => "345",
            ];
        
        return '<!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
                <meta name="title" content="'.$metas['meta_title'].'">
                <meta name="description" content="'.$metas['description'].'">
                <meta name="keywords" content="Jeu, Hordes, collaboration, équipe, construction, défenses, zombies">
                
                <meta property="og:url" content="'.$metas['canonical'].'">
                <meta property="og:type" content="website">
                <meta property="og:title" content="'.$metas['page_title'].'">
                <meta property="og:description" content="'.$metas['description'].'">
                <meta property="og:image" content="'.$metas['image'].'">
                <meta property="og:image:alt" content="'.$metas['image_alt'].'">
                <meta property="og:image:width" content="'.$metas['image_width'].'">
                <meta property="og:image:height" content="'.$metas['image_height'].'">
                
                <meta name="twitter:card" content="summary_large_image">
                <meta name="twitter:title" content="'.$metas['page_title'].'">
                <meta name="twitter:description" content="'.$metas['description'].'">
                <meta name="twitter:image:src" content="'.$metas['image'].'">
                <meta property="twitter:image:alt" content="'.$metas['image_alt'].'">
                
                <link rel="canonical" href="'.$metas['canonical'].'">
                <link rel="icon" type="image/png" href="resources/img/favicon.png">                
                <link rel="manifest" href="manifest.json" />
                <link rel="stylesheet" type="text/css" href="resources/css/sitelayout.css?v4.4">
                <link rel="stylesheet" type="text/css" href="resources/css/gamelayout.css?v8.6">
                <link rel="stylesheet" type="text/css" href="resources/css/map.css?v3.9">
                <link rel="stylesheet" type="text/css" href="resources/css/smartphone.css?v1.6">
                <link rel="stylesheet" type="text/css" href="resources/css/myzone.css?v1.4">
                <link rel="stylesheet" type="text/css" href="resources/css/city.css?v3.5">
                <link rel="stylesheet" type="text/css" href="resources/css/cityiso.css?v1.2">
                <link rel="stylesheet" type="text/css" href="resources/css/wall.css?v3.1">
                <link rel="stylesheet" type="text/css" href="resources/css/popup.css?v2.0">
                <link rel="stylesheet" type="text/css" href="resources/css/responsive.css?v2.4">
                ' . $css_link . '
                <title>'.$metas['page_title'].'</title>
            </head>
            
            <body>    
            <div id="body_bg">
                <div id="page_container">
                    <header>
                        <h1>InvaZion</h1>
                        <div id="slogan">Le projet de Hordes-like collaboratif</div>
                        <nav id="sitemenu">
                            <a href="index">Jouer</a>&nbsp;<span class="circle">&cir;</span>
                            <a href="'.official_server_root().'/discuss">Discuter</a>&nbsp;<span class="circle">&cir;</span>
                            <a href="'.official_server_root().'/project">Le projet</a>&nbsp;<span class="circle">&cir;</span>
                            <a href="'.official_server_root().'/customise-the-game">Créez votre version du jeu</a>&nbsp;<span class="circle">&cir;</span>
                            <a href="http://invazion.wikidot.com" target="_blank" rel="noopener">Wiki</a>
                            <br>
                            <a href="https://discord.gg/2GRPTyM" target="_blank" rel="noopener"
                                title="Pour parler du jeu, faire des propositions... Ou tout simplement discuter :)">
                                <img src="/resources/img/thirdparty/Discord-Logo-Black.png" alt="discord_logo" style="height:1.7em;margin-bottom:-0.6em;"> Discord
                            </a>
                            <a href="https://github.com/Nadawoo/invazion" target="_blank" rel="noopener"
                                title="Le code source du jeu est disponible sur Github">
                                <img src="/resources/img/thirdparty/GitHub-Mark-32px.png" alt="github_logo" style="height:1.4em;margin-bottom:-0.4em;"> Github
                            </a>
                            <a href="'.official_server_root().'/share" target="_blank" rel="noopener"
                                title="Partager et soutenir le projet InvaZion">
                                <img src="/resources/img/free/share.png" alt="share" style="height:1.4em;margin-bottom:-0.4em;"> Partager
                            </a>
                            <a href="'.official_server_root().'/apis-list" 
                                title="Modifiez toute l\'interface du jeu grâce aux API">
                                <img src="/resources/img/free/api.png" alt="api_logo" style="height:1.4em;margin-bottom:-0.4em;"> API
                            </a>
                        </nav>
                    </header>';
    }
    
    
    /**
     * Pied HTML des pages
     * 
     * @return string
     */
    function page_footer()
    {
        
        return '
                            <footer>
                                <p>Ce jeu est issu d\'<a href="https://invazion.nadazone.fr">Invazion</a>, 
                                créé par <strong>Nadawoo</strong> (développeur indépendant).<br> 
                                Invazion est inspiré du jeu <strong>Hordes</strong> développé 
                                par <a href="https://motion-twin.com" target="_blank" rel="noopener">Motion Twin</a>.<br>
                                Il n\'est pas affilié à Motion Twin.<br>
                                
                                </p>
                            </footer>
                        </div> <!-- End of #page_container -->
                    </div> <!-- End of #body_bg -->
                    
                    <script>
                    <!-- Registers the service workers to handle the website as a PWA -->
                    if (typeof navigator.serviceWorker !== "undefined") {
                        navigator.serviceWorker.register("/sw.js?v=1")
                    }
                    </script>

                    <script type="text/javascript" src="resources/js/ZombLib.js?v1.3"></script>
                    <script type="text/javascript" src="resources/js/wallTemplate.js?v4.4"></script>
                    <script type="text/javascript" src="resources/js/actionBlocks.func.js?v1.2"></script>
                    <script type="text/javascript" src="resources/js/cityEnclosure.func.js?v1.0"></script>
                    <script type="text/javascript" src="resources/js/discussions.func.js?v1.1"></script>
                    <script type="text/javascript" src="resources/js/events.func.js?v1.0"></script>
                    <script type="text/javascript" src="resources/js/misc.func.js?v9.3"></script>
                    <script type="text/javascript" src="resources/js/onPageLoad.js?v1.6"></script>
                    <script type="text/javascript" src="resources/js/events.js?v5.4"></script>
                </body>
            </html>';
    }
    
}
