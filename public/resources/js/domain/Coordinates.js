/**
 * Tools for calculating coordinates
 */
export class Coordinates {

    /**
     * Calculates the number of zones between two zones
     * 
     * @param {int} x1    The X coordinate of the first zone
     * @param {int} y1    The Y coordinate of the first zone
     * @param {int} x2 The X coordinate of the second zone
     * @param {int} y2 The Y coordinate of the second zone
     * @return {int}
     */
    getDistance(x1, y1, x2, y2) {

        // We calculate the relative coordinates of the citizen as if the city was in [0:0].
        // And we remove the eventual negative sign, because the orientation (N/S/W/E)
        // has no influence on the distance.
        let distanceX = Math.abs(x1 - x2),
            distanceY = Math.abs(y1 - y2);

        // Formula provided by https://www.redblobgames.com/grids/hexagons/#distances
        return distanceY + Math.max(0, (distanceX-distanceY)/2);
    }
    
    
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
