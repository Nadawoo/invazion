export class MapStats {
    
   /**
    * Calculate the number of all the cities on the map
    */
    async nbrCities() {

        const cities = await _cities;
        return cities !== null ? Object.keys(cities).length : 0;
    }
   
   
    /**
     * Calculate the number of not discovered cities on the map
     */
    async nbrUndiscoveredCities() {

        let nbrUndiscoveredCities = 0;

        if(await _cities !== null) {
            Object.values(_cities).forEach((caracs) => {
                let zone = document.querySelector(`#zone${caracs.coord_x}_${caracs.coord_y} .square_container`);
                // #233 = ID of the city type for "Undiscovered building" in the Azimutant's API
                if(Number(zone.dataset.citytypeid) === 233) {
                    nbrUndiscoveredCities++;
                }
            });
        }

        return nbrUndiscoveredCities;
    }
}
