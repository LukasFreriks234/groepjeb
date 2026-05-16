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
            clonedItem.setAttribute("draggable", "false");

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

            // opslaan + effect table updaten
            saveFunctionInGrid(cell, originalItem);
        });
    });
});


// DRAG FUNCTIE
function enableDrag() {
    const functionItems = document.querySelectorAll(".functionItem");

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

            cell.dataset.category = activeItem.dataset.category;

            cell.appendChild(clonedItem);

            cell.classList.remove("available");
            cell.classList.add("occupied");

            // opslaan + effect table updaten
            saveFunctionInGrid(cell, activeItem);
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

            tooltip.classList.remove("hidden");

            const padding = 10;
            const tooltipWidth = tooltip.offsetWidth;
            const tooltipHeight = tooltip.offsetHeight;

            let left = ev.clientX + 15;
            let top = ev.clientY + 15;

            // Als tooltip rechts buiten het scherm valt, zet hem links van de cursor
            if (left + tooltipWidth > window.innerWidth - padding) {
                left = ev.clientX - tooltipWidth - 15;
            }

            // Als tooltip onder buiten het scherm valt, zet hem boven de cursor
            if (top + tooltipHeight > window.innerHeight - padding) {
                top = ev.clientY - tooltipHeight - 15;
            }

            // Niet links buiten het scherm
            if (left < padding) {
                left = padding;
            }

            // Niet boven buiten het scherm
            if (top < padding) {
                top = padding;
            }

            tooltip.style.left = left + "px";
            tooltip.style.top = top + "px";
        });

        cell.addEventListener("mouseleave", function () {
            tooltip.classList.add("hidden");
        });
    });
}


// FUNCTIE OPSLAAN IN MYSQL
function saveFunctionInGrid(cell, originalItem) {
    const cellId = cell.dataset.id;
    const functionId = originalItem.dataset.functionId;

    if (!cellId || !functionId) {
        console.error("cell_id of function_id ontbreekt");
        return;
    }

    fetch('/grid/assign-function', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cell_id: cellId,
            function_id: functionId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Saved:", data);

        if (data.success && data.effectTotals) {
            updateEffectTable(data.effectTotals, data.qualityOfLife);
        }
    })
    .catch(error => {
        console.error("Save error:", error);
    });
}


// EFFECT TABLE DIRECT UPDATEN
window.updateEffectTable = function (effectTotals, qualityOfLife) {
    Object.keys(effectTotals).forEach(function (category) {
        const element = document.querySelector(`[data-effect-category="${category}"]`);

        if (element) {
            element.textContent = effectTotals[category];
        }
    });

    const qualityElement = document.getElementById("qualityOfLifeValue");

    if (qualityElement) {
        qualityElement.textContent = qualityOfLife;
    }
};


// EFFECTS VAN BOVEN/LINKS/RECHTS/ONDER LADEN VOOR TOOLTIP
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


// TOOLTIP EFFECTS UPDATEN
function updateTooltipEffects(effectTotals) {
    let qualityOfLife = 0;

    Object.keys(effectTotals).forEach(function (category) {
        const value = Number(effectTotals[category]);
        qualityOfLife += value;

        const element = document.querySelector(`[data-tooltip-effect-category="${category}"]`);

        if (element) {
            element.textContent = value;
        }
    });

    const qualityElement = document.getElementById("tooltipQualityOfLife");

    if (qualityElement) {
        qualityElement.textContent = qualityOfLife;
    }
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
    } else if (window.innerHeight - clientY < scrollThreshold) {
        startAutoScroll(scrollSpeed);
    } else {
        stopAutoScroll();
    }
}

document.addEventListener("dragover", function (ev) {
    checkScroll(ev.clientY);
});

document.addEventListener("touchmove", function (ev) {
    const touchY = ev.touches[0].clientY;
    checkScroll(touchY);
}, { passive: false });

document.addEventListener("dragend", stopAutoScroll);
document.addEventListener("drop", stopAutoScroll);
document.addEventListener("touchend", stopAutoScroll);