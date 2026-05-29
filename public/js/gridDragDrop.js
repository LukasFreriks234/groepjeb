document.addEventListener("DOMContentLoaded", function () {
    enableDrag();
    enableMobileDrag();
    enableTooltip();

    const gridCells = document.querySelectorAll(".gridCell");

    gridCells.forEach((cell) => {

        const initialImage = cell.querySelector(".gridImage");
        if (initialImage) {
            setCellLabel(cell, initialImage.alt, initialImage.dataset.category);
        } else {
            setCellLabel(cell, null, null);
        }

        // drag over toestaan
        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
        });

        // drop
        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const dragData = getDragData(ev);

            if (!dragData) return;

            // FUNCTIE UIT DE LIBRARY NAAR DE GRID
            if (dragData.source === "library") {
                const originalItem = document.getElementById(dragData.itemId);

                if (!originalItem) return;

                clearCell(cell);

                const newImage = createGridImage({
                    functionId: originalItem.dataset.functionId,
                    category: originalItem.dataset.category,
                    imageSrc: dragData.imageSrc,
                    imageAlt: dragData.imageAlt,
                    targetCellId: cell.dataset.id
                });

                cell.appendChild(newImage);
                setCellLabel(cell, originalItem.textContent.trim(), originalItem.dataset.category);
                markCellOccupied(cell, originalItem.dataset.category);

                saveFunctionInGrid(cell, originalItem);

                enableDrag();
                enableMobileDrag();
                refreshDeleteButtonsIfAvailable();
            }

            // FUNCTIE VAN GRID CELL NAAR ANDERE GRID CELL
            if (dragData.source === "grid") {
                const fromCell = document.querySelector(`.gridCell[data-id="${dragData.fromCellId}"]`);

                if (!fromCell) return;

                // Als je naar dezelfde cell sleept, niks doen
                if (fromCell.dataset.id === cell.dataset.id) {
                    return;
                }

                // Check of target cell al een functie heeft
                const targetImage = cell.querySelector(".gridImage");

                let targetData = null;

                if (targetImage) {
                    targetData = {
                        functionId: targetImage.dataset.functionId,
                        category: targetImage.dataset.category,
                        imageSrc: targetImage.src,
                        imageAlt: targetImage.alt
                    };
                }

                // Target cell leegmaken
                clearCell(cell);

                // Gesleepte functie in target cell zetten
                const draggedImage = createGridImage({
                    functionId: dragData.functionId,
                    category: dragData.category,
                    imageSrc: dragData.imageSrc,
                    imageAlt: dragData.imageAlt,
                    targetCellId: cell.dataset.id
                });

                cell.appendChild(draggedImage);
                setCellLabel(cell, draggedImage.alt, dragData.category);
                markCellOccupied(cell, dragData.category);

                // Oude cell leegmaken
                clearCell(fromCell);

                // Als target cell al een functie had, zet die terug in de oude cell
                if (targetData) {
                    const swappedImage = createGridImage({
                        functionId: targetData.functionId,
                        category: targetData.category,
                        imageSrc: targetData.imageSrc,
                        imageAlt: targetData.imageAlt,
                        targetCellId: fromCell.dataset.id
                    });

                    fromCell.appendChild(swappedImage);
                    markCellOccupied(fromCell, targetData.category);
                    setCellLabel(
                        fromCell,
                        targetData.imageAlt,
                        targetData.category
                    );
                } else {
                    // Als target leeg was, blijft oude cell leeg
                    markCellAvailable(fromCell);
                    setCellLabel(fromCell, null, null);
                }

                // Opslaan + effect table updaten
                moveFunctionInGrid(
                    dragData.fromCellId,
                    cell.dataset.id,
                    dragData.functionId
                );

                // Nieuwe grid images ook draggable maken
                enableDrag();
                enableMobileDrag();
                refreshDeleteButtonsIfAvailable();
            }
        });
    });
});


// DRAG DATA LEZEN
function getDragData(ev) {
    const rawData = ev.dataTransfer.getData("text/plain");

    if (!rawData) return null;

    try {
        return JSON.parse(rawData);
    } catch (error) {
        // fallback voor oude code: alleen item id
        return {
            source: "library",
            itemId: rawData
        };
    }
}


