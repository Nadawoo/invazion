/**
 * Translate the litterals of the game in various languages
 */
class Translator {
    
    /**
     * Main class to translate the game.
     * 
     * @param {string} lang Set to "en" to translate the game in english.
     *                      Set to "fr" for the french.
     *                      No other language available at the moment.
     * @returns {undefined}
     */
    translate(lang) {
        
        // Specific case: don't translate anything if the language asked for 
        // is "french", because the game is already written in french by default.
        if(lang === "fr") {
            return;
        }
        
        // For the other languages, load the appropriation JSON file
        fetch(`/resources/translations/${lang}.json`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error while loading the translation file.');
            }
            return response.json();
        })
        .then(allTranslations => this.#translateData(allTranslations, lang))
        .catch(error => console.error('Unexpected error:', error));
    }
    
    
    #translateData(allTranslations, lang) {
        
        // The litterals are grouped in scopes (header, menu, city...)
        // for a clear organization
        document.querySelectorAll("[data-section]").forEach((scope) => {
            let scopeName = scope.dataset.section;
            // Inside the scope, we translate all the tags having the "translation" attribute
            scope.querySelectorAll("[data-translate]").forEach((element) => {
                let stringName = element.dataset.translate,
                    translatedString = allTranslations[scopeName][stringName];
                if(translatedString !== undefined) {
                    element.textContent = translatedString;
                } else {
                    console.log(`Translation missing for "${scopeName}.${stringName}" in ${lang}.json`)
                }
            });
        });
    }
}

