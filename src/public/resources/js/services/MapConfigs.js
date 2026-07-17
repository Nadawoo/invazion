export class MapConfigs {
    
    // Get the data of the map only once, even with several instances
    static config = this.#getConfigsOnce();
    
    static #getConfigsOnce() {
        
        const configsNode = document.querySelector("#configs .map");

        if (!configsNode) {
//            throw new Error("[Azimutant] Error: Map configs not found");
            return;
        }

        return JSON.parse(configsNode.textContent);
    }
    
    
    constructor() {
        
        this.config = MapConfigs.config;
    }
    
    
    get(paramName=null) {
        
        return (paramName === null) ? this.config : this.config[paramName];
    }
}
