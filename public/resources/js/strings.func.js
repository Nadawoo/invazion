/*
 * Functions to manipulate strings.
 * Put only functions in this file, no executable code.
 */


/**
 * Automatically makes the word plural if there are 2 or more.
 * Useful to match words whose quantity is not known in advance
 * without adding "if" everywhere.
 * The function only handles plurals in "S" for the moment (not those in "-aux"
 * in French).
 * 
 * @param int    $amount        The amount of the item
 * @param string $singular_word The word, in the singular form
 * 
 * @return string Examples : "1 zombie"
 *                           "5 zombies"
 */
function plural(amount, singular_word)
{

    let s = (amount <= 1) ? '' : 's';
    return `${amount} ${singular_word}${s}`;
}


/**
 * Converts a raw UTC date to a string text date
 * 
 * @param {string} utcDate  The date as returned by the Azimutant's API (UTC time + ISO 8601 format)
 *                          Example : "2020-02-18T14:51:41+01:00"
 * @return {string} The human-readable date (e.g.: "lundi 6 juin 2020 à 13h40")
 */
function dateIsoToString(utcDate) {
    // Set here the presentation you want for the date
    // Available options : https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Objets_globaux/Intl/DateTimeFormat#Syntaxe
    var dateFormat  = { weekday:'long', year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'numeric' };
    
    return Intl.DateTimeFormat('fr-FR', dateFormat).format(new Date(utcDate));
}


/**
 * Converts newlines into <br> in a text to preserves them in HTML
 * Source : https://gist.github.com/yidas/41cc9272d3dff50f3c9560fb05e7255e
 *
 * @param {string}  text Input text
 * @return {string} Filtered text
 */
function nl2br (text) {
    
    return (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+'<br>');
}


/**
 * Sanitize the HTML from a string by neutralizing critical characters
 * (doesn't remove the tags)
 * Source: https://stackoverflow.com/questions/1787322/what-is-the-htmlspecialchars-equivalent-in-javascript
 * 
 * @param {string} text
 * @returns {unresolved}
 */
function sanitizeHtml(text) {
    
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    // NB: more complex than a succession of replace() but faster
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


/**
 * Adds text formatting (links, bold...) on a raw string of text
 * 
 * @param {String} text
 * @returns {String}
 */
function text2HTML(text) {
    // **bold**
    text = text.replace(/\*\*([^\*]+)\*\*/g, '<strong>$1</strong>');
    // * list item
    text = text.replace(/\n\*/g, '\n•');
    // *italic*
    text = text.replace(/\*([^\*]+)\*/g, '<em>$1</em>');
    // Add links on the URLs
    text = text.replace(/([a-z]{3,5}:\/\/[^\s]+)/i, '<a href="$1" target="_blank">$1</a>');
    // Convert the textual newlines to HTML <br>
    text = nl2br(text);     
    
    return text;
}
