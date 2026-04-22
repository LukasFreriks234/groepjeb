document.addEventListener("DOMContentLoaded", function () {
    enableDrag();
    enableMobileDrag();

    const gridCells = document.querySelectorAll(".gridCell");

    gridCells.forEach((cell) => {

        // drag over toestaan
        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
        });

        // drop
        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const itemId = ev.dataTransfer.getData("text/plain");
            const originalItem = document.getElementById(itemId);

            if (!originalItem) return;

            // oude inhoud verwijderen
            const existingItem = cell.querySelector(".functionItem");
            const existingImage = cell.querySelector(".gridImage");

            if (existingItem) existingItem.remove();
            if (existingImage) existingImage.remove();

            // clone maken
            const clonedItem = originalItem.cloneNode(true);
            clonedItem.removeAttribute("id");
            clonedItem.setAttribute("draggable", "false");

            // in cell zetten
            cell.appendChild(clonedItem);

            // styling update
            cell.classList.remove("available");
            cell.classList.add("occupied");
        });
    });
});


// DRAG FUNCTIE
function enableDrag() {
    const functionItems = document.querySelectorAll(".functionItem");

    functionItems.forEach((item) => {
        item.setAttribute("draggable", "true");

        item.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData("text/plain", ev.currentTarget.id);
        });
    });
}


// MOBIEL TOEGEVOEGD
function enableMobileDrag() {
    const functionItems = document.querySelectorAll(".functionItem");

    let activeItem = null;

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
        const cell = element ? element.closest(".gridCell") : null;

        if (cell) {
            const existingItem = cell.querySelector(".functionItem");
            const existingImage = cell.querySelector(".gridImage");

            if (existingItem) existingItem.remove();
            if (existingImage) existingImage.remove();

            const clonedItem = activeItem.cloneNode(true);
            clonedItem.removeAttribute("id");
            clonedItem.setAttribute("draggable", "false");

            cell.appendChild(clonedItem);

            cell.classList.remove("available");
            cell.classList.add("occupied");
        }

        activeItem = null;
    }, { passive: false });
}


// AUTO SCROLL
let scrollInterval;

function stopAutoScroll() {
    clearInterval(scrollInterval);
    scrollInterval = null;
}

function startAutoScroll(direction) {
    if (scrollInterval) return;

    scrollInterval = setInterval(() => {
        window.scrollBy(0, direction);
    }, 10);
}

document.addEventListener("dragover", function (ev) {
    const scrollThreshold = 100;
    const scrollSpeed = 15;

    if (ev.clientY < scrollThreshold) {
        startAutoScroll(-scrollSpeed);
    } else if (window.innerHeight - ev.clientY < scrollThreshold) {
        startAutoScroll(scrollSpeed);
    } else {
        stopAutoScroll();
    }
});

document.addEventListener("dragend", stopAutoScroll);
document.addEventListener("drop", stopAutoScroll);