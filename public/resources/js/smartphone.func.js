/*
 * Functions related to the smartphone displayed in game. No relation with 
 * the real user's device or responsive design!
 * Put only functions in this file, no executable code.
 */


function displaySmartphone() {
    
    // If we display the smartphone for the first time
    if(document.querySelector("#popsmartphone") === null) {
        let tplSmartphone = document.querySelector("#tplSmartphone").content.cloneNode(true)
        document.querySelector("#popups").appendChild(tplSmartphone);
    }
    
    activatePhoneTab();
}


/*
 * Active l'onglet du smartphone à droite de la carte
 *
 * @param {str} tabId L'id HTML de l'onglet du smartphone à afficher (minimap, health...)
 *                    La valeur par défaut indique quel onglet sera chargé tant que
 *                    le joueur n'a cliqué sur aucun onglet.
 */
function activatePhoneTab(tabId=null) {
    
    // Définir ici l'onglet affiché par défaut dans le smartphone du jeu
    var defaultTab = "minimap";
    
    // Si clic sur un onglet du smartphone
    if (tabId !== null) {
        // Mémorise l'onglet actif pour le réafficher après actualisation de la page
        setCookieConfig('phonetab', tabId);
    }
    // Si c'est le chargement de la page
    else {
        // Récupère l'onglet actif
        tabId = getCookieConfig('phonetab');
        
        // Si le cookie n'existe pas encore, on fixe un onglet par défaut
        if (tabId === undefined) {
            
            tabId = defaultTab;
        }
    }
    
    // Par défaut, on cache tous les onglet du smartphone
    hide(["#minimap", "#health", "#zone"]);
    // Puis on affiche le contenu de l'onglet actif
    display(`#${tabId}`);
}
