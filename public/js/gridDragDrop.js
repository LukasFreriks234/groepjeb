document.addEventListener("DOMContentLoaded", function () {
    const functionItems = document.querySelectorAll(".function-item");
    const gridCells = document.querySelectorAll(".grid-cell");
    const functionImages = document.querySelectorAll(".function-item img");

    functionImages.forEach((img) => {
        img.setAttribute("draggable", "false");
    });

    functionItems.forEach((item) => {
        item.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData("text/plain", ev.currentTarget.id);
        });
    });

    gridCells.forEach((cell) => {
        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
        });

        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const itemId = ev.dataTransfer.getData("text/plain");
            const originalItem = document.getElementById(itemId);

            if (!originalItem) return;
            if (cell.classList.contains("occupied") || cell.querySelector(".function-item")) return;

            const clonedItem = originalItem.cloneNode(true);
            clonedItem.removeAttribute("id");
            clonedItem.draggable = false;

            cell.appendChild(clonedItem);
        });
    });
});