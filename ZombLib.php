<?php
/**
 * Librairie d'appel à l'API d'InvaZion
 * pour récupérer ou/et écrire des données sur le serveur
 * http://invazion.nadazone.fr
 * 
 * Version 3.10
 */
class ZombLib
{
    
    
    private $_item_id = NULL;
    
    
    public function __construct($api_url='')
    {
        
        $default_url = 'http://invazion.nadazone.fr/api';
        
        // Si besoin, vous pouvez changer l'URL par défaut de l'API
        // en précisant la nouvelle URL lorsque vous appelez à la classe :
        // $api = new ZombLib('http://newapisite.com/api')
        $this->url = trim($api_url) !== '' ? $api_url : $default_url;
    }
    
    
    public function set_item_id($item_id)
    {
        
        if($item_id !== NULL and !is_int($item_id)){
            
            throw new Exception('$item_id must be an integer, '.gettype($item_id).' given');
        }
        
        $this->_item_id = $item_id;
    }
    
    
    /**
     * 
     * Connecte le joueur à son compte
     * 
     * @param string $email     L'email associé au compte   
     * @param string $password  Le mot de passe
     * 
     * @return array|bool   Le résultat retourné par l'API de connexion,
     *                      ou FALSE si la connexion a échoué
     */
    public function connect_user($email, $password)
    {
        
        // L'email est encodé en base64 pour pouvoir transiter dans l'URL
        $email64 = base64_encode($email);
        $pass64  = base64_encode($password);
        
        // On envoie les données de connexion à l'API
        $json = $this->get_api_output($this->url.'/user?action=connect&email64='.$email64.'&pass64='.$pass64);
        $api_results = $this->json_to_array($json);
        
        // Si la connexion a réussi
        if ($api_results['metas']['error_code'] === 'success') {
            
            // On stocke le jeton d'identification dans un cookie
            setcookie('token', $api_results['datas']['token']);
            // On crée aussi la variable, sinon le cookie n'existerait pas 
            // tant que le script en cours ne s'est pas terminé
            $_COOKIE['token'] = $api_results['datas']['token'];
        }
        
        return $api_results;
    }
    
    
    /**
     * Déconnecte le joueur de son compte
     * 
     * @return array    Le résultat renvoyé par l'API
     */
    public function disconnect_user()
    {
        
        // Récupère le jeton d'identification du joueur
        $token = $this->get_token();
        // Déconnecte le joueur sur le serveur du jeu
        $json = $this->get_api_output($this->url.'/user?action=disconnect&token='.$token);
        // Détruit le cookie d'identification dans le navigateur du joueur
        setcookie('token', NULL);
        // On détruit aussi la variable, sinon le cookie survivrait jusqu'à
        // la fin de l'exécution du script
        unset($_COOKIE['token']);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Renvoie le jeton d'identification stocké dans un cookie chez l'utilisateur 
     * déjà connecté.
     * Passez ce jeton en paramètre dans vos URL d'appel à l'API pour authentifier le joueur.
     * 
     * @return string|bool  Renvoie le jeton stocké dans le cookie, ou FALSE 
     *                      si ce cookie n'existe pas (utilisateur non connecté)
     */
    public function get_token()
    {
        
        if (isset($_COOKIE['token'])) {
            
            return filter_var($_COOKIE['token'], FILTER_SANITIZE_STRING);
        }
        else {
            
            return FALSE;
        }
    }
    
    
    /**
     * Retourne les données contenues dans le jeton, sous forme d'un array.
     * Ne pas confondre avec la méthode get_token(), qui retourne le jeton brut
     * (Ex : "eyJleHAiOplblH19.N2EDE5MDUyZDFjA1ZjIyZWQzZTF")
     * Une clé peut être précisée en paramètre pour retourner seulement un élément
     * précis :
     *      > get_token_data('user_id')
     * 
     * @param  string $data_key     Facultatif : le nom d'un élément précis du jeton à retourner 
     *                              (ex : 'citizen_id')
     * 
     * @return array|string|bool    Renvoie un array contenant toutes les données du jeton,
     *                              ou la valeur de l'élément précis qui a été demandé,
     *                              ou NULL si une erreur s'est produite (jeton invalide...)
     */
    public function get_token_data($data_key='')
    {
        
        $result = NULL;
        $token = $this->get_token();
 
        // Tente de mettre les données du jeton dans un array
        $json = json_decode(base64_decode(explode('.', $token)[0]), TRUE);
        
        if ($json !== NULL) {
            
            // Si on n'a pas demandé une donnée précise du jeton (ex: l'id du joueur),
            // on retourne toutes les données sous forme d'un array
            if ($data_key === '') {
                
                $result = $json;
            }
            // Si on a demandé une donnée précise (ex : get_token_data('citizen_id')),
            // on ne retourne que celle-ci
            elseif (array_key_exists($data_key, $json['data'])) {
                
                $result = $json['data'][$data_key];
            }
        }
        
        return $result;
    }
    
    
    /**
     * Indique si l'utilisateur est déjà connecté ou non
     * (en analysant le cookie contenant le jeton d'identification).
     * NB : cette méthode ne vérifie pas si les données du jeton ont été trafiquées 
     * (contrôle de la clé). Ce contrôle sera réalisé par le serveur central d'InvaZion.
     * 
     * @return boolean  TRUE si l'utilisateur est connecté, sinon FALSE.
     */
    public function user_seems_connected()
    {
        
        $datas  = $this->get_token_data();
        
        // Vérifie que le jeton n'a pas expiré
        if (is_array($datas) and (int)$datas['exp'] > time()) {
            
            return TRUE;
        }
        else {
            
            return FALSE;
        }
    }
    
    
    /**
     * Crée un citoyen sur la carte pour un utilisateur déjà connecté
     * 
     * @param  string $pseudo   Le nom du citoyen à créer
     * @return array  Le résultat retourné par l'API
     */
    public function create_citizen($pseudo)
    {
        
        // Le pseudo est encodé en base64 pour pouvoir transiter intact dans l'URL
        $pseudo64 = base64_encode($pseudo);
        
        $json   = $this->get_api_output($this->url.'/user?action=create_citizen&token='.$this->get_token().'&pseudo64='.$pseudo64);
        $result = $this->json_to_array($json);
        
        if ($result['metas']['error_code'] === 'success') {
            
            // On met à jour le jeton dans le cookie
            setcookie('token', $result['datas']['new_token']);
            // On modifie aussi la variable, sinon le cookie n'existerait pas 
            // tant que le script en cours ne s'est pas terminé
            $_COOKIE['token'] = $result['datas']['new_token'];
        }
        
        return $result;
    }
    
    
    /**
     * Ajouter un élément sur la carte (zombies, crypte...)
     * 
     * @param  string $stuff      Le nom de l'élément à ajouter ("zombies", "vault"...)
     * @param  string $conditions (Facultatif) Si vaut 'noconditions', les conditions 
     *                  normalement requises pour exécuter l'action seront ignorées
     *                  (Ex : pas besoin d'être dans une crypte pour repeupler la carte de zombies)
     *                  Si le paramètre n'est pas précisé ou contient toute autre valeur, 
     *                  les conditions normales seront appliquées.
     * @return array  Le résultat retourné par l'API
     */
    public function add_stuff_on_map($stuff, $conditions='')
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=add&stuff='.$stuff.'&conditions='.$conditions.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère toutes les données statiques du jeu : caractéristiques des chantiers,
     * des objets, des spécialités des citoyens...
     * 
     * @return array
     */
    public function get_config()
    {
        
        $json = $this->get_api_output($this->url.'/configs?action=get');
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère la liste des citoyens
     * 
     * @param  int $map_id   L'identifiant (numéro) de la carte
     * @return array Le résultat retourné par l'API
     */
    public function get_citizens($map_id)
    {
        
        $json = $this->get_api_output($this->url.'/citizens?action=get&map_id='.$map_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère les caractéristiques complètes (puits, chantiers...) 
     * de toutes les villes d'une carte.
     * 
     * NB 1 : N'utilisez pas cette méthode pour construire la carte. Utilisez
     *        la méthode get_map(), elle contient déjà l'emplacement des villes.
     *        N'utilisez get_cites() que pour récupérer *l'intérieur* d'une ville
     *        (le puits, les chantiers...)
     * 
     * NB 2 : Si vous n'avez besoin que d'une ville en particulier, 
     *        utilisez la méthode get_city().
     * 
     * @param  int $map_id  L'id de la carte dont vous voulez récupérer les villes
     * @return array Le résultat retourné par l'API
     */
    public function get_cities($map_id)
    {
        
        $json = $this->get_api_output($this->url.'/cities?action=get&map_id='.$map_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère les caractéristiques (puits, chantiers...) d'une ville en particulier.
     * Si vous voulez récupérer toutes les villes, utilisez plutôt get_cities().
     * 
     * @param  int $city_id  L'id de la ville dont vous voulez récupérer les villes
     * @return array Le résultat retourné par l'API
     */
    public function get_city($city_id)
    {
        
        $json = $this->get_api_output($this->url.'/cities?action=get&city_id='.$city_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère les objets sur la carte, zone par zone
     * 
     * @param  int $map_id   L'identifiant (numéro) de la carte
     * @return array Le résultat retourné par l'API
     */
    public function get_map($map_id)
    {
        
        $json = $this->get_api_output($this->url.'/maps?action=get&map_id='.$map_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupère les données du joueur connecté (à partir de son jeton d'identification)
     * 
     * @return array
     */
    public function get_me()
    {
        
        $json = $this->get_api_output($this->url.'/me?action=get&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Choix du métier du citoyen
     * 
     * @param string $job   L'alias du métier (digger/explorer/builder...)
     * @return array
     */
    public function specialize($job)
    {
        
        $json = $this->get_api_output($this->url.'/me?action=specialize&type='.$job.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Agresser un autre citoyen
     * 
     * @param string $target_id L'id du citoyen à agresser
     * @return array
     */
    public function attack_citizen($target_id)
    {
        
        $json = $this->get_api_output($this->url.'/me?action=attack&target_id='.$target_id.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Soigner un autre citoyen
     * 
     * @param string $target_id L'id du citoyen à soigner
     * @return array
     */
    public function heal_citizen($target_id)
    {
        
        $json = $this->get_api_output($this->url.'/me?action=heal&target_id='.$target_id.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Déplacer un citoyen sur la carte
     * 
     * @param string $direction  La direction dans laquelle le déplacer, parmi une
     *                           de ces 4 valeurs : north, south, east, west.
     * @return array Le résultat retourné par l'API
     */
    public function move($direction)
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=move&token='.$this->get_token().'&to='.$direction);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Fouiller une zone de l'outre monde
     * 
     * @return array Le résultat retourné par l'API
     */
    public function dig()
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=dig&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Déposer un objet par terre.
     * L'id de l'objet concerné est défini par la méthode set_item_id()
     * 
     * @return array Le résultat retourné par l'API
     */
    public function drop()
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=drop&token='.$this->get_token().'&item_id='.$this->_item_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Ramasser un objet au sol et le mettre dans son sac.
     * L'id de l'objet concerné est défini par la méthode set_item_id()
     * 
     * @return array Le résultat retourné par l'API
     */
    public function pickup()
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=pickup&token='.$this->get_token().'&item_id='.$this->_item_id);
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Attaquer un zombie à mains nues
     * 
     * @param string $action Si vaut 'fight' (par défaut) = attaquer un zombie
     *                       Si vaut 'bigfight' = attaquer des zombies en masse
     * @return array Le résultat retourné par l'API
     */
    public function fight($action='fight')
    {
        
        $json = $this->get_api_output($this->url.'/zone?action='.$action.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Fonder une ville dans la zone
     * 
     * @param  int    $city_size   Le nombre de citoyens autorisés dans la ville
     * @return array  Le résultat retourné par l'API
     */
    public function build_city($city_size)
    {
        
        $json = $this->get_api_output($this->url.'/city?action=build&city_size='.$city_size.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Attaquer une ville de la zone où se trouve le citoyen
     * 
     * @return array  Le résultat retourné par l'API
     */
    public function attack_city()
    {
        
        $json = $this->get_api_output($this->url.'/city?action=attack&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Entrer ou sortir de la ville
     * 
     * @return array  Le résultat retourné par l'API
     */
    public function go_inout_city()
    {
        
        $json = $this->get_api_output($this->url.'/city?action=go_inout&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Ouvrir la porte de la ville
     * 
     * @return array  Le résultat retourné par l'API
     */
    public function open_city_door()
    {
        
        $json = $this->get_api_output($this->url.'/city?action=open_door&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Fermer la porte de la ville
     * 
     * @return array  Le résultat retourné par l'API
     */
    public function close_city_door()
    {
        
        $json = $this->get_api_output($this->url.'/city?action=close_door&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Fabriquer un objet à l'atelier.
     * L'id de l'objet à fabriqué est défini par la méthode set_item_id()
     * 
     * @return array  Le résultat retourné par l'API
     */
    public function craft_item()
    {
        
        $json = $this->get_api_output($this->url.'/buildings?action=build&item_id='.$this->_item_id.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Investir des points d'action dans un chantier
     * 
     * @param  int $construction_id L'ID du chantier à construire
     * @return array  Le résultat retourné par l'API
     */
    public function construct($construction_id)
    {
        
        $json = $this->get_api_output($this->url.'/buildings?action=build&construction_id='.$construction_id.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Dévoiler des zones aléatoires sur la carte
     * 
     * @param  string $stuff Code précisant le type de révélation
     *                       Ex : "random7" pour dévoiler 7 zones aléatoires.
     *                       Consultez la documentation de l'API sur le site
     *                       pour avoir la liste complète et actualisée.
     * @return array Le résultat retourné par l'API
     */
    public function reveal_zones($stuff)
    {
        
        $json = $this->get_api_output($this->url.'/zone?action=reveal&stuff='.$stuff.'&token='.$this->get_token());
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Récupérer la liste des sujets de l'espace de discussion
     * 
     * @return array Le résultat retourné par l'API
     */
    public function get_discuss_threads()
    {
        
        $json = $this->get_api_output($this->url.'/discuss/threads?action=get');
        
        return $this->json_to_array($json);
    }
    
    
    /**
     * Retourne l'adresse IP du visteur (pour la connexion au citoyen sans compte)
     * 
     * @return string|bool  L'adresse IP, ou FALSE si l'IP est invalide.
     */
    public function get_user_ip()
    {
        
        // Important : on utilise filter_var() au lieu de filter_input()
        // car chez certains hébergeurs (notamment OVH), cette fonction 
        // ne fonctionne pas avec la variable $_SERVER
        return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }
    
    
    /**
     * Sends the text to convert and the defined options to the online API, 
     * then gets the JSON result returned by the server
     * 
     * @param  string $api_file_url L'url de l'API à appeler.
     * @return string               Le JSON retourné par le serveur
     */
    private function get_api_output($api_file_url)
    {
        
        $stream = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
                            // VERY IMPORTANT : sets a user agent. If no user agent
                            // is sent, the server of InvaZion will return a "403/forbidden" error
                            // so you wouldn't be able to access the API.
                            ."User-Agent: Mozilla/4.0 (compatible; MSIE 6.0)\r\n",
                ]
            ];
        
        // Reads the result returned by the API
        return file_get_contents( $api_file_url, FALSE, stream_context_create($stream) );
    }
    
    
    /**
     * Convertit le JSON texte brut en array.
     * 
     * @param  string $json Le flux JSON sous forme de texte brut
     * @return array        Le flux JSON sous forme d'array exploitable
     */
    private function json_to_array($json)
    {
        
        // Convertit le JSON texte en array.
        // NB : le paramètre TRUE précise qu'on veut un array et non pas un objet
        $json_array = json_decode($json, TRUE);
        
        // Si jamais le serveur retourne un JSON invalide (notamment au cas où
        // il y aurait un plantage site principal), la librairie retourne un JSON
        // minimaliste par défaut.
        // Sa structure et les noms des clés sont les mêmes que ceux des JSON 
        // normalement retournés par le serveur.
        if ($json_array === NULL) {
            
            $json_array = [
                'metas' => [
                    'api_version'   => 'rescue_zomblib_api',
                    'error_code'    => 'zomblib_invalid_json',
                    'error_message' => 'Erreur serveur : le site d\'InvaZion a retourné un flux JSON invalide, '
                                     . 'signalez-le à l\'administrateur du jeu.<br>'
                                     . 'Le serveur a retourné le message suivant : '. $json,
                    'error_class'   => 'critical'
                    ]
                ];
        }
        
        return $json_array;
    }
}
