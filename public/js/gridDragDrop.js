document.addEventListener("DOMContentLoaded", function () {
    enableDrag();
    enableMobileDrag();
    enableTooltip();

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

            // category opslaan op cell
            cell.dataset.category = originalItem.dataset.category;
            clonedItem.removeAttribute("id");
            clonedItem.setAttribute( "draggable", "false");

            // dataset kopiëren
            const originalImage = originalItem.querySelector("img");
            const clonedImage = clonedItem.querySelector("img");
            if (originalImage && clonedImage) { 
                clonedImage.dataset.category = originalImage.dataset.category;
            }

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

    const functionItems =
        document.querySelectorAll(".functionItem");

    functionItems.forEach((item) => {
        item.setAttribute("draggable", "true");

        item.addEventListener("dragstart", function (ev) {

            ev.dataTransfer.setData(
                "text/plain",
                ev.currentTarget.id
            );

        });
    });
}


// MOBIEL TOEGEVOEGD
function enableMobileDrag() {

    const functionItems =
        document.querySelectorAll(".functionItem");

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
            clonedItem.setAttribute( "draggable", "false" );

            cell.appendChild(clonedItem);

            cell.classList.remove("available");
            cell.classList.add("occupied");
        }

        activeItem = null;
    }, { passive: false });
}

// TOOLTIP
function enableTooltip() {
    const tooltip = document.getElementById("functionTooltip");
    const gridCells = document.querySelectorAll(".gridCell");

    gridCells.forEach((cell) => {
        cell.addEventListener("mousemove", function (ev) {
            loadNeighborEffects(cell);

            tooltip.style.left = (ev.clientX + 15) + "px";
            tooltip.style.top = (ev.clientY + 15) + "px";

            tooltip.classList.remove("hidden");
        });

        cell.addEventListener("mouseleave", function () {
            tooltip.classList.add("hidden");
        });
    });
}

function loadNeighborEffects(cell) {
    const cellId = cell.dataset.id;

    if (!cellId) return;

    fetch('/grid/neighbor-effects', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cell_id: cellId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.effectTotals) {
            updateTooltipEffects(data.effectTotals);
        }
    })
    .catch(error => {
        console.error("Neighbor effects error:", error);
    });
}

function updateTooltipEffects(effectTotals) {
    Object.keys(effectTotals).forEach(function (category) {
        const element = document.querySelector(`[data-tooltip-effect-category="${category}"]`);

        if (element) {
            element.textContent = effectTotals[category];
        }
    });
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

function checkScroll(clientY) {
    const scrollThreshold = 100;
    const scrollSpeed = 10;

    if (clientY < scrollThreshold) {
        startAutoScroll(-scrollSpeed);
    } else if (
        window.innerHeight - clientY < scrollThreshold
    ) {
        startAutoScroll(scrollSpeed);

    } else {
        stopAutoScroll();
    }
}

document.addEventListener("dragover", function (ev) {
    checkScroll(ev.clientY);
});

document.addEventListener("touchmove", function (ev) {
    const touchY =
        ev.touches[0].clientY;
    checkScroll(touchY);
}, { passive: false });
document.addEventListener("dragend", stopAutoScroll);
document.addEventListener("drop", stopAutoScroll);
document.addEventListener("touchend", stopAutoScroll);