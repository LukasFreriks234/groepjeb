document.addEventListener("DOMContentLoaded", function () {
    enableDrag();
    enableMobileDrag();
    enableKeyboardDragDrop();
    enableArrowKeyGridNavigation();
    enableTooltip();
    updateEffectsAccessibilityLabelForReader();

    document.querySelectorAll(".gridCell").forEach((cell) => {
        const initialImage = cell.querySelector(".gridImage");

        if (initialImage) {
            setCellLabel(cell, getGridImageName(initialImage), initialImage.dataset.category);
        } else {
            setCellLabel(cell, null, null);
        }

        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
        });

        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const dragData = getDragData(ev);

            if (!dragData) return;

            if (dragData.source === "library") {
                const originalItem = document.getElementById(dragData.itemId);

                if (!originalItem) return;

                placeLibraryFunctionInCell(cell, originalItem, dragData);
                return;
            }

            if (dragData.source === "grid") {
                placeGridFunctionInCell(cell, dragData);
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
    const existingItems = cell.querySelectorAll(".functionItem, .gridImage, .delete-btn");

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
    image.alt = "";
    image.setAttribute("aria-hidden", "true");
    image.setAttribute("tabindex", "-1");
    image.classList.add("gridImage", "draggableGridFunction");
    image.setAttribute("draggable", "true");

    image.dataset.functionId = data.functionId;
    image.dataset.fromCellId = data.targetCellId;
    image.dataset.category = data.category;
    image.dataset.functionName = data.imageAlt || "Function";

    return image;
}


// NAAM UIT GRID IMAGE HALEN
function getGridImageName(image) {
    return image.dataset.functionName || image.alt || "Function";
}


// FUNCTIE NAAM UIT FUNCTION TABLE HALEN
function getFunctionNameFromItem(item) {
    const functionNameElement = item.querySelector(".functionName");

    if (functionNameElement) {
        return functionNameElement.textContent.trim();
    }

    const image = item.querySelector("img");

    if (image && image.alt) {
        return image.alt.trim();
    }

    return item.textContent.trim();
}


// DRAG FUNCTIE VOOR MUIS
function enableDrag() {
    const functionItems = document.querySelectorAll("#functionsList .functionItem");

    functionItems.forEach((item) => {
        item.setAttribute("draggable", "true");

        if (item.dataset.dragEnabled === "true") return;
        item.dataset.dragEnabled = "true";

        item.addEventListener("dragstart", function (ev) {
            const image = ev.currentTarget.querySelector("img");
            const functionName = getFunctionNameFromItem(ev.currentTarget);

            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    source: "library",
                    itemId: ev.currentTarget.id,
                    functionId: ev.currentTarget.dataset.functionId,
                    category: ev.currentTarget.dataset.category,
                    imageSrc: image ? image.src : "",
                    imageAlt: functionName
                })
            );
        });
    });

    const gridImages = document.querySelectorAll(".draggableGridFunction");

    gridImages.forEach((image) => {
        image.setAttribute("draggable", "true");
        image.setAttribute("tabindex", "-1");
        image.setAttribute("aria-hidden", "true");

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
                    imageAlt: getGridImageName(ev.currentTarget)
                })
            );
        });
    });
}


// LIBRARY FUNCTIE IN CELL PLAATSEN
function placeLibraryFunctionInCell(cell, originalItem, dragData) {
    const functionName = dragData.imageAlt || getFunctionNameFromItem(originalItem);

    clearCell(cell);

    const newImage = createGridImage({
        functionId: originalItem.dataset.functionId || dragData.functionId,
        category: originalItem.dataset.category || dragData.category,
        imageSrc: dragData.imageSrc,
        imageAlt: functionName,
        targetCellId: cell.dataset.id
    });

    cell.appendChild(newImage);

    markCellOccupied(cell, originalItem.dataset.category || dragData.category);
    setCellLabel(cell, functionName, originalItem.dataset.category || dragData.category);

    saveFunctionInGrid(cell, originalItem);

    enableDrag();
    enableMobileDrag();
    refreshDeleteButtonsIfAvailable();

    const position = getReadableCellPosition(cell);

    setTimeout(() => {
        updateEffectsAccessibilityLabelForReader();
    }, 500);

    cell.focus();
}


