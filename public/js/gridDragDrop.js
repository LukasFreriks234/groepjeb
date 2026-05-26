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

                markCellOccupied(cell, originalItem.dataset.category);

                saveFunctionInGrid(cell, originalItem);

                enableDrag();
                enableMobileDrag();
            }

            // FUNCTIE VAN GRID CELL NAAR ANDERE GRID CELL
            if (dragData.source === "grid") {
                const fromCell = document.querySelector(`.gridCell[data-id="${dragData.fromCellId}"]`);

                if (!fromCell) return;

                if (fromCell.dataset.id === cell.dataset.id) {
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
                    functionId: dragData.functionId,
                    category: dragData.category,
                    imageSrc: dragData.imageSrc,
                    imageAlt: dragData.imageAlt,
                    targetCellId: cell.dataset.id
                });

                cell.appendChild(draggedImage);
                markCellOccupied(cell, dragData.category);

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
                    dragData.fromCellId,
                    cell.dataset.id,
                    dragData.functionId
                );

                enableDrag();
                enableMobileDrag();
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
let mobileDragDocumentListenersAdded = false;
let activeItem = null;
let activeDragData = null;
let touchTimeout = null;
let lastTouchX = 0;
let lastTouchY = 0;

function enableMobileDrag() {
    const draggableItems = document.querySelectorAll(".functionItem, .draggableGridFunction");

    draggableItems.forEach((item) => {
        if (item.dataset.mobileDragEnabled === "true") return;
        item.dataset.mobileDragEnabled = "true";

        item.addEventListener("touchstart", function (ev) {
            activeItem = item;

            const touch = ev.touches[0];
            lastTouchX = touch.clientX;
            lastTouchY = touch.clientY;

            if (item.classList.contains("draggableGridFunction")) {
                activeDragData = {
                    source: "grid",
                    functionId: item.dataset.functionId,
                    fromCellId: item.dataset.fromCellId,
                    category: item.dataset.category,
                    imageSrc: item.src,
                    imageAlt: item.alt
                };
            } else {
                const image = item.querySelector("img");

                activeDragData = {
                    source: "library",
                    itemId: item.id,
                    functionId: item.dataset.functionId,
                    category: item.dataset.category,
                    imageSrc: image ? image.src : "",
                    imageAlt: image ? image.alt : item.textContent.trim()
                };
            }

            touchTimeout = setTimeout(() => {
                if (activeItem) {
                    activeItem.style.opacity = "0.5";
                }
            }, 200);

        }, { passive: true });
    });

    if (mobileDragDocumentListenersAdded) return;
    mobileDragDocumentListenersAdded = true;

    document.addEventListener("touchmove", function (ev) {
        const touch = ev.touches[0];
        lastTouchX = touch.clientX;
        lastTouchY = touch.clientY;

        if (touchTimeout && activeItem && activeItem.style.opacity !== "0.5") {
            clearTimeout(touchTimeout);
            activeItem = null;
            activeDragData = null;
        }

        if (activeItem && activeItem.style.opacity === "0.5") {
            ev.preventDefault();
        }
    }, { passive: false });

    document.addEventListener("touchend", function () {
        clearTimeout(touchTimeout);

        if (!activeItem || activeItem.style.opacity !== "0.5" || !activeDragData) {
            if (activeItem) activeItem.style.opacity = "1";
            activeItem = null;
            activeDragData = null;
            return;
        }

        activeItem.style.opacity = "1";

        const element = document.elementFromPoint(lastTouchX, lastTouchY);
        const cell = element ? element.closest(".gridCell") : null;

        if (cell) {

            // MOBIEL: LIBRARY NAAR GRID
            if (activeDragData.source === "library") {
                clearCell(cell);

                const newImage = createGridImage({
                    functionId: activeDragData.functionId,
                    category: activeDragData.category,
                    imageSrc: activeDragData.imageSrc,
                    imageAlt: activeDragData.imageAlt,
                    targetCellId: cell.dataset.id
                });

                cell.appendChild(newImage);

                markCellOccupied(cell, activeDragData.category);

                saveFunctionInGrid(cell, activeItem);

                enableDrag();
                enableMobileDrag();
            }

            // MOBIEL: GRID NAAR GRID
            if (activeDragData.source === "grid") {
                const fromCell = document.querySelector(`.gridCell[data-id="${activeDragData.fromCellId}"]`);

                if (fromCell && fromCell.dataset.id !== cell.dataset.id) {
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
                        functionId: activeDragData.functionId,
                        category: activeDragData.category,
                        imageSrc: activeDragData.imageSrc,
                        imageAlt: activeDragData.imageAlt,
                        targetCellId: cell.dataset.id
                    });

                    cell.appendChild(draggedImage);
                    markCellOccupied(cell, activeDragData.category);

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
                        activeDragData.fromCellId,
                        cell.dataset.id,
                        activeDragData.functionId
                    );

                    enableDrag();
                    enableMobileDrag();
                }
            }
        }

        activeItem = null;
        activeDragData = null;
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

            if (left + tooltipWidth > window.innerWidth - padding) {
                left = ev.clientX - tooltipWidth - 15;
            }

            if (top + tooltipHeight > window.innerHeight - padding) {
                top = ev.clientY - tooltipHeight - 15;
            }

            if (left < padding) {
                left = padding;
            }

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