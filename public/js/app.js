import { request } from "./module/request.js";
import { paginationUsers } from "./module/paginationUsers.js";
import { paginationGarages } from "./module/paginationGarages.js";

const app = {
    init: async function()
    {
        await request.getTokenApi();
        paginationUsers.init();
        paginationGarages.init();
    }
}


document.addEventListener("DOMContentLoaded", app.init);