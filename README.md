# Le projet InvaZion, en deux mots

**Site officiel :** https://invazion.nadazone.fr/

* **Le concept :** développer un jeu basé sur les principes essentiels du jeu Hordes, à savoir : des joueurs devant s'organiser ensemble pour assurer leur survie face aux zombies. (Cela dit, les zombies ne sont là que pour donner un cadre. Si vous préférez voir un affrontement des chocolatines contre les pains au chocolat, vous en avez le droit.)
* **La question qui se posait :** Hordes est un jeu complexe qui ne peut pas être développé par une personne seule. Il fallait donc permettre un travail collaboratif fluide, durable et le plus ouvert possible.
* **La solution retenue :** la méthode « API First ». Développer d'un côté le moteur du jeu, le rendre public au moyen d'APIs, et permettre à toutes les personnes volontaires de contribuer à l'interface graphique sur Github. 

Démo jouable : https://invaziongame.nadazone.fr/

## Organisation des fichiers
### Structure générale
* index.php : page principale du jeu. Structure appelant les éléments HTML présents dans le dossier /view.
* register.php : page où le joueur se crée un compte
* connect.php : page où le joueur se connecte à son compte
* ZombLib : librairie pour exploiter les API du jeu - voir la doc : https://invazion.nadazone.fr/zomblib-doc.php
* /view : contient les classes et fonctions générant les éléments HTML spécifiques (carte...)
* /controller : fonctions pour traiter certaines données avant affichage
* /resources : fichiers appelables par le client : images, CSS, javascript

### Dossier /view
* BuilHtml.php : classe principale générant les éléments HTML du jeu, hors ceux placés dans une classe dédiée.
* HtmlPage.php : structure de base de la page (header et footer HTML)
* HtmlMap.php : génère le HTML de la carte
* HtmlCityEnclosure.php : génère le HTML de l'intérieur de la ville (chantiers, banque, puits...)
* HtmlMyzone.php : génère le HTML de la zone où se trouve le joueur
* HtmlButtons.php : génère les boutons d'action (attaquer un zombie...)
* movement_paddle.php : génère les flèches de déplacement à côté de la carte
* minimap.php : génère le smartphone-gps à côté de la carte
* plural.php : petite fonction pour simplifier l'ajout des "s" aux mots au pluriel

### Dossier /controller
[à compléter]

### Dossier /resources
* /img : contient les images
* / css
  * sitelayout.css : mise en page du site. Ne contient aucun élément relatif au jeu.
  * gamelayout.css : CSS principale du jeu. Contient tous les styles du jeu qui ne sont pas placés dans une CSS dédiée.
  * map.css : CSS de la carte
  * city.css : CSS de l'intérieur de la ville
  * myzone.css : CSS de la zone où se rouve le joueur
  * gps.css : CSS du smartphone-gps à côté de la carte
  * popup.css : gère la pop-up 100% CSS
  * night.css : essai de design sombre pour le site. Inutilisé à ce jour.
* script.js : fonctions javascript
 
