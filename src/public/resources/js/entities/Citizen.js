export class Citizen {
    
    get id() {
        
        const citizenIdString = document.querySelector("#citizenId").textContent;
        return (citizenIdString === "") ? null : Number(citizenIdString);
    }
    
    
    get pseudo() {
        
        return document.querySelector("#citizenPseudo").textContent;
    }
    
    
    get x() {
        
        return Number(document.querySelector("#citizenCoordX").textContent);
    }
    
    
    get y() {
        
        return Number(document.querySelector("#citizenCoordY").textContent);
    }
    
    
    get mapId() {
        
        return Number(document.querySelector("#mapId").textContent);
    }
    
    
    get actionPoints() {
        
        return Number(document.querySelector("#actionPoints").textContent);
    }
    
    /**
     * The ID of city to which the citizen is attached (not the city on his zone)
     * 
     * @returns {unresolved}
     */
    get cityId() {
        
        const cityIdString = document.querySelector("#cityId").textContent;
        return (cityIdString === "") ? null : Number(cityIdString);
    }
}