document.addEventListener("DOMContentLoaded", function () {
    const functionItems = document.querySelectorAll(".function-item");
    const gridCells = document.querySelectorAll(".grid-cell");
    const functionsList = document.getElementById("functions_list");

    functionItems.forEach((item) => {
        item.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        });
    });

    gridCells.forEach((cell) => {
        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
        });

        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const itemId = ev.dataTransfer.getData("text");
            const draggedItem = document.getElementById(itemId);

            if (!draggedItem) return;
            if (cell.querySelector(".function-item")) return;

            cell.appendChild(draggedItem);
        });
    });

    functionsList.addEventListener("dragover", function (ev) {
        ev.preventDefault();
    });

    functionsList.addEventListener("drop", function (ev) {
        ev.preventDefault();

        const itemId = ev.dataTransfer.getData("text");
        const draggedItem = document.getElementById(itemId);

        if (!draggedItem) return;

        functionsList.appendChild(draggedItem);
    });
});