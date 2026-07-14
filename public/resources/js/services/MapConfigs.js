export class MapConfigs {
    
    // Get the data of the map only once, even with several instances
    static config = JSON.parse(
        document.querySelector("#configs .map").textContent
    );
    
    constructor() {
        
        this.config = MapConfigs.config;
    }
    
    
    get(paramName=null) {
        
        return (paramName === null) ? this.config : this.config[paramName];
    }
}
