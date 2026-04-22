document.addEventListener("DOMContentLoaded", function () {
    const functionItems = document.querySelectorAll(".function-item");
    const gridCells = document.querySelectorAll(".grid-cell");
    const functionImages = document.querySelectorAll(".function-item img");

    let activeItem = null;

    functionImages.forEach((img) => {
        img.setAttribute("draggable", "false");
    });

    function placeItemInCell(originalItem, cell) {
        if (!originalItem || !cell) return;

        const existingItem = cell.querySelector(".function-item");
        const existingImage = cell.querySelector(".grid-image");

        if (existingItem) existingItem.remove();
        if (existingImage) existingImage.remove();

        const clonedItem = originalItem.cloneNode(true);
        clonedItem.removeAttribute("id");
        clonedItem.setAttribute("draggable", "false");

        cell.appendChild(clonedItem);
        cell.classList.remove("available");
        cell.classList.add("occupied");
    }

    // Desktop drag & drop
    functionItems.forEach((item) => {
        item.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData("text/plain", item.id);
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

            placeItemInCell(originalItem, cell);
        });
    });

    // Mobiel
    functionItems.forEach((item) => {
        item.addEventListener("touchstart", function (ev) {
            activeItem = item;
            ev.preventDefault();
        }, { passive: false });
    });

    document.addEventListener("touchmove", function (ev) {
        if (activeItem) {
            ev.preventDefault();
        }
    }, { passive: false });

    document.addEventListener("touchend", function (ev) {
        if (!activeItem) return;

        const touch = ev.changedTouches[0];
        const element = document.elementFromPoint(touch.clientX, touch.clientY);
        const dropTarget = element ? element.closest(".grid-cell") : null;

        if (dropTarget) {
            placeItemInCell(activeItem, dropTarget);
        }

        activeItem = null;
    }, { passive: false });
});