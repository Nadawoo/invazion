<?php
safely_require('/core/model/Server.php');

/**
 * Classe générique pour générer la structure HTML d'une page du site.
 * Les éléments spécifiques au jeu ne figurent pas ici mais dans des classes dédiées.
 */
class HtmlPage
{
    
    // Increment those variables when you modify the CSS or JS files. This ensures
    // that the users' browsers reload the up-to-date files, instead of using 
    // the obsolete ones stored in their cache.
    private $css_js_version = 56.2;
    
    /**
     * Sets HTTP headers to secure the website
     */
    function http_headers()
    {
        // Disallows third-party sites to display the site in an <iframe>
        // Doc : https://infosec.mozilla.org/guidelines/web_security#x-frame-options
        header("Content-Security-Policy: frame-ancestors 'none'; object-src 'none';");
        header("X-Frame-Options: DENY");
    }
    
    
    /**
     * En-tête HTML des pages
     * 
     * @param int $user_id
     * @param int $citizen_id
     * @param string $citizen_pseudo
     * @param  string $css_path Le chemin vers la feuille CSS voulue.
     * @return string
     */
    function page_header($user_id=null, $citizen_id=null, $citizen_pseudo=null, $css_path=NULL)
    {
        
        $this->http_headers();
        
        $css_link = ($css_path !== NULL) 
                    ? '<link rel="stylesheet" type="text/css" href="'.$css_path.'?v'.$this->css_js_version.'">'
                    : '';
        
        $metas = [
            'site_name'     => "Azimutant",
            'canonical'     => "https://invaziongame.nadazone.fr",
            'page_title'    => "Azimutant, le jeu de zombies",
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
                
                <meta property="og:site_name" content="'.$metas['site_name'].'">
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
                
                <link rel="stylesheet" type="text/css" href="resources/css/lib/googlefonts.css" media="screen,projection">
                <link rel="stylesheet" type="text/css" href="resources/css/lib/materialize.min.css" media="screen,projection">
                <link rel="stylesheet" type="text/css" href="resources/css/lib/animate.min.css" media="screen,projection">
                <link rel="stylesheet" type="text/css" href="resources/css/sitelayout.css?v'.$this->css_js_version.'" rel="preload" as="style">
                <link rel="stylesheet" type="text/css" href="resources/css/gamelayout.css?v'.$this->css_js_version.'" rel="preload" as="style">
                <link rel="stylesheet" type="text/css" href="resources/css/map.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/paths.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/smartphone.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/city.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/cityiso.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/wall.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/popup.css?v'.$this->css_js_version.'">
                <link rel="stylesheet" type="text/css" href="resources/css/responsive.css?v'.$this->css_js_version.'">
                ' . $css_link . '
                <title>'.$metas['page_title'].'</title>
            </head>
            
            <body>    
            <div id="body_bg">
                <div id="page_container">
                    <header data-section="header">
                        <a href="#" data-target="slide-out" class="menu_button sidenav-trigger">
                            <i class="material-icons">menu</i>
                        </a>
                        <a href="#poppresentation" class="help_button">
                            <i class="material-icons">help</i>
                        </a>
                        <h1><a href="/">Azimutant</a></h1>
                        <div id="slogan" data-translate="slogan">Gérez l\'apocalypse.</div>
                        '.$this->site_menu($user_id, $citizen_id, $citizen_pseudo).'
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
                                <p>Ce jeu est issu d\'<a href="https://invazion.nadazone.fr">Azimutant</a>, 
                                créé par <strong>Nadawoo</strong> (développeur indépendant).<br> 
                                Azimutant est inspiré du jeu <strong>Hordes</strong> développé 
                                par <a href="https://motion-twin.com" target="_blank" rel="noopener">Motion Twin</a>.<br>
                                Il n\'est pas affilié à Motion Twin.<br>
                                
                                </p>
                            </footer>
                        </div> <!-- End of #page_container -->
                    </div> <!-- End of #body_bg -->
                    
                    <div id="scripts">
                        <script>
                        <!-- Registers the service workers to handle the website as a PWA -->
                        if (typeof navigator.serviceWorker !== "undefined") {
                            navigator.serviceWorker.register("/sw.js?v=1")
                        }
                        </script>

                        <script type="text/javascript" src="resources/js/lib/materialize.min.js"></script>
                        <script type="text/javascript" src="resources/js/lib/scrollbooster.min.js"></script>
                        <script type="text/javascript" src="resources/js/ZombLib.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/Tasks.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/Translator.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/BuildingPopup.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/CitiesConnections.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/strings.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/cookies.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/mapInit.func.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/mapUse.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/paths.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/tooltips.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/wallTemplate.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/actionBlocks.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/cityEnclosure.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/cityEnclosureInit.func.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/discussions.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/smartphone.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/eventsMain.func.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/eventsActions.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/users.func.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/miscInit.func.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/misc.func.js?v'.$this->css_js_version.'" async></script>
                        <script type="text/javascript" src="resources/js/onPageLoad.js?v'.$this->css_js_version.'"></script>
                        <script type="text/javascript" src="resources/js/events.js?v'.$this->css_js_version.'"></script>
                    </div>
                </body>
            </html>';
    }
    
    
    /**
     * Main menu of the game
     * 
     * @param int $user_id
     * @param int $citizen_id
     * @param string $citizen_pseudo
     * @return string HTML
     */
    function site_menu($user_id, $citizen_id, $citizen_pseudo) {
        
        $server = new Server();
        $official_server_root = $server->official_server_root();
        
        $connection_buttons = ($citizen_id === null)
                    ? '<a href="register" class="#0d47a1 blue darken-4 white-text"> M\'inscrire </a> · <a href="connect#connectionForm" class="#0d47a1 blue darken-4 white-text"> Me connecter </a>'
                    : '<br>';
        
        $user_name = ($citizen_id === null)
                    ? '[Non connecté]'
                    : '<span title="Vous êtes connecté au compte #'.$user_id."\nVous contrôlez actuellement le citoyen #".$citizen_id.' « '.$citizen_pseudo.' »">Compte #'.$user_id.'<br>Citoyen #'.$citizen_id.'</span>';
                    
        return '
            <ul id="slide-out" class="sidenav">
            
                <li><div class="user-view">
                    <div class="background">
                        <img src="resources/img/motiontwin/mapBg.jpg" alt="Fond">
                    </div>
                    <a href="connect#connectionForm" class="user blue darken-4">
                        <img class="circle" src="resources/img/icons8/profile-96.png" alt="Utilisateur">
                        '.$user_name.'
                    </a>
                    '.$connection_buttons.'
                </div></li>'
                
                .$this->site_menu_item('Jouer', 'index#Outside', 'gamepad').'
                <li><a href="https://discord.gg/2GRPTyM" target="_blank" rel="noopener"
                    title="Pour parler du jeu, faire des propositions... Ou tout simplement discuter :)">
                    <i><img src="/resources/img/thirdparty/Discord-Logo-Black.png" alt="discord_logo"
                        height="32" width="32" style="margin-left:-0.2em;margin-bottom:-0.8em;"></i>Discord
                </a></li>'
                .$this->site_menu_item('Wiki', 'http://invazion.wikidot.com', 'edit')
                .$this->site_menu_item('Partager', $official_server_root.'/share', 'share',
                        'Partager et soutenir le projet Azimutant')
                
//                $this->site_menu_item('Forum', $official_server_root.'/discuss', 'forum')
                
                .$this->site_menu_subheader('Développer')
                .$this->site_menu_item('Le projet', $official_server_root.'/project', 'help')
                .$this->site_menu_item('Créez votre version du jeu', $official_server_root.'/customise-the-game', 'build').'
                <li><a href="https://github.com/Nadawoo/invazion" target="_blank" rel="noopener"
                    title="Le code source du jeu est disponible sur Github">
                    <i><img src="/resources/img/thirdparty/GitHub-Mark-32px.png" alt="github_logo"
                    height="24" width="24" style="margin-bottom:-0.4em;"></i>Github
                </a></li>
                <li><a href="'.$official_server_root.'/apis-list" 
                    title="Modifiez toute l\'interface du jeu grâce aux API">
                    <i><img src="/resources/img/free/api.png" alt="api_logo"
                    height="24" width="24" style="margin-bottom:-0.4em;"></i>API
                </a></li>
            </ul>';
    }
    
    
    private function site_menu_subheader($name, $no_divider=null) {
        
        $divider = ($no_divider === null) ? '<li><div class="divider"></div></li>' : '';
        
        return $divider.'<li><a class="subheader">'.$name.'</a></li>';
    }
    
    
    private function site_menu_item($name, $href, $googlefont_icon, $title="") {
        
        return '<li>
            <a href="'.$href.'" title="'.$title.'"><i class="material-icons black-text">'.$googlefont_icon.'</i>'.$name.'</a>
            </li>';
    }
}
