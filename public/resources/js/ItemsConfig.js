
class ItemsConfig {
    
    
    /**
    * Returns all the tags present in the table of items, without duplicates
    * 
    * @returns {U[]}
    */
    getTagsList() {

        let chips = document.querySelectorAll('#items_table .chip');
        let tags = new Set();

        chips.forEach(chip => {
          tags.add(chip.textContent.trim());
        });

        return Array.from(tags).sort();
    }

    writeHtmlTagsList(tags) {

        let htmlTags = "";
        tags.forEach(tag => {
            htmlTags += `<li class="chip">${tag}</li>`;
        });

        document.querySelector('#tags').innerHTML = htmlTags;
    }
}


