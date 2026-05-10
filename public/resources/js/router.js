import { EditScreen, initEditPage } from "/resources/js/screens/edit.js";

const routes = {
//  "/": HomeScreen,
    "/edit": {
        view: EditScreen,
        init: initEditPage
    }
};


export function navigate(path) {
    
    history.pushState({}, "", path);
    render();
}


async function render() {
    
    const route = routes[location.pathname];

    if(!route) {
        document.querySelector("#app").innerHTML = "404";
        return;
    }

    const html = await route.view();
    document.querySelector("#app").innerHTML = html;

    if(route.init) {
        route.init();
    }
}


// Handle the previous/next button of the browser
window.addEventListener("popstate", render);
// Display the page asked in the URL
render();
