/**
 * Functions related to the user or the citizen.
 * Put only functions here, no executable code.
 */


/**
 * Connects the user to his account (sends logins and gets the API result)
 */
async function connectUser() {
    
    let emailField  = document.getElementById("email"),
        email       = emailField.value,
        password    = document.getElementById("password").value;
    
    if (email === '') {
        
        document.getElementById("error").innerHTML = "Veuillez indiquer l'adresse mail que vous aviez utilisée "
                        +"lors de votre inscription.<br>"
                        +"<em>Pas encore inscrit ? <a href=\"register.php\">Créez votre compte maintenant !</a></em>";
    }
    else if (emailField.checkValidity() !== true) {
        
        document.getElementById("error").innerHTML = "L'email que vous avez saisi est invalide. Vérifiez qu'il ne contient pas une faute de frappe...";
    }
    else {
        // Calls the connection API
        let json = await callApi("POST", "user", `action=connect&email=${email}&password=${password}`);

        if (json.metas.error_code !== "success") {

            document.getElementById("error").innerHTML = json.metas.error_message;
        }
        else {
            // Stores the identification token in a cookie
            setCookie("token", json.datas.token);
            // Redirects to the main game page after the connction
            window.location.replace("index.php#Outside");
        }
    }
}


/**
 * Check if the user (connected or not) has a citizen in the current game (map)
 * 
 * @returns {Boolean}
 */
function isCitizenInGame() {
    
    let citizenIdNode = document.querySelector("#citizenId");
    
    return (citizenIdNode === null) ? false : Number.isInteger(parseInt(citizenIdNode.innerText));
}


/**
 * Gets the ID of the connected player
 * @returns {int}
 */
function getCitizenId() {

    return parseInt(document.getElementById("citizenId").innerHTML);
}
