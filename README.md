# Le projet InvaZion, en deux mots

**Jouer :** https://invaziongame.nadazone.fr/  
**Discord** : https://discord.gg/2GRPTyM

* **Le concept :** développer un jeu basé sur les principes essentiels du jeu Hordes (http://hordes.fr), à savoir : des joueurs devant s'organiser ensemble pour assurer leur survie face aux zombies. (Cela dit, les zombies ne sont là que pour donner un cadre. Si vous préférez voir un affrontement des chocolatines contre les pains au chocolat, vous en avez le droit.)
* **La question qui se posait :** Hordes est un jeu complexe qui ne peut pas être développé par une personne seule. Il fallait donc permettre un travail collaboratif fluide, durable et le plus ouvert possible.
* **La solution retenue :** la méthode « API First ». Développer d'un côté le moteur du jeu, le rendre public au moyen d'APIs, et permettre à toutes les personnes volontaires de contribuer à l'interface graphique sur Github. 

# Principes directeurs du projet
## Principes de jeu
* Objectif : survivre aux Hordes zombies
* Le jeu est collaboratif façon Hordes :
  * Pas de collaboration imposée, le jeu solo voire la trahison sont possibles.
  * Pas de rôle ni de camp défini comme ils peuvent l'être dans les jeux de rôle. Le joueur décide au fur et à mesure comment il agit.
  * Les actions proposées par le jeu ne sont pas "bonnes" ou "mauvaises" en elles-mêmes, elles procurent certains avantages et inconvénients.
  * Les actions proposées par le jeu doivent favoriser les interactions entre joueurs (collaboration, rivalités, échanges...).
* Respect des concepts originaux de Hordes tout en modernisant de son gameplay. Il ne s'agit pas de réaliser une copie conforme (aucun intérêt).
* Gestion des inactifs : le jeu doit être intéressant quel que soit le nombre de joueurs.
* Gestion du temps : permettre des rythmes de jeu adaptés aux différents profils de joueurs.
	
## Principes de développement
* Ouvrir le jeu au maximum afin que la communauté puisse se l'approprier :
  * Permettre aux joueurs de modifier les éléments du jeu (objets, chantiers, actions...) 
  * Interface open-source sur Github
  * API 100% fonctionnelles
  * Multisupport (site web, appli mobile...)
* Serveur commun afin de ne pas fragmenter la communauté (important pour un jeu multijoueurs)

# Organisation des fichiers
Voir [FILE-STRUCTURE.md](FILE-STRUCTURE.md) 