// CELL LEEGMAKEN
function clearCell(cell) {
    const existingItems = cell.querySelectorAll(".functionItem, .gridImage");

    existingItems.forEach((item) => {
        item.remove();
    });
}


// CELL OP BEZET ZETTEN
function markCellOccupied(cell, category) {
    cell.classList.remove("available");
    cell.classList.add("occupied");

    if (category) {
        cell.dataset.category = category;
    }
}


// CELL OP BESCHIKBAAR ZETTEN
function markCellAvailable(cell) {
    cell.classList.remove("occupied");
    cell.classList.add("available");

    delete cell.dataset.category;
}


// DELETE BUTTONS OPNIEUW CHECKEN
function refreshDeleteButtonsIfAvailable() {
    if (typeof window.refreshDeleteButtons === "function") {
        window.refreshDeleteButtons();
    }
}


// GRID IMAGE MAKEN
function createGridImage(data) {
    const image = document.createElement("img");

    image.src = data.imageSrc;
    image.alt = data.imageAlt || "Function image";
    image.classList.add("gridImage", "draggableGridFunction");
    image.setAttribute("draggable", "true");

    image.dataset.functionId = data.functionId;
    image.dataset.fromCellId = data.targetCellId;
    image.dataset.category = data.category;

    return image;
}


// DRAG FUNCTIE
function enableDrag() {
    const functionItems = document.querySelectorAll(".functionItem");

    functionItems.forEach((item) => {
        item.setAttribute("draggable", "true");

        // voorkomt dubbele event listeners
        if (item.dataset.dragEnabled === "true") return;
        item.dataset.dragEnabled = "true";

        item.addEventListener("dragstart", function (ev) {
            const image = ev.currentTarget.querySelector("img");

            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    source: "library",
                    itemId: ev.currentTarget.id,
                    functionId: ev.currentTarget.dataset.functionId,
                    category: ev.currentTarget.dataset.category,
                    imageSrc: image ? image.src : "",
                    imageAlt: image ? image.alt : ev.currentTarget.textContent.trim()
                })
            );
        });
    });

    const gridImages = document.querySelectorAll(".draggableGridFunction");

    gridImages.forEach((image) => {
        image.setAttribute("draggable", "true");

        // voorkomt dubbele event listeners
        if (image.dataset.dragEnabled === "true") return;
        image.dataset.dragEnabled = "true";

        image.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    source: "grid",
                    functionId: ev.currentTarget.dataset.functionId,
                    fromCellId: ev.currentTarget.dataset.fromCellId,
                    category: ev.currentTarget.dataset.category,
                    imageSrc: ev.currentTarget.src,
                    imageAlt: ev.currentTarget.alt
                })
            );
        });
    });
}


// MOBIEL TOEGEVOEGD
let selectedMobileElement = null;
let selectedMobileData = null;
let mobileDragEnabled = false;
let tooltipHideTimeout = null;

function enableMobileDrag() {
    if (mobileDragEnabled) return;
    mobileDragEnabled = true;

    document.addEventListener("touchstart", function (ev) {
        const touchedCell = ev.target.closest(".gridCell");
        let touchedFunction = ev.target.closest(".functionItem, .draggableGridFunction");

        // MOBIEL: als je op een bezette grid cell tikt, selecteer de image in die cell
        // Hierdoor hoef je niet precies op het plaatje te klikken.
        if (!touchedFunction && touchedCell) {
            touchedFunction = touchedCell.querySelector(".draggableGridFunction");
        }

        // MOBIEL: effects tonen als je op een grid cell tikt
        if (touchedCell) {
            const touch = ev.touches[0];

            if (touch) {
                showEffectsTooltip(touchedCell, touch.clientX, touch.clientY, true);
            }
        }

        // Als er al iets geselecteerd is en je tikt op een grid cell,
        // dan moet hij plaatsen of swappen.
        // Dit moet vóór touchedFunction staan, anders selecteert hij de target functie opnieuw.
        if (selectedMobileData && touchedCell) {
            ev.preventDefault();
            ev.stopPropagation();

            if (
                selectedMobileData.source === "grid" &&
                selectedMobileData.fromCellId === touchedCell.dataset.id
            ) {
                clearMobileSelection();
                return;
            }

            placeSelectedMobileFunction(touchedCell);
            return;
        }

        // Als er nog niks geselecteerd is, selecteer je een function item.
        if (touchedFunction) {
            ev.preventDefault();
            ev.stopPropagation();

            selectMobileFunction(touchedFunction);
            return;
        }
    }, { passive: false });
}


