
class ItemsConfig {
    
    
    /**
    * Returns all the tags present in the table of items, without duplicates
    * 
    * @returns {U[]}
    */
    getTagsList() {

        let chips = document.querySelectorAll('#items_table .chip');
        let tagCount = new Map();

        // Add the tag to the Map and increment its counter
        chips.forEach(chip => {
            let tag = chip.textContent.trim();
            if (tagCount.has(tag)) {
                tagCount.set(tag, tagCount.get(tag) + 1);
            } else {
                tagCount.set(tag, 1);
            }
        });

        // Sort the Map to an alphabetical-sorted array
        let uniqueTagsWithCount = Array.from(tagCount.entries());
        uniqueTagsWithCount.sort((a, b) => a[0].localeCompare(b[0]));

        return uniqueTagsWithCount;
    }

    writeHtmlTagsList(tags) {
        
        let htmlTags = "";
        tags.forEach(([tag, count]) => {
            htmlTags += `<li class="chip" data-tag="${tag}">${tag} (${count})</li>`;
        });

        document.querySelector('#tags').innerHTML = htmlTags;
    }
}


