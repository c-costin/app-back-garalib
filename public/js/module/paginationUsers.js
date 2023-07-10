import {
    request
} from "./request.js";

export const paginationUsers = {

    arrayStart: 0,
    arrayEnd: 30,
    counter: 30,
    usersList: [],

    init: async function () {
                
        // Load users from API
        await paginationUsers.loaderUsers();
        // Load page numbers
        paginationUsers.loaderPaginator();
        // Attach events to page numbers
        paginationUsers.attachEvents();
    },

    // Attach events to page numbers
    attachEvents: function () {

        // Add event listener for previous button
        document.querySelector(".js-previousPage").addEventListener("click", paginationUsers.previousPage);

        // Add event listener for next button
        document.querySelector(".js-nextPage").addEventListener("click", paginationUsers.nextPage);

        // Add event listener for each page number
        document.querySelectorAll(".js-numberPage").forEach(element => {
            element.addEventListener("click", paginationUsers.selectedPage);
        });

        // Add event listener for delete button
        document.querySelectorAll(".js-userDelete").forEach(element => {
            element.addEventListener('click', paginationUsers.handleRemoveUser);
        }); 
    },

    // Load users from API
    loaderUsers: async function () {

        // Display loading gif
        document.getElementById('loading-car').style.display = 'flex';
        document.getElementById('loading-car').style.justifyContent = 'center';

        paginationUsers.usersList = await request.findAllUsers();

        for (let i = paginationUsers.arrayStart; i < paginationUsers.arrayEnd; i++) {
            paginationUsers.addToDomRow(paginationUsers.usersList[i]);
        }

        // Hide loading gif
        document.getElementById('loading-car').style.display = 'none';
    },

    // Load page numbers
    loaderPaginator: function () {
        const numberPages = paginationUsers.calcNumberPage(paginationUsers.usersList, paginationUsers.counter);

        for (let i = 1; i <= numberPages; i++) {
            paginationUsers.addToDomNumberPage(i);
        }
    },

    // Calculate the number of pages
    calcNumberPage: function (array, number) {
        return Math.ceil(array.length / number);
    },

    // Go to previous page
    previousPage: function () {
        if (paginationUsers.arrayStart > 0) {
            paginationUsers.arrayStart = paginationUsers.arrayStart - 30
            paginationUsers.arrayEnd = paginationUsers.arrayEnd - 30

            document.querySelector(".js-listUsers").innerHTML = "";

            for (let i = paginationUsers.arrayStart; i < paginationUsers.arrayEnd; i++) {
                paginationUsers.addToDomRow(paginationUsers.usersList[i]);
            }
        } else {
            return paginationUsers.addToDomRow(paginationUsers.usersList[i + 1]);
        }
    },

    // Go to next page
    nextPage: function () {
        if (paginationUsers.arrayEnd < paginationUsers.usersList.length) {
            paginationUsers.arrayStart = paginationUsers.arrayStart + 30
            paginationUsers.arrayEnd = paginationUsers.arrayEnd + 30

            document.querySelector(".js-listUsers").innerHTML = "";

            for (let i = paginationUsers.arrayStart; i < paginationUsers.arrayEnd; i++) {
                paginationUsers.addToDomRow(paginationUsers.usersList[i]);
            }
        } else {
            return paginationUsers.addToDomRow(paginationUsers.usersList[i - 1]);
        }
    },

    // Go to specific page based on page number
    selectedPage: function (e) 
    {
        const numberPage = e.target.dataset.pageNumber;

        paginationUsers.arrayStart = (paginationUsers.counter * numberPage) - paginationUsers.counter;
        paginationUsers.arrayEnd = paginationUsers.counter * numberPage;

        document.querySelector(".js-listUsers").innerHTML = "";

        for (let i = paginationUsers.arrayStart; i < paginationUsers.arrayEnd; i++) {
            paginationUsers.addToDomRow(paginationUsers.usersList[i]);
        }
    },

    // Add a row per user to the DOM
    addToDomRow: function (data) {
        const templateArrayRow = document.getElementById("js-templateArrayRow");
        const newArrayRow = templateArrayRow.content.cloneNode(true);

        newArrayRow.querySelector(".js-userId").textContent = `# ${data.id}`;
        newArrayRow.querySelector(".js-userLastname").textContent = data.lastname;
        newArrayRow.querySelector(".js-userFirstname").textContent = data.firstname;
        newArrayRow.querySelector(".js-userRoles").textContent = data.roles;
        newArrayRow.querySelector(".js-userEmail").textContent = data.email;
        newArrayRow.querySelector(".js-userDelete").dataset.userId = data.id;

        newArrayRow.querySelector(".js-userRead").pathname = `/support/utilisateur/${data.id}`;
        newArrayRow.querySelector(".js-userProfilUpdate").pathname = `/support/utilisateur/editer/${data.id}`;
        newArrayRow.querySelector(".js-userAddressUpdate").pathname = `/support/address/editer/${data.id}`;
        newArrayRow.querySelector(".js-userVehicleUpdate").pathname = `/support/vehicule/editer/${data.id}`;
        newArrayRow.querySelector(".js-userAppointmentUpdate").pathname = `/support/appointment/editer/${data.id}`;
        newArrayRow.querySelector(".js-userReviewUpdate").pathname = `/support/review/editer/${data.id}`;

        document.querySelector(".js-listUsers").append(newArrayRow);
    },

    // Add a page number to the DOM
    addToDomNumberPage: function (number) {
        const templateNumberPage = document.getElementById("js-templateNumberPage");
        const newLi = templateNumberPage.content.cloneNode(true);

        newLi.querySelector(".js-numberPage").textContent = number;
        newLi.querySelector(".js-numberPage").dataset.pageNumber = number;

        document.querySelector(".js-numberPages").append(newLi);
    },

    handleRemoveUser: async function(e)
    {
        // Get User Id via dataset
        const userId = e.currentTarget.dataset.userId;

        // Remove row in DOM
        e.currentTarget.closest('tr').remove();

        // Call API for Delete User into database
        await request.removeUser(userId);

        // Call API for Update usersList
        paginationUsers.usersList = await request.findAllUsers();
    }
}