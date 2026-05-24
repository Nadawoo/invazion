export class Clipboard {
    
    /**
     * Copy the value of a textarea to the system clipboard.
     * 
     * @param {String} textareaSelector The HTML selector of the textarea to copy
     * @returns {undefined}
     */
    copyTextarea(textareaSelector) {
    
        const textarea = document.querySelector(textareaSelector);
        // Copy the content of the textarea
        navigator.clipboard.writeText(textarea.value);
        // UX: show the user that the text has been selected
        textarea.select();
    }
}
