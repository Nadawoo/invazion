class HealthBars {
    
    static NBR_CHUNKS = 10;

    displayHealthBars() {
        
        // Display the health bars
        document.querySelectorAll('.hexagon .healthbar').forEach((healthBar)=> {
            this.#displayHealthBar(healthBar);
        });
    }


    #displayHealthBar(healthBar) {

        const zombies = healthBar.closest(".square_container").dataset.zombies;
        const cityDefenses = healthBar.closest(".cityframe").dataset.defenses;
        const missingChunks = cityDefenses - zombies;
        
        // Display the the healthbar only if not full
        if(missingChunks > 0) {
            healthBar.classList.remove("hidden");
            // Create the chunks if not already done
            if(healthBar.innerHTML === "") {
                this.#createChunks(healthBar);
                this.#updateHealth(healthBar, missingChunks);
            }
        }
    }


    #createChunks(healthbar) {

        for(let i = 0; i < this.constructor.NBR_CHUNKS; i++) {
            const chunk = document.createElement("div");
            chunk.classList.add("chunk");
            healthbar.appendChild(chunk);
        }
    }


    #updateHealth(healthBar, missingDefenses) {

        const chunks = healthBar.querySelectorAll(".chunk");
        chunks.forEach((chunk, i) => {
            if(i < missingDefenses) {
                chunk.classList.remove("lost");
            } else {
                chunk.classList.add("lost");
            }
        });
    }
}
