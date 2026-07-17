import { editScreen, initEditPage } from "/resources/js/screens/edit.js";
import { gamesScreen, initGamesPage } from "/resources/js/screens/games.js";
import { Router } from "./utils/Router.js";

const routes = {
//  "/": HomeScreen,
    "/games": {
        view: gamesScreen,
        init: initGamesPage
    },
    "/edit": {
        view: editScreen,
        init: initEditPage
    }
};

const router = new Router(routes);

// Handle the previous/next button of the browser
window.addEventListener("popstate", router.render);
// Display the page asked in the URL
router.render();
