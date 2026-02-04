class Items {
    
    /**
     * Add the items inside a <ul class="items_list"></ul>
     * 
     * @param {string} domSelector The unique DOM selector of the block to populate.
     *                             Ex: "#ground .items_list"
     * @param {int} mapId
     * @param {int} coordX
     * @param {int} coordY
     * @returns {undefined}
     */
    async populateList(domSelector, mapId, coordX, coordY) {
        
        const cookies = new Cookies(),
              token = cookies.getCookie('token');
        const zombLib = new ZombLib(),
              htmlCoord = coordX+"_"+coordY;
        
        const json = await zombLib.callApi("GET", "maps", `action=get&map_id=${mapId}&token=${token}&zones=${htmlCoord}`);    
        const zone = json.datas.zones[htmlCoord];
        
        populateItemsList(domSelector, zone.items);
        hide(`${domSelector} .loader`);
    }
    
    
    /**
     * Builds the complete HTML to display an item (icon inside a square and with 
     * an explicative pop-up when clicking on it)
     * 
     * @param {int} itemId
     * @param {array} itemCaracs The characteristics of the given item, 
     *                           as returned by the "items" API
     * @param {int} itemAmount
     * @returns {htmlItem.template} Document fragment containing the HTML of the item.
     *              innerText doesn't work with fragments, insert in the page like this:
     *              document.querySelector("#myDiv").prepend(Items.item())
     */
    item(itemId, itemCaracs, itemAmount=1) {

        // Default values for the items. Useful if an item existing in the database
        // but is not in the list of the items of the current game.
        if(itemCaracs === undefined) {
            itemCaracs = {
                "name":"(Objet inconnu)",
                "icon_path":null,
                "icon_symbol":`<span class="red">#${itemId}</span>`,
                "descr_ambiance":"",
                "descr_purpose":"[Bug] Cet objet est inconnu.\
                                 Signalez-le au responsable du jeu."
            };
        }

        // Gets a blank HTML template of an item entry
        let template = document.querySelector("#tplItem").content.cloneNode(true);
        let icon = this.icon(itemCaracs['icon_path'], itemCaracs['icon_symbol']),
            itemTypeClass = null,
            bgColor = "";

        if(itemCaracs["item_type"] === "resource" || itemCaracs["item_type"] === "resource_rare") {
            itemTypeClass = "type_resource";
            bgColor = "#7FB3D5";
        } else if(itemCaracs["item_type"] === "weapon") {
            itemTypeClass = "type_weapon";
            bgColor = "#EC7063";
        } else if(itemCaracs["item_type"] === "food" || itemCaracs["item_type"] === "water") {
            itemTypeClass = "type_booster";
            bgColor = "orange";
        }
    //    else if(itemCaracs["item_type"] === "tool") {
    //        bgColor = "#27AE60"; 
    //    }

        // Populates the blank template with the item data
        template.querySelector('.item_label').style.background = `radial-gradient(white 0%, ${bgColor} 100%)`;
        template.querySelector('.form_drop button[name="params[item_id]"]').value  = itemId;
        template.querySelector('.form_pickup button[name="params[item_id]"]').value = itemId;
        template.querySelector('.icon').innerHTML = icon;
        template.querySelector('.icon').setAttribute('aria-label', itemCaracs['name']);
        template.querySelector('.details .icon').innerHTML = icon;
        template.querySelector('.item_name').innerHTML = itemCaracs['name'];
        template.querySelector('.descr_ambiance').innerHTML = itemCaracs['descr_ambiance'];
        template.querySelector('.descr_purpose').innerHTML  = itemCaracs['descr_purpose'];

        // Display the amount of items only if there is more than one
        if(itemAmount > 1) {
            template.querySelector('.dot_number').innerHTML = itemAmount;
    //        display('.dot_number');
            template.querySelector('.dot_number').classList.remove('hidden');
        }

        // Additionally, if the item is rare (no matter its type), add a gold frame
        if(itemCaracs["preciousness"] > 0) {
            template.querySelector('.item_label').classList.add("precious");
            template.querySelector('.item_label .preciousness').classList.remove("hidden");
        }
        // Display the type of item (resource, weapon...)
        if(itemTypeClass !== null) {
            template.querySelector(`.item_label .${itemTypeClass}`).classList.remove("hidden");
        }
        // Display if the item is heavy
        if(itemCaracs["heaviness"] > 0) {
            template.querySelector('.item_label .heaviness').classList.remove("hidden");
        }
        // Display the button to use the item as a weapon
        if(itemCaracs["item_type"] === "weapon") {
            template.querySelector("form[name='fight'] input[name='params[item_id]']").value = itemId;
        }
        // Display if the item gives defenses
        if(itemCaracs["defenses"] > 0) {
            template.querySelector('.item_label .defenses').classList.remove("hidden");
            template.querySelector('.item_label .defenses .nbr_defenses').innerText = itemCaracs["defenses"];
        }

        return template;
    }
    

    /**
     * Generates the icon for an item
     * 
     * @param {string} iconPath The path to the image (PNG, GIF...), 
     *                         as returned by the "configs" API of Azimutant
     * @param {string} iconSymbol The code for the HTML icon (&#...), 
     *                          as returned by the "configs" API of Azimutant
     * @param {int} height The dimensions to resize the image
     * @returns {string} HTML for the icon (<img> tag HTML symbol)
     */
    icon(iconPath, iconSymbol, height=null) {

        if(iconPath !== null) {
            // If an image file is set (PNG, GIF, display it as icon
            let dimensions = (height !== null) ? `height="${height}" width="${height}"`  : "";
            return `<img src="../resources/img/${iconPath}" ${dimensions}>`;
        }
        else if(iconSymbol !== null) {
            // If there is no file but a HTML symbol, display it as icon
            return iconSymbol;
        }
        else {
            // If nothing is set, display a "?" as icon
            return "&#10067;";
        }
    }
    
    /**
     * Display/hide the tootip of an item when clicking on its icon
     * 
     * @param {object} event
     * @returns {undefined}
     */
    toggleTooltip(event) {

        let itemLabel = event.target.closest(".item_label"),
            tooltip = itemLabel.querySelector(".details");
        
        // If the item's tooltip is already opened, we just hide it
        if (this.#isTooltipOpen(tooltip)) {
            this.#closeTooltip(tooltip, itemLabel);
            // Avoids instant re-opening of the tooltip, as it is a click in .item_label too
            event.stopPropagation();
        }
        else {
            // If we want to open a new tooltip, first close all the other open tooltips
            this.#closeAllTooltips();
            // Then, display the intended tooltip
            this.#openTooltip(tooltip, itemLabel);
            this.#handleTooltipOverflow(tooltip);
        }
    }
    
    
    #isTooltipOpen(tooltip) {
        
        return !tooltip.classList.contains("hidden");
    }
    
    
    #closeTooltip(tooltip, itemLabel) {
        
        tooltip.classList.add("hidden");
        itemLabel.style.border = null;
        
        // Reactivate the scroll previously desactivated by opentooltip()
        const itemsList = itemLabel.closest("#actions fieldset");
        itemsList.style.overflow = "auto";
    }
    
    
    #closeAllTooltips() {
        
        const visibleTooltips = document.querySelectorAll(".item_label .details:not(.hidden)");

        visibleTooltips.forEach(tooltip => {
            tooltip.classList.add("hidden");
            tooltip.closest(".item_label").style.border = null;
        });
    }
    
    
    #openTooltip(tooltip, itemLabel) {
        
        tooltip.classList.remove("hidden");
        itemLabel.style.border = "4px solid darkred";
        
        // Desactivate the scroll while the tooltip is open (to avoid shifting)
        const itemsList = itemLabel.closest("#actions fieldset");
        itemsList.style.overflow = "visible";
    }
    
    
    /**
     * Correct the position of the tootip if it owerflows the current box
     * 
     * @param {type} tooltip
     * @returns {undefined}
     */
    #handleTooltipOverflow(tooltip) {
        
        const parent = tooltip.closest(".items_list");
        if (!parent) return;

        // Reset previous adjustments (important!)
        tooltip.style.transform = "";

        const parentRect = parent.getBoundingClientRect();
        let rect = tooltip.getBoundingClientRect();

        let deltaX = 0;
        let deltaY = 0;

        // -----------------------------
        // HORIZONTAL CORRECTION
        // -----------------------------

        // Overflowing right
        if (rect.right > parentRect.right) {
            deltaX = parentRect.right - rect.right - 4; 
        }
        // Overflowing left
        if (rect.left + deltaX < parentRect.left) {
            deltaX = parentRect.left - rect.left + 4;
        }
        // Apply both X corrections
        tooltip.style.transform = `translate(${deltaX}px, 0px)`;

        // Re-measure after those adjustments
        rect = tooltip.getBoundingClientRect();

        // -----------------------------
        // VERTICAL CORRECTION
        // -----------------------------
        
        // Overflowing top
        if (rect.bottom > parentRect.bottom) {
            deltaY = parentRect.bottom - rect.bottom - 4;
        }
        // Overflowing bottom
        if (rect.top + deltaY < parentRect.top) {
            deltaY = parentRect.top - rect.top + 4;
        }
        // Apply both Y corrections
        tooltip.style.transform = `translate(${deltaX}px, ${deltaY}px)`;

        // Optional final re-measure if you want to log/debug :
        // console.log(tooltip.getBoundingClientRect());
    }
}
