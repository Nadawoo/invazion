/**
 * Tools for calculating coordinates
 */
export class Coordinates {
    
    /**
     * Gives the string coordinates of each of the 6 zones around a given zone.
     * The coordinates are structured in the HTML standard for Azimutant: X_Y
     * 
     * @param {int} coordX
     * @param {int} coordY
     * @returns
     */
    getNeighborsHtmlCoords(coordX, coordY) {
        
        return {
            "northwest":`${coordX-1}_${coordY-1}`,
            "northeast":`${coordX+1}_${coordY-1}`,
            "west":     `${coordX-2}_${coordY}`,
            "east":     `${coordX+2}_${coordY}`,
            "southwest":`${coordX-1}_${coordY+1}`,
            "southeast":`${coordX+1}_${coordY+1}`,
            };
    }
}
