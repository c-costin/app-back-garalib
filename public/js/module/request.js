export const request = {

        // Connect with Costin server
        // endpoint: "https://c-costin-server.eddi.cloud/api/",

        // Connect with Charlotte server
        endpoint: "https://charlotte-carpentier-server.eddi.cloud/api/",
        token: "",

        getTokenApi: async function ()
        {
            const response = await fetch(`${request.endpoint}login_check`, {
                method: "POST",
                headers: {
                    "Accept": "*/*",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    email: "admin@admin.com",
                    password: "admin",
                }),
            });

            const responseLogin = await response.json();

            request.token = responseLogin.token;
        },

        findAllUsers: async function()
        {
            const response = await fetch(`${request.endpoint}user/`, {
                method: "GET",
                headers: {
                    "Accept": "*/*",
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${request.token}`,
                }
            });

            const usersList = await response.json();

            return usersList;
        },

        findAllGarages: async function()
        {
            const response = await fetch(`${request.endpoint}garage/`, {
                method: "GET",
                headers: {
                    "Accept": "*/*",
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${request.token}`,
                }
            });

            const garagesList = await response.json();

            return garagesList;
        },

        removeUser: async function(userId)
        {
            const response = await fetch(`${request.endpoint}user/delete/${userId}`, {
                method: "DELETE",
                headers: {
                    "Accept": "*/*",
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${request.token}`,
                }
            });

            const usersList = await response.json();

            return usersList;
        },

        removeGarage: async function(garageId)
        {
            console.log("ok");
            const response = await fetch(`${request.endpoint}garage/delete/${garageId}`, {
                method: "DELETE",
                headers: {
                    "Accept": "*/*",
                    "Authorization": `Bearer ${request.token}`,
                }
            });

            const garagesList = await response.json();

            console.log(garagesList);
            return garagesList;
        },
}

