import {
    request
} from "./request.js";

export const paginationGarages = {
    arrayStart: 0,
    arrayEnd: 30,
    counter: 30,
    garagesList: [],

    init: async function () {

        // Load garages from API
        await paginationGarages.loaderGarages();

        // Load page numbers
        paginationGarages.loaderPaginator();

        // Attach events to page numbers
        paginationGarages.attachEvent();
    },

    // Attach events to page numbers
    attachEvent: function () {

        // Add event listener for previous button
        document.querySelector(".js-previousPage").addEventListener("click", paginationGarages.previousPage);

        // Add event listener for next button
        document.querySelector(".js-nextPage").addEventListener("click", paginationGarages.nextPage);

        // Add event listener for each page number
        document.querySelectorAll(".js-numberPage").forEach((element) => {
            element.addEventListener("click", paginationGarages.selectedPage);
        });

        // Add event listener for delete button
        document.querySelectorAll(".js-garageDelete").forEach(element => {
            element.addEventListener('click', paginationGarages.handleRemoveGarage);
        }); 
    },

    // Load garages from API
    loaderGarages: async function () {

        // Display loading gif
        document.getElementById('loading-car').style.display = 'flex';
        document.getElementById('loading-car').style.justifyContent = 'center';

        paginationGarages.garagesList = await request.findAllGarages();

        for (let i = paginationGarages.arrayStart; i < paginationGarages.arrayEnd; i++) {
            paginationGarages.addToDomRow(paginationGarages.garagesList[i]);
        }

        // Hide loading gif
        document.getElementById('loading-car').style.display = 'none';
    },

    // Load page numbers
    loaderPaginator: function () {
        const numberPages = paginationGarages.calcNumberPage(paginationGarages.garagesList, paginationGarages.counter);

        for (let i = 1; i <= numberPages; i++) {
            paginationGarages.addToDomNumberPage(i);
        }
    },

    // Calculate the number of pages
    calcNumberPage: function (array, number) {
        return Math.ceil(array.length / number);
    },

    // Go to previous page
    previousPage: function () {
        if (paginationGarages.arrayStart > 0) {
            paginationGarages.arrayStart -= paginationGarages.counter;
            paginationGarages.arrayEnd -= paginationGarages.counter;

            document.querySelector(".js-listGarages").innerHTML = "";

            for (let i = paginationGarages.arrayStart; i < paginationGarages.arrayEnd; i++) {
                paginationGarages.addToDomRow(paginationGarages.garagesList[i]);
            }
        } else {
            return paginationGarages.addToDomRow(paginationGarages.garageList[i + 1]);
        }
    },

    // Go to next page
    nextPage: function () {
        if (paginationGarages.arrayEnd < paginationGarages.garagesList.length) {
            paginationGarages.arrayStart += paginationGarages.counter;
            paginationGarages.arrayEnd += paginationGarages.counter;

            document.querySelector(".js-listGarages").innerHTML = "";

            for (let i = paginationGarages.arrayStart; i < paginationGarages.arrayEnd; i++) {
                paginationGarages.addToDomRow(paginationGarages.garagesList[i]);
            }
        } else {
            return paginationGarages.addToDomRow(paginationGarages.garagesList[i - 1]);
        }
    },

    // Go to specific page based on page number
    selectedPage: function (e) {
        const numberPage = e.target.dataset.pageNumber;

        paginationGarages.arrayStart =
            (numberPage - 1) * paginationGarages.counter;
        paginationGarages.arrayEnd = numberPage * paginationGarages.counter;

        document.querySelector(".js-listGarages").innerHTML = "";

        for (
            let i = paginationGarages.arrayStart; i < paginationGarages.arrayEnd; i++) {
            paginationGarages.addToDomRow(paginationGarages.garagesList[i]);
        }
    },

    // Add a row per garage to the DOM
    addToDomRow: function (data) {
        const templateArrayRow = document.getElementById("js-templateArrayRow");
        const newArrayRow = templateArrayRow.content.cloneNode(true);

        newArrayRow.querySelector(".js-garageId").textContent = `# ${data.id}`;
        newArrayRow.querySelector(".js-garageName").textContent = data.name;
        newArrayRow.querySelector(
            ".js-garageRegisterNumber"
        ).textContent = data.registerNumber;
        newArrayRow.querySelector(".js-garagePhone").textContent = data.phone;
        newArrayRow.querySelector(".js-garageEmail").textContent = data.email;
        newArrayRow.querySelector(".js-garageDelete").dataset.garageId = data.id;

        newArrayRow.querySelector(".js-garageRead").pathname = `/support/garage/${data.id}`;
        newArrayRow.querySelector(".js-garageProfilUpdate").pathname = `/support/garage/editer/${data.id}`;
        newArrayRow.querySelector(".js-garageScheduleUpdate").pathname = `/support/schedule/editer/${data.id}`;
        newArrayRow.querySelector(".js-garageMemberUpdate").pathname = `/support/garage/membres/${data.id}`;
        newArrayRow.querySelector(".js-garageTypeUpdate").pathname = `/support/type/editer/${data.id}`;

        document.querySelector(".js-listGarages").append(newArrayRow);
    },

    // Add a page number to the DOM
    addToDomNumberPage: function (number) {
        const templateNumberPage = document.getElementById("js-templateNumberPage");
        const newLi = templateNumberPage.content.cloneNode(true);

        newLi.querySelector(".js-numberPage").textContent = number;
        newLi.querySelector(".js-numberPage").dataset.pageNumber = number;

        document.querySelector(".js-numberPages").append(newLi);
    },

    handleRemoveGarage: async function(e)
    {
        // Get Garage Id via dataset
        const garageId = e.currentTarget.dataset.garageId;

        // Remove row in DOM
        e.currentTarget.closest('tr').remove();

        // Call API for Delete Garage into database
        await request.removeGarage(garageId);

        // Call API for Update garagesList
        paginationGarages.garagesList = await request.findAllGarages();
    }

};