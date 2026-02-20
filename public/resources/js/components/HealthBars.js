class HealthBars {
    
    static MAX_HEALTHPOINTS = 10;

    displayHealthBars() {
        
        // Display the health bars
        document.querySelectorAll('.hexagon .healthbar').forEach((healthBar)=> {
            this.#displayHealthBar(healthBar);
        });
    }


    #displayHealthBar(healthBar) {

        const zombies = healthBar.closest('.square_container').dataset.zombies;
        const healthPoints = this.constructor.MAX_HEALTHPOINTS - zombies;

        // Display the the healthbar only if not full
        if(zombies > 0) {
            healthBar.classList.remove("hidden");
            // Create the chunks if not already done
            if(healthBar.innerHTML === "") {
                this.#createChunks(healthBar);
                this.#updateHealth(healthBar, healthPoints);
            }
        }
    }


    #createChunks(healthbar) {

        for(let i = 0; i < this.constructor.MAX_HEALTHPOINTS; i++) {
            const chunk = document.createElement("div");
            chunk.classList.add("chunk");
            healthbar.appendChild(chunk);
        }
    }


    #updateHealth(healthBar, currentHealthpoints) {

        const chunks = healthBar.querySelectorAll(".chunk");
        chunks.forEach((chunk, i) => {
            if (i < currentHealthpoints) {
                chunk.classList.remove("lost");
            } else {
                chunk.classList.add("lost");
            }
        });
    }
}
