/**
 * Get the characteristics of the player's zone (number of zombies...)
 * Read-only.
 * 
 * @type type
 */
export class Zone {
    
    constructor() {
        
        this.myZone = document.querySelector("#me").parentNode;
    }
    
    
    get x() {
        
        return Number(this.myZone.dataset.coordx);
    }
    
    
    get y() {
        
        return Number(this.myZone.dataset.coordy);
    }
    
    
    /**
     * Get the HTML ID of the zone, example : #zone14_10
     * 
     * @returns {String}
     */
    get zoneHtmlId() {
        
        return `#zone${this.x}_${this.y}`;
    }
    
    
    get nbrCitizens() {
        
        return Number(this.myZone.dataset.citizens);
    }
    
    
    get nbrZombies() {
        
        return Number(this.myZone.dataset.zombies);
    }
    
    
    get nbrItems() {
        
        return Number(this.myZone.dataset.items);
    }
    
    
    get controlPointsCitizens() {
        
        return Number(this.myZone.dataset.controlpointscitizens);
    }
    
    
    get controlPointsZombies() {
        
        return Number(this.myZone.dataset.controlpointszombies);
    }
    
    
    get cityId() {
        
        const cityId = this.myZone.dataset.cityid;
        return cityId === "" ? null : Number(cityId);
    }
    
    
    get cityTypeId() {
        
        const cityTypeId = this.myZone.dataset.citytypeid;
        return cityTypeId === "" ? null : Number(cityTypeId);
    }
}
