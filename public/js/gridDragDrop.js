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
            clonedItem.classList.remove("selectedMobileCell");

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

    // ALLEEN MOBIEL
    if (window.innerWidth > 768) {
        return;
    }

    const functionItems =
        document.querySelectorAll(
            ".functionItem"
        );

    const gridCells =
        document.querySelectorAll(
            ".gridCell"
        );

    let selectedItem = null;


    // ITEM SELECTEREN
    functionItems.forEach((item) => {

        item.addEventListener(

            "touchstart",

            function (ev) {

                ev.preventDefault();
                ev.stopPropagation();

                // oude selectie verwijderen
                document
                    .querySelectorAll(
                        ".functionItem"
                    )
                    .forEach((el) => {

                        el.classList.remove(
                            "selectedMobileCell"
                        );

                    });

                // huidige geel maken
                ev.currentTarget.classList.add(
                    "selectedMobileCell"
                );

                // geselecteerde item opslaan
                selectedItem =
                    ev.currentTarget;

            },

            { passive: false }

        );

    });


    // ITEM IN GRID PLAATSEN
    gridCells.forEach((cell) => {

        cell.addEventListener(

            "touchstart",

            function (ev) {

                ev.preventDefault();
                ev.stopPropagation();

                if (!selectedItem) return;

                // oude inhoud verwijderen
                const existingItem =
                    cell.querySelector(
                        ".functionItem"
                    );

                const existingImage =
                    cell.querySelector(
                        ".gridImage"
                    );

                if (existingItem)
                    existingItem.remove();

                if (existingImage)
                    existingImage.remove();

                // clone maken
                const clonedItem =
                    selectedItem.cloneNode(
                        true
                    );

                // geel uit grid halen
                clonedItem.classList.remove(
                    "selectedMobileCell"
                );

                clonedItem.removeAttribute(
                    "id"
                );

                clonedItem.setAttribute(
                    "draggable",
                    "false"
                );

                // category opslaan
                cell.dataset.category =
                    selectedItem.dataset.category;

                // item in grid zetten
                cell.appendChild(
                    clonedItem
                );

                // cell styling
                cell.classList.remove(
                    "available"
                );

                cell.classList.add(
                    "occupied"
                );

                // save in database
                saveFunctionInGrid(
                    cell,
                    selectedItem
                );

                // GEEL WEGHALEN UIT LIJST
                selectedItem.classList.remove(
                    "selectedMobileCell"
                );

                // GEEN SELECTIE MEER
                selectedItem = null;

            },

            { passive: false }

        );

    });

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

// KLEUR EFFECTEN
function getEffectClass(value) {
    if (value > 0) {
        return "positiveEffect";
    }
    if (value < 0) {
        return "negativeEffect";
    }
    return "neutralEffect";
}

// EFFECT TABLE DIRECT UPDATEN
window.updateEffectTable = function (
    effectTotals,
    qualityOfLife
) {
    Object.keys(effectTotals).forEach(
        function (category) {
            const element =
                document.querySelector(
                    `[data-effect-category="${category}"]`
                );
            if (element) {
                const value =
                    Number(
                        effectTotals[category]
                    );
                element.innerHTML = `
                    <span class="${getEffectClass(value)}">
                        ${value}
                    </span>
                `;
            }
        }
    );
    const qualityElement =
        document.getElementById(
            "qualityOfLifeValue"
        );
    if (qualityElement) {
        const total = qualityOfLife;
        qualityElement.innerHTML = `
            <span class="${getEffectClass(total)}">
                ${total}
            </span>
        `;
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
            updateTooltipEffects(data.effectTotals, data.qualityOfLife);
        }
    })
    .catch(error => {
        console.error("Neighbor effects error:", error);
    });
}


// TOOLTIP EFFECTS UPDATEN
function updateTooltipEffects(
    effectTotals,
    qualityOfLife = null
) {
    let calculatedQualityOfLife = 0;
    Object.keys(effectTotals).forEach(
        function (category) {
            const value =
                Number(
                    effectTotals[category]
                );
            calculatedQualityOfLife += value;
            const element =
                document.querySelector(
                    `[data-tooltip-effect-category="${category}"]`
                );
            if (element) {
                element.innerHTML = `
                    <span class="${getEffectClass(value)}">
                        ${value}
                    </span>
                `;
            }
        }
    );
    const qualityElement =
        document.getElementById(
            "tooltipQualityOfLife"
        );
    if (qualityElement) {
        const total =
            qualityOfLife ??
            calculatedQualityOfLife;
        qualityElement.innerHTML = `
            <span class="${getEffectClass(total)}">
                ${total}
            </span>
        `;
    }
}


// AUTO SCROLL
let scrollInterval = null;

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