// MOBIEL ITEM SELECTEREN
function selectMobileFunction(item) {
    clearMobileSelection();

    selectedMobileElement = item;
    item.classList.add("selectedMobileCell");

    const parentCell = item.closest(".gridCell");

    if (parentCell) {
        parentCell.classList.add("selectedMobileCell");
    }

    if (item.classList.contains("draggableGridFunction")) {
        selectedMobileData = {
            source: "grid",
            functionId: item.dataset.functionId,
            fromCellId: item.dataset.fromCellId,
            category: item.dataset.category,
            imageSrc: item.src,
            imageAlt: item.alt
        };
    } else {
        const image = item.querySelector("img");

        selectedMobileData = {
            source: "library",
            itemId: item.id,
            functionId: item.dataset.functionId,
            category: item.dataset.category,
            imageSrc: image ? image.src : "",
            imageAlt: image ? image.alt : item.textContent.trim()
        };
    }

    // Mobiel: als je een grid-cell selecteert, maak/check direct de delete-knop
    // Dit is nodig omdat touchstart door gridDragDrop wordt gestopt met stopPropagation.
    refreshDeleteButtonsIfAvailable();
}


// MOBIELE SELECTIE WEGHALEN
function clearMobileSelection() {
    document
        .querySelectorAll(".functionItem, .draggableGridFunction, .gridCell")
        .forEach((item) => {
            item.classList.remove("selectedMobileCell");
        });

    selectedMobileElement = null;
    selectedMobileData = null;
}


// MOBIEL ITEM IN GRID PLAATSEN
function placeSelectedMobileFunction(cell) {
    if (!selectedMobileData || !selectedMobileElement) return;

    // MOBIEL: LIBRARY NAAR GRID
    if (selectedMobileData.source === "library") {
        clearCell(cell);

        const newImage = createGridImage({
            functionId: selectedMobileData.functionId,
            category: selectedMobileData.category,
            imageSrc: selectedMobileData.imageSrc,
            imageAlt: selectedMobileData.imageAlt,
            targetCellId: cell.dataset.id
        });

        cell.appendChild(newImage);

        markCellOccupied(cell, selectedMobileData.category);

        saveFunctionInGrid(cell, selectedMobileElement);

        clearMobileSelection();

        enableDrag();
        refreshDeleteButtonsIfAvailable();
        return;
    }

    // MOBIEL: GRID NAAR GRID
    if (selectedMobileData.source === "grid") {
        const fromCell = document.querySelector(`.gridCell[data-id="${selectedMobileData.fromCellId}"]`);

        if (!fromCell) {
            clearMobileSelection();
            return;
        }

        if (fromCell.dataset.id === cell.dataset.id) {
            clearMobileSelection();
            return;
        }

        const targetImage = cell.querySelector(".gridImage");

        let targetData = null;

        if (targetImage) {
            targetData = {
                functionId: targetImage.dataset.functionId,
                category: targetImage.dataset.category,
                imageSrc: targetImage.src,
                imageAlt: targetImage.alt
            };
        }

        clearCell(cell);

        const draggedImage = createGridImage({
            functionId: selectedMobileData.functionId,
            category: selectedMobileData.category,
            imageSrc: selectedMobileData.imageSrc,
            imageAlt: selectedMobileData.imageAlt,
            targetCellId: cell.dataset.id
        });

        cell.appendChild(draggedImage);
        markCellOccupied(cell, selectedMobileData.category);

        clearCell(fromCell);

        if (targetData) {
            const swappedImage = createGridImage({
                functionId: targetData.functionId,
                category: targetData.category,
                imageSrc: targetData.imageSrc,
                imageAlt: targetData.imageAlt,
                targetCellId: fromCell.dataset.id
            });

            fromCell.appendChild(swappedImage);
            markCellOccupied(fromCell, targetData.category);
        } else {
            markCellAvailable(fromCell);
        }

        moveFunctionInGrid(
            selectedMobileData.fromCellId,
            cell.dataset.id,
            selectedMobileData.functionId
        );

        clearMobileSelection();

        enableDrag();
        refreshDeleteButtonsIfAvailable();
    }
}


