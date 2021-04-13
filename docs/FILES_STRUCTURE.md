# Organisation des fichiers
*Ce document vous permet de vous familiariser avec l'organisation des fichiers.  
Ils sont listés par ordre d'importance.*

### Structure générale
* index.php : page principale du jeu. Structure appelant les éléments HTML présents dans le dossier /view.
* register.php : page où le joueur se crée un compte
* connect.php : page où le joueur se connecte à son compte
* ZombLib : librairie pour exploiter les API du jeu - voir la doc : https://invazion.nadazone.fr/zomblib-doc.php
* /view : tout ce qui concerne la vue, c'est-à-dire les éléments HTML de l'interface
  * /view/elements : les composants HTML de l'interface (menu, carte, boutons...)
  * /view/generators : scripts générant des vues partielles (ex : générer une zone du désert).
  Appelez ces pages en javascript pour actualiser des éléments précis de l'interface
  au lieu de recharger toute la page.
* /controller : fonctions pour traiter certaines données avant affichage
* /resources : fichiers appelables par le client : images, CSS, javascript
* /archived : code non utilisé à ce jour mais éventuellement réutilisable

### Dossier /view
* HtmlPage.php : structure de base de la page (header et footer HTML)
* HtmlLayout.php : classe principale générant les blocs HTML du jeu, hors ceux placés dans une classe dédiée.
* HtmlMap.php : génère le HTML de la carte
* HtmlCityEnclosure.php : génère le HTML de l'intérieur de la ville (chantiers, dépôt, puits...)
* HtmlMyZone.php : génère le HTML de la zone où se trouve le joueur
* HtmlButtons.php : génère les boutons d'action (attaquer un zombie...)
* HtmlMovementPaddle.php : génère les flèches de déplacement à côté de la carte
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
* /js : fonctions javascript

### Dossier /archived
  * block_citizens_vs_zombies.php : encadré affichant les points de contrôle des humains et zombies dans la zone
  * night.css : essai de design sombre pour le site. Inutilisé à ce jour.
  * items_panel.html/items_panel.css : panneaux d'objets amovibles sous la carte
                      (désormais supprimés, les objets sont affichés à droite)


# Documentation CSS

### Ordre des superpositions (z-index)

Les "z-index" en CSS sont organisés précisément afin d'éviter les superpositions bloquantes 
(ex : blocs d'action cachés sous la zone). On change le z-index de dizaine à chaque changement 
de type d'élément, et on utilise les numéros intermédiaires pour les éventuels sous-éléments 
de même type :

* 0 : la carte
     - 1 : la horde mobile (triangles rouges)
     - 2 : les infobulles au survol des zones
*  10 : zoom sur la zone (par clic sur le bouton « Afficher ma zone »)
    - 10 : le fond hexagonal
    - 11 : les blocs (citoyens, zombies, objets, ville...)
    - 12 : les boutons "afficher les objets / afficher ma zone"
*  20 : les blocs d’action (affichés/masqués par les boutons bouger/fouiller/zombies…)
*  30 : l'intérieur de la ville
    - 30 : le fond sombre par-dessus la carte
    - 31 : l'interface de la ville
*  40 : le volet "Communications"
*  50 : la liste des notifications (cloche en haut) et la future barre du haut qui affichera 
        les actions de profil dans un menu déroulant (modifier mon profil, me déconnecter...)
*  60 : la pop-up
