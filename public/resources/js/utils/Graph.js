/**
 * Tools for building graphs and pathfinding
 */
class Graph {
    
    /**
     * Build a graph giving the neighbors for each city. Useful for pathfinding.
     * 
     * @param {Object} roads The roads as returned by the "connections" API. Each
     *                       item is a pair of city ID origin/destination. Example:
     *                       {
     *                          { source: 1216, target: 1220 },
     *                          { source: 1216, target: 1221 },
     *                          { source: 1216, target: 1222 },
     *                          { source: 1217, target: 1222 }
     *                       }
     * @returns {unresolved} Example of result:
     *   {
     *      1216: [1220, 1221, 1222],
     *      1220: [1216],
     *      1221: [1216],
     *      1222: [1216, 1217],
     *      1217: [1222]
     *   }
     */
    buildGraph(roads) {

        const graph = {};

        roads.forEach(road => {

            if (!graph[road.source]) graph[road.source] = [];
            if (!graph[road.target]) graph[road.target] = [];
            
            // NB: the road can be bidirectional
            graph[road.source].push(road.target);
            graph[road.target].push(road.source);

        });

        return graph;
    }


    /**
     * Get the neighbors cities' IDs for one given city.
     * 
     * @param {int} cityId The ID of the city for which you want the neighbors
     * @param {Object} graph A graph as returned by the buildGraph() method
     * @returns {Array|CityConnections.#getNeighbors.graph}
     */
    getNeighbors(cityId, graph) {
        
        return graph[cityId] || [];
    }
    
    
    /**
     * Pathfing using the A* algorithm
     * 
     * @param {Object} graph A graph as returned by the buildGraph() method
     * @param {Object} cities The coordinates of the cities, structured like this:
     *                          1216: { x: 0, y: 0 },
     *                          1217: { x: 1, y: 1 },
     *                          1220: { x: 2, y: 0 },
     *                          1221: { x: 2, y: 1 }
     * @param {int} start The ID of the departure city 
     * @param {type} goal The ID of the destination city 
     * @returns {Array|Graph.#reconstructPath.path}
     */
    getPath(graph, cities, start, goal) {

        const openSet = new Set([start]);
        const cameFrom = {};
        const gScore = {};
        const fScore = {};

        Object.keys(graph).forEach(n => {
            gScore[n] = Infinity;
            fScore[n] = Infinity;
        });

        gScore[start] = 0;
        fScore[start] = this.#heuristic(start, goal, cities);

        while (openSet.size > 0) {
            
            let current = null;
            let bestScore = Infinity;

            openSet.forEach(node => {
                if(fScore[node] < bestScore) {
                  bestScore = fScore[node];
                  current = node;
                }
            });

            if (current == goal) {
                return this.#reconstructPath(cameFrom, current);
            }

            openSet.delete(current);

            for(const neighbor of graph[current]) {

                const tentativeG = gScore[current] + 1;

                if(tentativeG < gScore[neighbor]) {
                    cameFrom[neighbor] = current;
                    gScore[neighbor] = tentativeG;
                    fScore[neighbor] = tentativeG + this.#heuristic(neighbor, goal, cities);
                    openSet.add(neighbor);
                }
            }
        }

        return null;
    }
    
    
    #heuristic(sourceCityId, targetCityId, citiesCoords) {
        
        const dx = citiesCoords[sourceCityId].coord_x - citiesCoords[targetCityId].coord_x;
        const dy = citiesCoords[sourceCityId].coord_y - citiesCoords[targetCityId].coord_y;
        return Math.sqrt(dx * dx + dy * dy);
    }
    
    
    #reconstructPath(cameFrom, current) {

        const path = [current];

        while (cameFrom[current]) {
            current = cameFrom[current];
            path.unshift(current);
        }

        return path;
    }
}
