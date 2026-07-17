import { MapStats } from "../services/MapStats.js";

export class FooterBar {
    
    /**
     * Display the number of (not) discoverd cities on the map
     */
    async updateCitiesCounter() {

        const mapStats = new MapStats;
        const nbrTotalCities = await mapStats.nbrCities();
        const nbrUndiscoveredCities = await mapStats.nbrUndiscoveredCities();

        // Display the number of not discovered cities in the counter
        // at the bottom of the map
        let nbrDiscoveredCities = nbrTotalCities-nbrUndiscoveredCities;
        document.querySelector("#city_counter .number").innerText = `${nbrDiscoveredCities}/${nbrTotalCities}`;
    }
    
    
    /**
     * Display the number of zombie cores on the map
     */
    async updateZombieCoresCounter() {

        let nbrZombieCores = 0;

        if(await _cities !== null) {
            // Calculate the number of zombie cores on the map
            Object.values(_cities).forEach((caracs) => {
                // #228 is the ID of the type of building for the "zombie cores"
                if(Number(caracs.city_type_id) === 228) {
                    nbrZombieCores++;
                }
            });
        }

        // Display the number of zombie cores in the counter
        // at the bottom of the map
        document.querySelector("#zombie_cores_counter .number").innerText = `${nbrZombieCores}/${nbrZombieCores}`;
    }
}
