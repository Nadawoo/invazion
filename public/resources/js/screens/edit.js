import { ItemsConfig } from "../services/ItemsConfig.js";

/**
 * Function for loading the page with the JS router
 * 
 * @returns {unresolved}
 */
export async function EditScreen() {
    
    const response = await fetch("/screens/edit.php");
    return await response.text();
}


/**
 * Page-specific functions to load
 * 
 * @returns {undefined}
 */
export function initEditPage() {
    
    hideElements();
    filterItems();
}


function hideElements() {
    
    hide([
        '#block_findable',
        '#block_findable_advanced',
        '#block_compo',
        '#block_apgain',
        '#block_malus',
        '#block_healing',
        '#block_weapon',
        '#block_bag',
        '#block_drop',
        '#block_loads',
        '#block_solidity_custom',
        '#block_killing_rate'
    ]);
}


/**
 * Filter the items by tag in the table of items
 * 
 * @returns {undefined}
 */
function filterItems() {
    
    let itemsConfig = new ItemsConfig(),
        tags = itemsConfig.getTagsList();

    itemsConfig.writeHtmlTagsList(tags);

    const filterButtons = document.querySelectorAll('#tags .chip');
    const tableRows = document.querySelectorAll('#items_table tbody tr');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            filterTableByTag();
        });
    });

    function filterTableByTag() {
        const activeTags = Array.from(filterButtons)
            .filter(button => button.classList.contains('active'))
            .map(button => button.dataset.tag);

        tableRows.forEach(row => {
            const rowTags = row.dataset.tags.split(' ');
            const isVisible = activeTags.every(tag => rowTags.includes(tag));

            row.style.display = isVisible || activeTags.length === 0 ? '' : 'none';
        });
    }
}
