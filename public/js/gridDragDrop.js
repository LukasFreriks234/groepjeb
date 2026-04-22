document.addEventListener("DOMContentLoaded", function () {

    enableDrag();

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

            // 🔥 oude inhoud verwijderen (image + clone)
            const existingItem = cell.querySelector(".functionItem");
            const existingImage = cell.querySelector(".gridImage");

            if (existingItem) existingItem.remove();
            if (existingImage) existingImage.remove();

            // 🔥 clone maken
            const clonedItem = originalItem.cloneNode(true);
            clonedItem.removeAttribute("id");
            clonedItem.setAttribute("draggable", "false");

            // 🔥 in cell zetten
            cell.appendChild(clonedItem);

            // 🔥 styling update
            cell.classList.remove("available");
            cell.classList.add("occupied");
        });
    });
});


// 🔥 DRAG FUNCTIE (BELANGRIJK VOOR DELETE)
function enableDrag() {
    const functionItems = document.querySelectorAll(".functionItem");

    functionItems.forEach((item) => {

        item.setAttribute("draggable", "true");

        item.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData("text/plain", ev.currentTarget.id);
        });
    });
}


// 🔥 AUTO SCROLL (ongewijzigd maar opgeschoond)
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