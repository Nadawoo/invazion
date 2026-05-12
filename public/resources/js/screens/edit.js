import { ItemsConfig } from "../services/ItemsConfig.js";

/**
 * Function for loading the page with the JS router
 * 
 * @returns {unresolved}
 */
export async function editScreen() {
    
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


/**
 * On page load, we hide by default all the secondary options of the form
 * 
 * @returns {undefined}
 */
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


/**
 * Sends the data to create a new item in game
 */
async function createItem() {
    
    let cookies = new Cookies(),
        token = cookies.getCookie('token'),
        formData = new FormData(document.querySelector('form')),
        request = {};
    
    for (var pair of formData.entries()) {
        request += "&"+pair[0]+"="+pair[1];
    }
    
    // Sends the characteristics of the new item to the API
    let zombLib = new ZombLib();
    let json = await zombLib.callApi("POST", "configs", `action=create&type=item&token=${token}&${request}`);
    
    document.getElementById("error").innerHTML = json.metas.error_message;
}
