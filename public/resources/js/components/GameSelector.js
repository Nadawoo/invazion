class GameSelector {
    
    populateGamesList(games) {
        
        console.log();
        
        Object.entries(games).forEach(([mapId, game]) => {
            const tplGame = document.querySelector("#tplGame").content.cloneNode(true);
            let citizens = "";
            
            // List of the citizens in this game
            Object.values(game.citizens).forEach((citizen) => {
                citizens += `<li>${citizen} ·</li>`;
            });
            
            tplGame.querySelector(".map_id").innerText = mapId;
            tplGame.querySelector(".map_name").innerText = game.name;
            tplGame.querySelector(".description").innerText = game.descr_purpose;
            tplGame.querySelector(".dimensions").innerText = `${game.map_cols} × ${game.map_rows}`;
            
            if(citizens !== "") {
                tplGame.querySelector(".citizens").innerHTML = citizens;
            }
            
            document.querySelector("#games_list").append(tplGame);
        });
    }
}
