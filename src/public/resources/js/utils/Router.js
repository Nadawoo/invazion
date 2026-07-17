/**
 * Router for handling a SPA (single page application)
 * 
 * @type type
 */
export class Router {
    
    constructor(routes) {
        
        this.routes = routes;
    }
    
    
    async render() {

        const route = this.routes[location.pathname];

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
    
    navigate(path) {

        history.pushState({}, "", path);
        render();
    }
}