// TOOLTIP TONEN
function showEffectsTooltip(cell, clientX, clientY, autoHide = false) {
    const tooltip = document.getElementById("functionTooltip");

    if (!tooltip) return;

    loadNeighborEffects(cell);

    tooltip.classList.remove("hidden");

    const padding = 10;
    const tooltipWidth = tooltip.offsetWidth;
    const tooltipHeight = tooltip.offsetHeight;

    let left = clientX + 15;
    let top = clientY + 15;

    if (left + tooltipWidth > window.innerWidth - padding) {
        left = clientX - tooltipWidth - 15;
    }

    if (top + tooltipHeight > window.innerHeight - padding) {
        top = clientY - tooltipHeight - 15;
    }

    if (left < padding) {
        left = padding;
    }

    if (top < padding) {
        top = padding;
    }

    tooltip.style.left = left + "px";
    tooltip.style.top = top + "px";

    if (autoHide) {
        clearTimeout(tooltipHideTimeout);

        tooltipHideTimeout = setTimeout(() => {
            tooltip.classList.add("hidden");
        }, 2500);
    }
}


// TOOLTIP
function enableTooltip() {
    const tooltip = document.getElementById("functionTooltip");
    const gridCells = document.querySelectorAll(".gridCell");

    gridCells.forEach((cell) => {
        // PC: hover
        cell.addEventListener("mousemove", function (ev) {
            showEffectsTooltip(cell, ev.clientX, ev.clientY);
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
            refreshDeleteButtonsIfAvailable();
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


// FUNCTIE VERPLAATSEN IN MYSQL
function moveFunctionInGrid(fromCellId, toCellId, functionId) {
    if (!fromCellId || !toCellId || !functionId) {
        console.error("from_cell_id, to_cell_id of function_id ontbreekt");
        return;
    }

    fetch('/grid/move-function', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            from_cell_id: fromCellId,
            to_cell_id: toCellId,
            function_id: functionId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Moved:", data);

        if (data.success && data.effectTotals) {
            updateEffectTable(data.effectTotals, data.qualityOfLife);
            refreshDeleteButtonsIfAvailable();
        }
    })
    .catch(error => {
        console.error("Move error:", error);
    });
}


// EFFECT TABLE DIRECT UPDATEN
window.updateEffectTable = function (effectTotals, qualityOfLife) {
    Object.keys(effectTotals).forEach(function (category) {
        const element = document.querySelector(`[data-effect-category="${category}"]`);

        if (element) {
            const value = Number(effectTotals[category]);

            element.innerHTML = `
                <span class="${getEffectClass(value)}">
                    ${value}
                </span>
            `;
        }
    });

    const qualityElement = document.getElementById("qualityOfLifeValue");

    if (qualityElement) {
        const total = Number(qualityOfLife);

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
function updateTooltipEffects(effectTotals, qualityOfLife = null) {
    let calculatedQualityOfLife = 0;

    Object.keys(effectTotals).forEach(function (category) {
        const value = Number(effectTotals[category]);
        calculatedQualityOfLife += value;

        const element = document.querySelector(`[data-tooltip-effect-category="${category}"]`);

        if (element) {
            element.innerHTML = `
                <span class="${getEffectClass(value)}">
                    ${value}
                </span>
            `;
        }
    });

    const qualityElement = document.getElementById("tooltipQualityOfLife");

    if (qualityElement) {
        const total = qualityOfLife ?? calculatedQualityOfLife;

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

function setCellLabel(cell, name = null, category = null) {
    const x = cell.dataset.x;
    const y = cell.dataset.y;
    let baseLabel = `Cel row ${y}, column ${x}.`;

    if (name) {
        let cleanName = name.trim();

        if (category) {
            const regex = new RegExp(`\\(?\\s*${category}\\s*\\)?`, "gi");
            cleanName = cleanName.replace(regex, "").trim();
        }

        if (category) {
            cell.setAttribute("aria-label", `${baseLabel} Occupied by ${cleanName} (${category})`);
        } else {
            cell.setAttribute("aria-label", `${baseLabel} Occupied by ${cleanName}`);
        }
    } else {
        cell.setAttribute("aria-label", `${baseLabel} Available.`);
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