<?php
safely_require('/core/controller/array_key_first.php');

/**
 * The tasks (goals) asked to the player to guide him in the game
 * Ex: "Gather 10 wood planks"
 *     "Discover 3 buildings in the desert"
 *     "Survive 20 days" 
 */
class Tasks {
    
    function __construct() {
        
        $this->tasks = $this->set_tasks();
    }
    
    
    /**
     * Get the characteristics of the tasks unlocked among a guven set of tasks.
     * 
     * @param array|int $tasks_ids The IDs of the tasks set in the set_tasks() method.
     *                             Can be an array of IDs.
     *                             Can be an int if only one entrey is needed.
     *                             Can be null if you need all the tasks.
     * @param int $last_unlocked_task_id The ID of the last tasks accomplished
     *                  by the player. This ID must exist among the keys of 
     *                  the tasks defined in the get_tasks() method. 
     * @return array
     */
    public function get_unlocked_tasks($tasks_ids, $last_unlocked_task_id) {
        
        $result = false;
        // Get only the set of tasks asked by the user
        $tasks_scope = $this->get_tasks($tasks_ids);
        
        if($last_unlocked_task_id === null) {
            // If the player has completed no task for now, return the first one
            $next_task_id = $tasks_ids[array_key_first($tasks_ids)];
        }
        elseif($tasks_scope[$last_unlocked_task_id]['next_task_id'] !== null) {
            // If the player has fulfilled one task or more (but not the last one),
            // show the next task to him.
            $next_task_id = $tasks_scope[$last_unlocked_task_id]['next_task_id'];
        }
        elseif($tasks_scope[$last_unlocked_task_id]['next_task_id'] === null) {
            // If the player has fulfilled the last task in the chain of tasks,
            // no more task to show.
            return [];
        }
        
        if(isset($tasks_scope[$next_task_id])) {
            $result[$next_task_id] = $tasks_scope[$next_task_id];
        }
        
        return $result;
    }
    
    
    /**
     * Get the characteristics of one or several tasks (their icons, texts...)
     * 
     * @param array|int $tasks_ids The IDs of the tasks set in the set_tasks() method.
     *                             Can be an array of IDs.
     *                             Can be an int if only one entrey is needed.
     *                             Can be null if you need all the tasks.
     * @return array
     */
    private function get_tasks($tasks_ids=null) {
        
        $result = false;
        
        if($tasks_ids === null) {
            $result = $this->tasks;
        }
        elseif(is_array($tasks_ids)) {
            $result = array_intersect_key($this->tasks, array_flip($tasks_ids));
        }
        else {
            $result = $this->tasks[$tasks_ids];
        }
        
        return $result;
    }
    
    
    /**
     * Defines the characteristics of all the tasks set (their icons, texts...)
     * 
     * @return array
     */
    private function set_tasks() {
        
        $buttons = new HtmlButtons();
        
        return [
            1 => [
                'icon'  => "&#x2753;",
                'title' => "Principe du jeu",
                'text'  => "Les zombies attaquent la ville chaque soir. Vous devez
                            renforcer ses défenses en y construisant les chantiers disponibles.
                            Les matériaux nécessaires doivent être collectés hors de la ville,
                            dans le désert hostile.<br>
                            <a href=\"#popattack\">[En savoir plus...]</a>",
                'next_task_id' => 2,
                ],
            2 => [
                'icon'  => "&#x1F3E2;",
                'title' => "Découvrir 10 bâtiments du désert",
                'text'  => "Découvrez les 10 bâtiments dissimulés dans le désert, 
                            avant que les zombies n'anéantissent votre ville.",
                'next_task_id' => 3,
                ],
            3 => [
                'icon'  => "&#x1F6E1;&#xFE0F;",
                'title' => "Contrôler 10 bâtiments pendant 5 jours",
                'text'  => "Si vous maintenez sous contrôle <strong>10 bâtiments</strong>
                            pendant <strong>5 jours</strong> consécutifs,
                            la carte sera considérée comme sécurisée et vous remporterez 
                            la partie. Un bâtiment est sous contrôle tant que son nombre 
                            de <a href=\"#popcontrol\"><strong>points de contrôle</strong></a>
                            est supérieur à celui des zombies dans la zone.",
                'next_task_id' => null,
                ],
            4 => [
                'icon'  => "&#x1F9F1;",
                'title' => "Construire le Mur d'enceinte",
                'text'  => "Les zombies attendus ce soir sont plus nombreux que 
                            les défenses de la ville. Augmentez les défenses en construisant
                            le chantier « Mur d'enceinte ».",
                'next_task_id' => 5,
                ],
            5 => [
                'icon'  => "&#x1F6B0;",
                'title' => "Construire le Puits",
                'text'  => "Construisez un puits pour accéder aux réserves d'eau de la ville.
                            L'eau vous donnera de l'énergie pour les constructions suivantes.",
                'next_task_id' => 6,
                ],
            6 => [
                'icon'  => "&#x1F4A7;",
                'title' => "Boire une ration d'eau",
                'text'  => "Prenez une ration d'eau dans le puits et buvez-la
                            afin de récupérer de l'énergie.",
                'next_task_id' => 7,
                ],
            7 => [
                'icon'  => "&#x1F6A7;",
                'title' => "Construire la Porte de la ville",
                'text'  => "Construisez la porte pour augmenter à nouveau les défenses 
                            de la ville.",
                'next_task_id' => 8,
                ],
            8 => [
                'icon'  => "&#x1F512;",
                'title' => "Fermer la porte de la ville",
                'text'  => "Fermez la porte de la ville pour activer les défenses 
                            avant l'attaque zombie du soir. Si la porte est ouverte, 
                            au moment de l'attaque, les défenses seront totalement inefficaces !",
                'next_task_id' => 9,
                ],
            9 => [
                'icon'  => "&#x1F9DF;",
                'title' => "Déclencher l'attaque zombie",
                'text'  => "Déclenchez l'attaque zombie afin de passer au jour suivant.
                            Si vous survivez, vos points d'action seront rechargés
                            et vous pourrez réaliser de nouvelles actions.
                            <p>".$buttons->button('end_cycle', false)."</p>",
                'next_task_id' => null,
                ],
            10 => [
                'icon'  => "&#x26A0;&#xFE0F;",
                'title' => "Ajouter <span class=\"nbr_missing_defenses\"></span> défenses
                            à la ville",
                'text'  => "Vous devez augmenter les défenses de la ville pour repousser 
                            les <a href=\"#popattack\" class=\"bold\"><span class=\"nbr_zombies\"></span> zombies</a>
                            qui vont attaquer la ville ce soir. Construisez
                            des <strong>chantiers de défense</strong></a>.",
                'next_task_id' => null,
                ],
            11 => [
                'icon'  => "&#x1F97E;",
                'title' => "Sortir de la ville",
                'text'  => "Sortez aux portes de la ville afin de préparer votre exploration 
                            du désert environnant.",
                'next_task_id' => 12,
                ],
            12 => [
                'icon'  => "&#x1F9ED;",
                'title' => "Tracer une expédition",
                'text'  => "Tracez un itinéraire d'expédition qui vous permettra de déplacer
                            vos citoyens vers les zones du désert que vous voulez explorer.",
                'next_task_id' => 13,
                ],
            13 => [
                'icon'  => "&#x1FAB5;",
                'title' => "Collecter des ressources",
                'text'  => "Déplacez-vous dans le désert et fouillez chaque zone sur votre chemin.
                            Ramassez les objets utiles que vous trouvez.",
                'next_task_id' => 14,
                ],
            14 => [
                'icon'  => "&#x1F306;",
                'title' => "Ramener les ressources en ville",
                'text'  => "Ramenez au dépôt de la ville les objets que vous avez ramassés
                            au cours de votre exploration du désert.",
                'next_task_id' => 15,
                ],
            15 => [
                'icon'  => "&#x1F3D7;&#xFE0F;",
                'title' => "Construisez des chantiers",
                'text'  => "Utilisez les ressources stockées dans le dépôt de la ville 
                            pour construire de nouvelles défenses ou d'autres chantiers
                            utiles à la ville.",
                'next_task_id' => 16,
                ],
            16 => [
                'icon'  => "&#x1F9DF;",
                'title' => "Survivre à l'attaque du soir",
                'text'  => "Une fois que vous avez consommé 
                            les <a href=\"#popmove\"><strong>points d'action</strong></a>
                            de vos citoyens, vous pouvez déclencher
                            <a href=\"#popattack\"><strong>l'attaque zombie</strong></a>.
                            Si vous survivez, vos points d'action seront rechargés 
                            pour une nouvelle journée.
                            <p>".$buttons->button('end_cycle', false)."</p>",
                'next_task_id' => null,
                ],
            ];
    }
}
