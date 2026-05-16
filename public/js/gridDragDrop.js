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

            // opslaan + effect table updaten
            saveFunctionInGrid(cell, activeItem);
        }

        activeItem = null;
    }, { passive: false });
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
        console.error("Error:", error);
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
    
    const activeItem = document.querySelector('.functionItem[style*="opacity"]'); 
    checkScroll(touchY);
    
}, { passive: false });

document.addEventListener("dragend", stopAutoScroll);
document.addEventListener("drop", stopAutoScroll);
document.addEventListener("touchend", stopAutoScroll);