import { TemplatesManager } from "../utils/TemplatesManager.js";

export class Popup {
    
    async open(templateName) {
        
        // Global structure of the popup
        const fragmentPopupStructure = await TemplatesManager.instantiate("popups", "tplPopup");
        fragmentPopupStructure.querySelector("#popup").classList.add("force_visibility");
        
        // Add the content in the popup
        const fragmentPopupContent = await TemplatesManager.instantiate("popups", templateName);
        fragmentPopupStructure.querySelector("#popup .content").appendChild(fragmentPopupContent);
        
        // Place the pop-up by erasing the eventual previous content
        document.querySelector("#popupWrapper").replaceChildren(fragmentPopupStructure);
    }
    
    
    close() {
        
        document.querySelector("#popup").classList.remove("force_visibility");
    }
}
