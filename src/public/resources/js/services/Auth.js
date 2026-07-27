import { ZombLib } from "../lib/ZombLib.js";
import { displayToast } from "../misc.func.js";

export class Auth {
    
    /**
     * Connects the user to his account (sends logins and gets the API result)
     * 
     * @returns {undefined}
     */
    async logIn() {

        let emailField  = document.getElementById("email"),
            email       = emailField.value,
            password    = document.getElementById("password").value;

        if (email === '') {
            displayToast("Veuillez saisir votre adresse mail pour vous connecter.", "warning");
        }
        else if (emailField.checkValidity() !== true) {
            displayToast("L'email que vous avez saisi est invalide. Vérifiez qu'il ne contient pas une faute de frappe...", "warning");
        }
        else {
            // Calls the connection API
            let zombLib = new ZombLib();
            let json = await zombLib.callApi("POST", "user", `action=connect&email=${email}&password=${password}`);

            if (json.metas.error_code !== "success") {
                displayToast(json.metas.error_message, "warning");
            }
            else {
                const cookies = new Cookies();
                // Stores the identification token in a cookie
                cookies.setCookie("token", json.datas.token);
                // Stores the email adress for prefilling the field at the next connection
                cookies.setCookie("email", email);
                // Redirects to the main game page after the connection
                window.location.replace("index");
            }
        }
    }
    
    
    /**
     * Disconnect the user from his account
     * 
     * @returns {undefined}
     */
    async logOut() {
        
        const zombLib = new ZombLib();
        const cookies = new Cookies();
        const token = cookies.getCookie("token");
        
        // Call the disconnection API
        const json = await zombLib.callApi("POST", "user", `action=disconnect&token=${token}`);
        
        if(json.metas.error_code !== "success") {
            displayToast(json.metas.error_message, "warning");
        }
        else {
            // Destroy the authentification cookie in the browser
            cookies.setCookie("token", null);
            // Redirect to the main game page after the connection
            window.location.replace("index");
        }
    }
}
