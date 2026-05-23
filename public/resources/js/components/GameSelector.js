class GameSelector {
    
    async populateAllGamesList(games) {
        
        const fragment = document.createDocumentFragment();
        
        Object.entries(games).forEach(([gameId, gameDatas]) => {
            const tplGame = this.#populateGameCardTemplate(gameId, gameDatas);
            fragment.appendChild(tplGame); 
        });
        
        document.querySelector("#allGames .games_list").appendChild(fragment);
    }
    
    
    populateMyGamesList(games, myCurrentGameId) {
        
        if(myCurrentGameId !== null) {        
            const tplGame = this.#populateGameCardTemplate(myCurrentGameId, games[myCurrentGameId]);
            document.querySelector("#myGames .games_list").appendChild(tplGame);
        }
    }
    
    
    #populateGameCardTemplate(gameId, gameDatas) {
        
        const tplGame = document.querySelector("#tplGame").content.cloneNode(true);
        let citizens = "";
        
        // List of the citizens in this game
        Object.values(gameDatas.citizens).forEach((citizen) => {
            citizens += `<li>${citizen} ·</li>`;
        });

        tplGame.querySelector(".map_id").innerText = gameId;
        tplGame.querySelector(".map_name").innerText = gameDatas.name;
        tplGame.querySelector(".description").innerText = gameDatas.descr_purpose;
        tplGame.querySelector(".dimensions").innerText = `${gameDatas.map_cols} × ${gameDatas.map_rows}`;
        tplGame.querySelector('input[name="params[map_id]"]').value = gameId;

        if(citizens !== "") {
            tplGame.querySelector(".citizens").innerHTML = citizens;
        }
        
        return tplGame;
    }
}
