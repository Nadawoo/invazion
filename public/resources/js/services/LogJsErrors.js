import { displayToast } from "../misc.func.js";

/**
 * Write the javascript errors in a temporary log inside the HTML page.
 * Useful for helping the user to share easily his errors.
 * 
 * @type type
 */
export class LogJsErrors {
    
    constructor() {
        
        this.alreadyDisplayedErrors = new Set();
    }
    
    
    listenToErrors() {

        // Display the catched JS errors in a toast
        window.addEventListener("error", (event) => {
            const message = event.message || "Une erreur inattendue s'est produite.";
            const stack = event.error.stack;

            displayToast(`Une erreur technique est survenue. L'affichage est peut être incorrect.`, "warning");

            this.#writeErrorInLog(message, stack);
        });

        // Display the not catched JS errors in a toast
        window.addEventListener("unhandledrejection", (event) => {
            const message = event.reason?.message ||
                            String(event.reason) ||
                            "Une erreur asynchrone s'est produite.";
            
            const stack = event.reason?.stack || "(stack indisponible)";
            
            displayToast(`Une erreur technique est survenue. L'affichage est peut être incorrect.`, "warning");

            this.#writeErrorInLog(message, stack);
        });
    }


    #writeErrorInLog(message, stack) {

        // Store each message only once to avoid having an enormous log
        if(this.alreadyDisplayedErrors.has(message)) return;

        this.alreadyDisplayedErrors.add(message);

        const entry = `\n---\n**${message}**\n    ${stack}`;

        document.querySelector("#jsLog textarea").value += entry;
    }
}
