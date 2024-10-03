# Futures fonctionnalités du jeu
Une fois le jeu officiellement lancé (voir [PROGRESSION.md](PROGRESSION.md), des fonctionnalités
propres à Azimutant seront implémentées. En voici un aperçu non exhaustif, et susceptible
d'évoluer fortement en fonction de l'évolution de l'orientation du jeu et des priorités.

## A court terme

* Notifier sur la page principale les messages postés dans l'espace de discussion

* Générer automatiquement des zombies à intervalles réguliers. Le bouton de pop actuel est provisoire pour les tests.  
Le rythme d'apparition des zombies dépendra du rythme de la partie.

* Créer une page de profil pour afficher les récompenses (pictos) gagnés. Ils sont d'ores et déjà enregistrés (nombre de zombies tués...) mais il n'y a pas d'interface pour les voir.

## A moyen terme : cartes multiples & mécanismes de dynamisation du jeu

### ► Cartes multiples
La grande carte commune actuelle est un bac à sable pour les tests.   
Le joueur pourra lancer sa partie seul ou avec d'autres joueurs. Pas de quota de nombre de joueurs pour que la ville démarre (trop incertain). Le jeu sera jouable seul (voir plus bas), mais jouer à plusieurs apportera de nombreux avantages (Azimutant est un jeu collaboratif).

### ► Citoyens-bots si manque d'humains dans une partie
* Présence de citoyens bots afin que le jeu soit jouable quel que soit le nombre d'actifs. Mais jouer avec des humains sera toujours plus avantageux (meilleures caractéristiques...).   
Chaque joueur humain qui rejoint une partie prend la place d'un bot, le but est d'avoir un maximum d'humains dans la partie.  
Les bots auront des caractéristiques aléatoires pour animer la partie, inspirées des comportements humains :
	* degré de désobéissance
	* malveillance
	* étourderie (ex : partir dans la mauvaise direction en expédition)
	* plages de présence (comme un humain n'est pas disponible 24h/24)
	* autres...     	
	
Ainsi, même avec des bots, le joueur devra :
   - réfléchir à la composition d'une équipe pour partir en expédition
   - faire face à des constructions de chantier imprévues (un bot aura mis des PA dans le mauvais chantier...)
   - etc.
   
Pour contrôler un bot, le joueur devra lui donner régulièrement une drogue spéciale fournie par un chantier, qui devra être construit et entretenu.
   - Intérêt : incite à jouer avec des humains (pas de coût de chantier) plutôt qu'avec les bots
   - Donner plusieurs cachets à un bot augmentera sa fiabilité, mais les cachets seront précieux => le joueur devra faire des choix stratégiques (donner plusieurs cachets a un bot pour qu'il soit plus fiable, ou un seul à plusieurs bots pour partir plus nombreux en expédition)
   - Roleplay : le type de drogue est à définir (doit justifier qu'il rend les bots plus fiables). Il pourrait même s'agir d'autre chose que d'une drogue, cela donne l'impression que les bots sont des escalves serviles. Ce pourrait être le "nounours" de Hordes : rassurant, il calme les sentiments négatifs des bots (désespoir, angoisse, égoïsme...)
   
Pour mettre des PA dans les chantiers avec les bots, le joueur ne contrôlera pas chaque bot pour les faire travailler un par un. Il aura un réservoir global de PA (somme des PA de tous les bots) et chaque PA utilisé sera déduit aléatoirement d'un bot.
   - Intérêt gameplay : pouvoir gérer précisément les PA des bots serait trop facile (par exemple en gardant des bots avec tous leurs PA pour les expéditions). 
   - Intérêt UX : pas répétitif à exécuter pour le joueur (pas besoin de prendre le contrôle de chaque bot 1 par 1)
   

### ► 2 types de parties en parallèle
* Des cartes éphémères, détruites après la défaite finale. Même principe que Hordes, mais avec un rythme de jeu plus dynamique. 
* Des cartes persistantes, à rythme de jeu plus lent. Le succès dans les parties éphémères apporte des bonus de construction de la carte persistante.
Intérêt : les villes éphémères apportent une vraie pression et un sentiment de victoire/défaite ; le monde persistant assure un sentiment de progression.

### ► Rythme du jeu
   * Permettre des rythmes plus rapides que des cycles de 24 heures. Ce rythme permet des stratégies  approfondies mais est trop lent pour beaucoup de gens.
   * La mise en place de plusieurs rythmes au choix sera nécessaire car aucun ne conviendra à tout le monde. Il s'agira d'une liste prédéfinie (ex : 24h, 12h, 6h, 1h), une saisie totalement libre n'a pas d'intérêt et ne ferait que perdre le joueur inexpérimenté.


## A moyen terme

* **Plantes et toxicité :** 
Des champignons sont disposés aléatoirement sur la carte. Dilemme pour les joueurs :
   - Plus une case contient de champignons et moins les zombies y apparaissent => intérêt à les laisser
   - Cependant, récupérer ces champignons donne une ressource => intérêt à les prendre   
   (La nature de la ressource reste à déterminer : un carburant artisanal ?)   
Par ailleurs, moins il y a de champignons sur la carte et plus le taux toxicité global de la carte augmente (matérialisé par une jauge). Lié ensuite à l'idée de Skaen sur les "pro-tox" et les "anti-tox" https://github.com/Nadawoo/invazion/issues/3   
(à affiner)

* **Interface pour que les joueurs puissent éditer/créer :**
	* Les objets
	* Les chantiers
Prérequis : Avoir mis en place les cartes multiples. Les récompenses gagnées lors de ces parties personnalisées seront comptabilisées séparément des "officielles" afin de de ne pas fausser les compteurs.

## A long terme : 

* **Monde & anti-monde :** chaque action sur la carte du monde aura l'effet inverse sur la carte de l'anti-monde, comme un miroir. Par exemple, tuer un zombie sur la carte ajoutera un zombie dans la zone correspondante de l'anti-monde, et réciproquement. Pour les fouilles, la regénération d'une zone du monde épuisera les ressources dans l'anti-monde, et réciproquement.  
Ceci obligera à réfléchir stratégiquement au lieu de tout rusher. Par exemple, exterminer systématiquement les zombies et épuiser toutes les ressources ne sera pas forcément une bonne option car les nouvelles ressources devront ensuite être cherchées dans l'anti-monde, qui se retrouvera surpeuplé de zombies.  
Le passage d'un monde à l'autre sera rare et coûteux afin qu'il y ait un vrai challenge.

Prérequis : Avoir mis en place les cartes multiples.

* **Moderniser l'espace de discussion** en affichant les nouveaux message en enfilade, sans besoin d'ouvrir chaque sujet un par un. Intérêt : le titre d'un sujet seul ne reflète pas forcément bien son contenu, on peut ignorer sans le savoir des discussions qui nous intéressent.
La présentation en forum classique (sujet par sujet) restera disponible, leux deux ont leurs avantages. 

* **Possibilité d'éditer les conditions et conséquences des actions de jeu.** Exemple : si X fait Y, alors il se passe Z. Le travail de recensement est terminé (listage des conditions et conséquences, syntaxe des instructions) mais sa mise en oeuvre effective demandera d'importants développements.
Ces personnalisations se feront sur des cartes dédiées car elles modifient profondément l'équilibre du jeu. Les récompenses gagnées lors de ces parties personnalisées seront comptabilisées séparément des "officielles" afin de de ne pas fausser les compteurs.