// GRID FUNCTIE IN CELL PLAATSEN OF WISSELEN
function placeGridFunctionInCell(cell, dragData) {
    const fromCell = document.querySelector(`.gridCell[data-id="${dragData.fromCellId}"]`);

    if (!fromCell) return;

    if (fromCell.dataset.id === cell.dataset.id) {
        announceKeyboardStatus("Selection cancelled.");
        return;
    }

    const targetImage = cell.querySelector(".gridImage");

    let targetData = null;

    if (targetImage) {
        targetData = {
            functionId: targetImage.dataset.functionId,
            category: targetImage.dataset.category,
            imageSrc: targetImage.src,
            imageAlt: getGridImageName(targetImage)
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
    setCellLabel(cell, dragData.imageAlt, dragData.category);

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
        setCellLabel(fromCell, targetData.imageAlt, targetData.category);
    } else {
        markCellAvailable(fromCell);
        setCellLabel(fromCell, null, null);
    }

    moveFunctionInGrid(
        dragData.fromCellId,
        cell.dataset.id,
        dragData.functionId
    );

    enableDrag();
    enableMobileDrag();
    refreshDeleteButtonsIfAvailable();

    const position = getReadableCellPosition(cell);

    setTimeout(() => {
        updateEffectsAccessibilityLabelForReader();
    }, 500);

    cell.focus();
}


// MOBIEL
let selectedMobileElement = null;
let selectedMobileData = null;
let mobileDragEnabled = false;
let tooltipHideTimeout = null;

window.addEventListener("resize", function () {

    if (window.innerWidth > 768) {

        document
            .querySelectorAll(
                ".selectedMobileCell, .keyboardSelected"
            )
            .forEach((item) => {
                item.classList.remove("selectedMobileCell");
                item.classList.remove("keyboardSelected");
                item.setAttribute("aria-selected", "false");
            });

        selectedMobileElement = null;
        selectedMobileData = null;
        selectedKeyboardElement = null;
        selectedKeyboardData = null;
    }
});

function enableMobileDrag() {
    if (mobileDragEnabled) return;
    mobileDragEnabled = true;

    document.addEventListener("touchstart", function (ev) {
        const touchedCell = ev.target.closest(".gridCell");
        let touchedFunction = ev.target.closest("#functionsList .functionItem, .draggableGridFunction");

        if (!touchedFunction && touchedCell) {
            touchedFunction = touchedCell.querySelector(".draggableGridFunction");
        }

        if (touchedCell) {
            const touch = ev.touches[0];

            if (touch) {
                showEffectsTooltip(touchedCell, touch.clientX, touch.clientY, true);
            }
        }

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

        if (touchedFunction) {
            ev.preventDefault();
            ev.stopPropagation();

            selectMobileFunction(touchedFunction);
        }
    }, { passive: false });
}


// MOBIEL ITEM SELECTEREN
function selectMobileFunction(item) {
    clearMobileSelection();

    selectedMobileElement = item;

    const parentCell = item.closest(".gridCell");

    if (parentCell) {
        parentCell.classList.add("selectedMobileCell");
        parentCell.setAttribute("aria-selected", "true");
    } else {
        item.classList.add("selectedMobileCell");
        item.setAttribute("aria-selected", "true");
    }

    if (item.classList.contains("draggableGridFunction")) {
        selectedMobileData = {
            source: "grid",
            functionId: item.dataset.functionId,
            fromCellId: item.dataset.fromCellId,
            category: item.dataset.category,
            imageSrc: item.src,
            imageAlt: getGridImageName(item)
        };
    } else {
        const image = item.querySelector("img");
        const functionName = getFunctionNameFromItem(item);

        selectedMobileData = {
            source: "library",
            itemId: item.id,
            functionId: item.dataset.functionId,
            category: item.dataset.category,
            imageSrc: image ? image.src : "",
            imageAlt: functionName
        };
    }

    refreshDeleteButtonsIfAvailable();
}

// MOBIELE SELECTIE WEGHALEN
function clearMobileSelection() {
    document
        .querySelectorAll("#functionsList .functionItem, .draggableGridFunction, .gridCell")
        .forEach((item) => {
            item.classList.remove("selectedMobileCell");
            item.classList.remove("keyboardSelected");
            item.setAttribute("aria-selected", "false");
        });

    selectedMobileElement = null;
    selectedMobileData = null;
}


// MOBIEL ITEM IN GRID PLAATSEN
function placeSelectedMobileFunction(cell) {
    if (!selectedMobileData || !selectedMobileElement) return;

    if (selectedMobileData.source === "library") {
        placeLibraryFunctionInCell(cell, selectedMobileElement, selectedMobileData);
        clearMobileSelection();
        return;
    }

    if (selectedMobileData.source === "grid") {
        placeGridFunctionInCell(cell, selectedMobileData);
        clearMobileSelection();
    }
}


// KEYBOARD DRAG AND DROP
let selectedKeyboardElement = null;
let selectedKeyboardData = null;

function enableKeyboardDragDrop() {
    document.addEventListener("keydown", function (ev) {
        const isEnter = ev.key === "Enter";
        const isSpace = ev.key === " " || ev.key === "Spacebar" || ev.code === "Space";

        if (!isEnter && !isSpace) {
            return;
        }

        if (ev.target.closest(".delete-btn")) {
            return;
        }

        const functionItem = ev.target.closest("#functionsList .functionItem");
        const gridCell = ev.target.closest(".gridCell");

        if (!functionItem && !gridCell) {
            return;
        }

        ev.preventDefault();

        if (selectedKeyboardData && gridCell) {
            if (
                selectedKeyboardData.source === "grid" &&
                selectedKeyboardData.fromCellId === gridCell.dataset.id
            ) {
                clearKeyboardSelection();
                announceKeyboardStatus("Selection cancelled.");
                return;
            }

            placeSelectedKeyboardFunction(gridCell);
            return;
        }

        if (functionItem) {
            selectKeyboardFunction(functionItem);
            return;
        }

        if (gridCell) {
            const imageInCell = gridCell.querySelector(".draggableGridFunction");

            if (imageInCell) {
                selectKeyboardFunction(imageInCell);
            } else {
                announceKeyboardStatus("Empty grid cell. Select a function first, then press Enter or Space here to place it.");
            }
        }
    });
}


// KEYBOARD FUNCTIE SELECTEREN
function selectKeyboardFunction(item) {
    clearKeyboardSelection();

    selectedKeyboardElement = item;

    const parentCell = item.closest(".gridCell");

    // GEEN selectedMobileCell meer voor desktop/toetsenbord
    if (parentCell) {
        parentCell.classList.add("keyboardSelected");
    } else {
        item.classList.add("keyboardSelected");
    }

    if (item.classList.contains("draggableGridFunction")) {
        selectedKeyboardData = {
            source: "grid",
            functionId: item.dataset.functionId,
            fromCellId: item.dataset.fromCellId,
            category: item.dataset.category,
            imageSrc: item.src,
            imageAlt: getGridImageName(item)
        };

        announceKeyboardStatus(
            `${getGridImageName(item)} selected. Move to another grid cell with Tab, Shift Tab, or arrow keys. Press Enter or Space to move or swap it.`
        );
    } else {
        const image = item.querySelector("img");
        const functionName = getFunctionNameFromItem(item);

        selectedKeyboardData = {
            source: "library",
            itemId: item.id,
            functionId: item.dataset.functionId,
            category: item.dataset.category,
            imageSrc: image ? image.src : "",
            imageAlt: functionName
        };

        announceKeyboardStatus(
            `${functionName} selected. Move to a grid cell and press Enter or Space to place it.`
        );
    }

    refreshDeleteButtonsIfAvailable();
}


// KEYBOARD SELECTIE WEGHALEN
function clearKeyboardSelection() {
    document
        .querySelectorAll("#functionsList .functionItem, .draggableGridFunction, .gridCell")
        .forEach((item) => {
            item.classList.remove("selectedMobileCell");
            item.classList.remove("keyboardSelected");
        });

    selectedKeyboardElement = null;
    selectedKeyboardData = null;
}


// KEYBOARD ITEM IN GRID PLAATSEN
function placeSelectedKeyboardFunction(cell) {
    if (!selectedKeyboardData || !selectedKeyboardElement) return;

    if (selectedKeyboardData.source === "library") {
        placeLibraryFunctionInCell(cell, selectedKeyboardElement, selectedKeyboardData);
        clearKeyboardSelection();
        return;
    }

    if (selectedKeyboardData.source === "grid") {
        placeGridFunctionInCell(cell, selectedKeyboardData);
        clearKeyboardSelection();
    }
}


// PIJLTJES NAVIGATIE ALLEEN IN DE GRID
function enableArrowKeyGridNavigation() {
    document.addEventListener("keydown", function (ev) {
        const currentCell = ev.target.closest(".gridCell");

        if (!currentCell) return;

        const arrowKeys = ["ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight"];

        if (!arrowKeys.includes(ev.key)) {
            return;
        }

        ev.preventDefault();

        const currentX = Number(currentCell.dataset.x);
        const currentY = Number(currentCell.dataset.y);

        let targetX = currentX;
        let targetY = currentY;

        if (ev.key === "ArrowUp") {
            targetY = currentY - 1;
        }

        if (ev.key === "ArrowDown") {
            targetY = currentY + 1;
        }

        if (ev.key === "ArrowLeft") {
            targetX = currentX - 1;
        }

        if (ev.key === "ArrowRight") {
            targetX = currentX + 1;
        }

        const targetCell = document.querySelector(
            `.gridCell[data-x="${targetX}"][data-y="${targetY}"]`
        );

        if (targetCell) {
            targetCell.focus();
            announceCurrentGridCell(targetCell);
        }
    });
}


// LEESBARE GRID POSITIE
function getReadableCellPosition(cell) {
    return {
        row: Number(cell.dataset.y) + 1,
        column: Number(cell.dataset.x) + 1
    };
}


// ZEGT WAAR JE IN DE GRID ZIT
function announceCurrentGridCell(cell) {
    const image = cell.querySelector(".gridImage");
    const position = getReadableCellPosition(cell);

    if (image) {
        announceKeyboardStatus(
            `Grid cell row ${position.row}, column ${position.column}. Contains ${getGridImageName(image)}. Press Enter or Space to select this function to move it. Use the remove button to remove it.`
        );
    } else {
        announceKeyboardStatus(
            `Grid cell row ${position.row}, column ${position.column}. Empty cell. Press Enter or Space to place a selected function here.`
        );
    }
}


// CELL LABEL VOOR SCREENREADERS
function setCellLabel(cell, name = null, category = null) {
    const position = getReadableCellPosition(cell);
    const baseLabel = `Grid cell row ${position.row}, column ${position.column}.`;

    if (name) {
        let cleanName = name.trim();

        if (category) {
            const regex = new RegExp(`\\(?\\s*${category}\\s*\\)?`, "gi");
            cleanName = cleanName.replace(regex, "").trim();
        }

        cell.setAttribute(
            "aria-label",
            `${baseLabel} Contains ${cleanName}. Press Enter or Space to select this function to move it. Use the remove button to remove it.`
        );
    } else {
        cell.setAttribute(
            "aria-label",
            `${baseLabel} Empty cell. Press Enter or Space to place a selected function here.`
        );
    }
}


// TOOLTIP TONEN
function showEffectsTooltip(cell, clientX, clientY, autoHide = false) {
    const tooltip = document.getElementById("functionTooltip");

    if (!tooltip) return;

    loadNeighborEffects(cell);

    tooltip.classList.remove("hidden");
    tooltip.hidden = false;

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

    const announcement = document.getElementById("tooltipAnnouncement");

    if (announcement) {

        const tooltipText = tooltip.innerText
            .replace(/\s+/g, " ")
            .trim();

        announcement.textContent = "";

        setTimeout(() => {
            announcement.textContent = tooltipText;
        }, 100);
    }
}


function enableTooltip() {
    const tooltip = document.getElementById("functionTooltip");
    const gridCells = document.querySelectorAll(".gridCell");

    gridCells.forEach((cell) => {

        // Koppel tooltip aan deze cel
        cell.setAttribute("aria-describedby", "functionTooltip");

        cell.addEventListener("mousemove", function (ev) {
            showEffectsTooltip(cell, ev.clientX, ev.clientY);
        });

        cell.addEventListener("mouseleave", function () {
            tooltip.classList.add("hidden");
        });

        cell.addEventListener("focus", function () {

            const rect = cell.getBoundingClientRect();

            showEffectsTooltip(
                cell,
                rect.left + rect.width / 2,
                rect.top + rect.height / 2
            );
        });

        cell.addEventListener("blur", function () {
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

    fetch("/grid/assign-function", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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


// FUNCTIE VERPLAATSEN IN MYSQL
function moveFunctionInGrid(fromCellId, toCellId, functionId) {
    if (!fromCellId || !toCellId || !functionId) {
        console.error("from_cell_id, to_cell_id of function_id ontbreekt");
        return;
    }

    fetch("/grid/move-function", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
window.updateEffectTable = function (effectTotals, qualityOfLife) {
    Object.keys(effectTotals).forEach(function (category) {
        const element = document.querySelector(`[data-effect-category="${category}"]`);

        if (element) {
            const value = Number(effectTotals[category]);
            element.textContent = value;
            element.classList.remove("positiveEffect", "negativeEffect", "neutralEffect");
            element.classList.add(getEffectClass(value));
        }
    });

    const qualityElement = document.getElementById("qualityOfLifeValue");

    if (qualityElement) {
        const total = Number(qualityOfLife);
        qualityElement.textContent = total;
        qualityElement.classList.remove("positiveEffect", "negativeEffect", "neutralEffect");
        qualityElement.classList.add(getEffectClass(total));
    }

    requestAnimationFrame(() => {
        updateEffectsAccessibilityLabelForReader();
    });
};


// EFFECT TABLE IN 1 KEER VOORLEZEN MET ECHTE TEKST
function updateEffectsAccessibilityLabelForReader() {

    const effectsList =
        document.getElementById("effectsList");

    const effectsReader =
        document.getElementById("effectsReader");

    if (!effectsList || !effectsReader) {
        return;
    }

    const effectSpans =
        effectsList.querySelectorAll(
            "[data-effect-category]"
        );

    const effectsText = [];

    effectSpans.forEach((span) => {

        const category =
            span.dataset.effectCategory;

        const numericValue =
            Number(span.textContent.trim());

        const readableValue =
            numericValue < 0
                ? `minus ${Math.abs(numericValue)}`
                : `${numericValue}`;

        effectsText.push(
            `${category} ${readableValue}`
        );
    });

    const qualityElement =
        document.getElementById(
            "qualityOfLifeValue"
        );

    if (qualityElement) {

        const total =
            Number(
                qualityElement.textContent.trim()
            );

        const readableTotal =
            total < 0
                ? `minus ${Math.abs(total)}`
                : `${total}`;

        effectsText.push(
            `Quality of Life ${readableTotal}`
        );
    }

    const fullText =
        `Effects updated. ${effectsText.join(". ")}.`;

    // Screenreader live region updaten
    effectsReader.textContent = "";

    setTimeout(() => {
        effectsReader.textContent = fullText;
    }, 100);

    // Als gebruiker naar de lijst tabt
    effectsList.setAttribute(
        "aria-label",
        fullText
    );
}


// TOOLTIP EFFECTS UPDATEN
function updateTooltipEffects(effectTotals, qualityOfLife = null) {
    let calculatedQualityOfLife = 0;

    Object.keys(effectTotals).forEach(function (category) {
        const value = Number(effectTotals[category]);
        calculatedQualityOfLife += value;

        const element = document.querySelector(
            `[data-tooltip-effect-category="${category}"]`
        );

        if (element) {
            element.textContent = value;
            element.classList.remove(
                "positiveEffect",
                "negativeEffect",
                "neutralEffect"
            );
            element.classList.add(getEffectClass(value));
        }
    });

    const qualityElement = document.getElementById("tooltipQualityOfLife");

    if (qualityElement) {
        const total = qualityOfLife ?? calculatedQualityOfLife;

        qualityElement.textContent = total;
        qualityElement.classList.remove(
            "positiveEffect",
            "negativeEffect",
            "neutralEffect"
        );
        qualityElement.classList.add(getEffectClass(total));
    }

    announceKeyboardStatus(
        `Environmental Quality ${
            Number(effectTotals["Environmental Quality"] ?? 0) < 0
                ? `minus ${Math.abs(Number(effectTotals["Environmental Quality"]))}`
                : effectTotals["Environmental Quality"] ?? 0
        }. ` +
        `Mobility ${
            Number(effectTotals["Mobility"] ?? 0) < 0
                ? `minus ${Math.abs(Number(effectTotals["Mobility"]))}`
                : effectTotals["Mobility"] ?? 0
        }. ` +
        `Recreation ${
            Number(effectTotals["Recreation"] ?? 0) < 0
                ? `minus ${Math.abs(Number(effectTotals["Recreation"]))}`
                : effectTotals["Recreation"] ?? 0
        }. ` +
        `Safety ${
            Number(effectTotals["Safety"] ?? 0) < 0
                ? `minus ${Math.abs(Number(effectTotals["Safety"]))}`
                : effectTotals["Safety"] ?? 0
        }. ` +
        `Services ${
            Number(effectTotals["Services"] ?? 0) < 0
                ? `minus ${Math.abs(Number(effectTotals["Services"]))}`
                : effectTotals["Services"] ?? 0
        }. ` +
        `Quality of Life ${
            Number(qualityOfLife ?? calculatedQualityOfLife) < 0
                ? `minus ${Math.abs(Number(qualityOfLife ?? calculatedQualityOfLife))}`
                : qualityOfLife ?? calculatedQualityOfLife
        }.`
    );
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


// STATUS VOOR SCREENREADERS
function announceKeyboardStatus(message) {

    let status = document.getElementById("keyboardDragStatus");

    if (!status) {

        status = document.createElement("div");

        status.id = "keyboardDragStatus";
        status.className = "sr-only";

        status.setAttribute("role", "status");
        status.setAttribute("aria-live", "assertive");
        status.setAttribute("aria-atomic", "true");

        document.body.appendChild(status);
    }

    status.remove();

    status = document.createElement("div");

    status.id = "keyboardDragStatus";
    status.className = "sr-only";

    status.setAttribute("role", "status");
    status.setAttribute("aria-live", "assertive");
    status.setAttribute("aria-atomic", "true");

    document.body.appendChild(status);

    setTimeout(() => {
        status.textContent = message;
    }, 50);
}