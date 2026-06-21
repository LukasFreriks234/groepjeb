document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("myInput");
    const rows = document.querySelectorAll("#functionsList .groupItem");

    if (!input || !rows.length) {
        return;
    }

    function filterGroups() {
        const filter = input.value.trim().toUpperCase();

        rows.forEach(function (row) {
            const nameElement = row.querySelector(".functionName");
            const functionsElement = row.querySelector(".groupFunctions");

            const name = nameElement
                ? nameElement.innerText.toUpperCase()
                : "";

            const functions = functionsElement
                ? functionsElement.innerText.toUpperCase()
                : "";

            row.style.display =
                name.includes(filter) || functions.includes(filter)
                    ? ""
                    : "none";
        });
    }

    input.addEventListener("input", filterGroups);

    filterGroups();
});