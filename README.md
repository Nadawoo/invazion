# Le projet InvaZion, en deux mots

**Jouer :** https://invaziongame.nadazone.fr/  
**Discord** : https://discord.gg/2GRPTyM

**Le concept :** développer un jeu basé sur les principes essentiels du jeu Hordes (http://hordes.fr), à savoir : des joueurs devant s'organiser ensemble pour assurer leur survie face aux zombies. (Cela dit, les zombies ne sont là que pour donner un cadre. Si vous préférez voir un affrontement des chocolatines contre les pains au chocolat, vous en avez le droit.)

Le jeu vise à être hautement customisable et le plus ouvert possible (interface, plugins...)  afin que la communauté puisse participer à son évolution indépendamment des développements officiels.

# Principes directeurs du projet
## Principes de jeu
* Bases similiraies à hordes :
   * Objectif : survivre aux Hordes zombies
   * Les actions faites en jeu débloquent des récompenses (pictos), y compris si l'action n'est pas cruciale. Plus les récompenses sont nombreuses et plus les possibilités d'objectifs individuels seront variés.
* Le jeu est collaboratif façon Hordes :
  * Pas de collaboration imposée, le jeu solo voire la trahison sont possibles.
  * Pas de rôle ni de camp défini comme ils peuvent l'être dans les jeux de rôle. Le joueur décide au fur et à mesure comment il agit.
  * Les actions proposées par le jeu ne sont pas "bonnes" ou "mauvaises" en elles-mêmes, elles procurent certains avantages et inconvénients.
  * Les actions proposées par le jeu doivent favoriser les interactions entre joueurs (collaboration, rivalités, échanges...).
* Respect des concepts originaux de Hordes tout en modernisant de son gameplay. Il ne s'agit pas de réaliser une copie conforme (aucun intérêt).
* Gestion des inactifs : le jeu doit être intéressant quel que soit le nombre de joueurs.
* Gestion du temps : permettre des rythmes de jeu adaptés aux différents profils de joueurs.
	
## Principes de développement
* Méthode « API First » : développer d'un côté le moteur du jeu, le rendre public au moyen d'APIs, et permettre à toutes les personnes volontaires de contribuer à l'interface graphique. 
* Ouvrir le jeu au maximum afin que la communauté puisse se l'approprier :
  * Permettre aux joueurs de modifier les éléments du jeu (objets, chantiers, actions...) 
  * Interface open-source sur Github
  * API 100% fonctionnelles
  * Multisupport (site web, appli mobile...)
* Serveur commun afin de ne pas fragmenter la communauté (important pour un jeu multijoueurs)

# Feuille de route (roadmap)

## A court terme

* Notifier sur la page principale les messages postés dans l'espace de discussion

* Générer automatiquement des zombies à intervalles réguliers. Le bouton de pop actuel est provisoire pour les tests.  
Le rythme d'apparition des zombies dépendra du rythme de la partie.

* Créer une page de profil pour afficher les récompenses (pictos) gagnés. Ils sont d'ores et déjà enregistrés (nombre de zombies tués...) mais il n'y a pas d'interface pour les voir.

## A moyen terme : cartes multiples & mécanismes de dynamisation du jeu

* **Cartes multiples.** La grande carte commune actuelle est un bac à sable pour les tests.   
Le joueur pourra lancer sa partie seul ou avec d'autres joueurs. Pas de quota de nombre de joueurs pour que la ville démarre (trop incertain). Le jeu sera jouable seul (voir plus bas), mais jouer à plusieurs apportera de nombreux avantages (Invazion est un jeu collaboratif).

* **Présence de citoyens bots** afin que le jeu soit jouable quel que soit le nombre d'actifs. Mais jouer avec des humains sera toujours plus avantageux (meilleures caractéristiques...).   
Chaque joueur humain qui rejoint une partie prend la place d'un bot, le but est d'avoir un maximum d'humains dans la partie.  
Les bots auront des caractéristiques aléatoires pour animer la partie, inspirées des comportements humains :
	* degré de désobéissance
	* malveillance
	* étourderie (ex : partir dans la mauvaise direction en expédition)
	* autres...
Ainsi, même avec des bots, le joueur devra :
   - réfléchir à la composition d'une équipe pour partir en expédition
   - faire face à des construction de chantier imprévues
   - etc.

* **2 types de parties en parallèle :**
	* Des cartes éphémères, détruites après la défaite finale. Même principe que Hordes, mais avec un rythme de jeu plus dynamique. 
	* Des cartes persistantes, à rythme de jeu plus lent. Le succès dans les parties éphémères apporte des bonus de construction de la carte persistante.
	Intérêt : les villes éphémères apportent une vraie pression et un sentiment de victoire/défaite ; le monde persistant assure un sentiment de progression.
	
* **Rythme du jeu :**
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

# Organisation des fichiers
Voir [FILES-STRUCTURE.md](FILES-STRUCTURE.md) 
