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

            const existingItem = cell.querySelector(".function-item");
            const existingImage = cell.querySelector(".grid-image");

            if (existingItem) {
                existingItem.remove();
            }

            if (existingImage) {
                existingImage.remove();
            }

            const clonedItem = originalItem.cloneNode(true);
            clonedItem.removeAttribute("id");
            clonedItem.draggable = false;

            cell.appendChild(clonedItem);
            cell.classList.remove("available");
            cell.classList.add("occupied");
        });
    });
});

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