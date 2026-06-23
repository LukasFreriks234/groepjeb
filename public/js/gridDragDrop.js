let selectedEventId = null;
let selectedEventImage = null;
let eventPlacementMode = false;

let selectedMobileElement = null;
let selectedMobileData = null;
let mobileDragEnabled = false;
let tooltipHideTimeout = null;

let selectedKeyboardElement = null;
let selectedKeyboardData = null;

let scrollInterval = null;
let focusInsideGrid = false;
let pointerGridEventData = null;
let pointerGridEventStart = null;
let nativeGridEventDragStarted = false;

document.addEventListener("DOMContentLoaded", function () {
    enableDrag();
    enableGridEventMouseDrag();
    enablePointerGridEventMove();
    enableMobileDrag();
    enableKeyboardDragDrop();
    enableArrowKeyGridNavigation();
    enableTooltip();
    prepareGridAccessibility();
    prepareDeleteButtonsForKeyboard();
    updateEffectsAccessibilityLabelForReader();

    if (typeof window.refreshDeleteButtons === "function") {
        window.refreshDeleteButtons();
    }

    document.querySelectorAll(".gridCell").forEach((cell) => {
        updateCellLabel(cell);

        cell.addEventListener("click", function () {
            if (!eventPlacementMode || !selectedEventId || !selectedEventImage) {
                return;
            }

            placeEventInCell(cell, {
                source: "eventLibrary",
                eventId: selectedEventId,
                imageSrc: selectedEventImage.src,
                imageAlt: selectedEventImage.alt || "Event"
            });
        });

        cell.addEventListener("dragover", function (ev) {
            ev.preventDefault();
            ev.dataTransfer.dropEffect = "move";
        });

        cell.addEventListener("drop", function (ev) {
            ev.preventDefault();

            const dragData = getDragData(ev);

            if (!dragData) {
                return;
            }

            if (dragData.source === "library") {
                const originalItem = document.getElementById(dragData.itemId);

                if (!originalItem) {
                    return;
                }

                placeLibraryFunctionInCell(cell, originalItem, dragData);
                return;
            }

            if (dragData.source === "grid") {
                placeGridFunctionInCell(cell, dragData);
                return;
            }

            if (dragData.source === "eventLibrary" || dragData.source === "gridEvent") {
                placeEventInCell(cell, dragData);
                return;
            }
        });
    });

    document.querySelectorAll("#eventsList li").forEach((eventItem) => {
        eventItem.addEventListener("click", function () {
            selectedEventId = eventItem.dataset.eventId;

            const image = eventItem.querySelector("img");

            if (!selectedEventId || !image) {
                return;
            }

            selectedEventImage = {
                src: image.src,
                alt: image.alt || getEventNameFromItem(eventItem)
            };

            eventPlacementMode = true;

            announceKeyboardStatus(`${selectedEventImage.alt} selected. Click a grid cell to place this event.`);
        });
    });
});

function getDragData(ev) {
    const rawData = ev.dataTransfer.getData("text/plain");

    if (!rawData) {
        return null;
    }

    try {
        return JSON.parse(rawData);
    } catch (error) {
        return {
            source: "library",
            itemId: rawData
        };
    }
}

function clearCell(cell) {
    const existingItems = cell.querySelectorAll(".functionItem, .gridImage, .gridEvents, .delete-btn");

    existingItems.forEach((item) => {
        item.remove();
    });
}

function clearFunctionFromCell(cell) {
    const existingItems = cell.querySelectorAll(".functionItem, .gridImage, .delete-btn");

    existingItems.forEach((item) => {
        item.remove();
    });
}

function markCellOccupied(cell, category) {
    cell.classList.remove("available");
    cell.classList.add("occupied");

    if (category) {
        cell.dataset.category = category;
    }
}

function markCellAvailable(cell) {
    cell.classList.remove("occupied");
    cell.classList.add("available");

    delete cell.dataset.category;
}


function prepareGridAccessibility() {
    const grid = document.querySelector(".metropolisGrid");

    if (grid) {
        if (!grid.getAttribute("role")) {
            grid.setAttribute("role", "grid");
        }

        if (grid.dataset.gridInstructionEnabled !== "true") {
            grid.dataset.gridInstructionEnabled = "true";

            grid.addEventListener("focusin", function (event) {
                const focusedCell = event.target.closest(".gridCell");

                if (!focusedCell || focusInsideGrid) {
                    return;
                }

                focusInsideGrid = true;

                announceKeyboardStatus(
                    "Grid instructions. Use arrow keys to move between grid cells. Select a function or event first, then press Enter or Space on a grid cell to place it. Use Tab to focus an event or delete button."
                );
            });

            grid.addEventListener("focusout", function () {
                setTimeout(function () {
                    if (!grid.contains(document.activeElement)) {
                        focusInsideGrid = false;
                    }
                }, 0);
            });
        }
    }

    document.querySelectorAll(".gridCell").forEach((cell) => {
        if (!cell.getAttribute("tabindex")) {
            cell.setAttribute("tabindex", "0");
        }

        if (!cell.getAttribute("role")) {
            cell.setAttribute("role", "gridcell");
        }

        updateCellLabel(cell);
    });

    prepareGridEventElements();
}

function prepareGridEventElements() {
    document.querySelectorAll(".gridEvents").forEach((container) => {
        container.removeAttribute("aria-hidden");
        container.style.pointerEvents = "auto";
        container.style.zIndex = "1000";
    });

    document.querySelectorAll(".gridEventImage, .draggableGridEvent").forEach((image) => {
        image.classList.add("gridEventImage", "draggableGridEvent");
        image.setAttribute("draggable", "true");
        image.setAttribute("tabindex", "0");
        image.setAttribute("role", "button");
        image.removeAttribute("aria-hidden");
        image.alt = image.alt || getGridEventName(image);

        image.style.pointerEvents = "auto";
        image.style.cursor = "grab";
        image.style.position = "relative";
        image.style.zIndex = "1001";
        image.style.userSelect = "none";
        image.style.webkitUserDrag = "element";

        image.setAttribute("aria-label", `Move event ${getGridEventName(image)} from this grid cell`);
    });
}

function prepareDeleteButtonsForKeyboard() {
    document.querySelectorAll(".delete-btn").forEach((button) => {
        button.setAttribute("tabindex", "0");

        if (!button.getAttribute("aria-label")) {
            button.setAttribute("aria-label", "Remove item from this grid cell");
        }

        if (button.tagName.toLowerCase() === "button" && !button.getAttribute("type")) {
            button.setAttribute("type", "button");
        }
    });
}

function refreshDeleteButtonsIfAvailable() {
    if (typeof window.refreshDeleteButtons === "function") {
        window.refreshDeleteButtons();
    }

    prepareDeleteButtonsForKeyboard();
    prepareGridEventElements();
}

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

function createGridEventImage(data) {
    const image = document.createElement("img");

    image.src = data.imageSrc;
    image.alt = data.imageAlt || "Event";
    image.classList.add("gridEventImage", "draggableGridEvent");
    image.setAttribute("draggable", "true");
    image.setAttribute("tabindex", "0");
    image.setAttribute("role", "button");
    image.setAttribute("aria-label", `Move event ${data.imageAlt || "Event"} from this grid cell`);

    image.dataset.eventId = data.eventId;
    image.dataset.eventName = data.imageAlt || "Event";
    image.dataset.fromCellId = data.targetCellId;

    return image;
}

function getGridImageName(image) {
    return image.dataset.functionName || image.alt || "Function";
}

function getGridEventName(image) {
    return image.dataset.eventName || image.alt || "Event";
}

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

function getEventNameFromItem(item) {
    const eventNameElement = item.querySelector(".eventName");

    if (eventNameElement) {
        return eventNameElement.textContent.trim();
    }

    const image = item.querySelector("img");

    if (image && image.alt) {
        return image.alt.trim();
    }

    return item.textContent.trim();
}

function enableDrag() {
    const functionItems = document.querySelectorAll("#functionsList .functionItem");

    functionItems.forEach((item) => {
        item.setAttribute("draggable", "true");

        if (item.dataset.dragEnabled === "true") {
            return;
        }

        item.dataset.dragEnabled = "true";

        item.addEventListener("dragstart", function (ev) {
            const image = ev.currentTarget.querySelector("img");
            const functionName = getFunctionNameFromItem(ev.currentTarget);

            ev.dataTransfer.effectAllowed = "move";
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

    const eventItems = document.querySelectorAll("#eventsList li");

    eventItems.forEach((item) => {
        //item.setAttribute("draggable", "true");
        item.setAttribute("tabindex", item.getAttribute("tabindex") || "0");
        item.setAttribute("role", item.getAttribute("role") || "button");

        if (item.dataset.dragEnabled === "true") {
            return;
        }

        item.dataset.dragEnabled = "true";

        item.addEventListener("dragstart", function (ev) {
            const image = ev.currentTarget.querySelector("img");
            const eventName = getEventNameFromItem(ev.currentTarget);

            ev.dataTransfer.effectAllowed = "move";
            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    source: "eventLibrary",
                    itemId: ev.currentTarget.id || "",
                    eventId: ev.currentTarget.dataset.eventId,
                    imageSrc: image ? image.src : "",
                    imageAlt: eventName
                })
            );
        });
    });

    const gridImages = document.querySelectorAll(".draggableGridFunction");

    gridImages.forEach((image) => {
        image.setAttribute("draggable", "true");
        image.setAttribute("tabindex", "-1");
        image.setAttribute("aria-hidden", "true");

        if (image.dataset.dragEnabled === "true") {
            return;
        }

        image.dataset.dragEnabled = "true";

        image.addEventListener("dragstart", function (ev) {
            nativeGridEventDragStarted = true;
            pointerGridEventData = null;
            pointerGridEventStart = null;
            ev.stopPropagation();
            ev.dataTransfer.effectAllowed = "move";
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

    prepareGridEventElements();

    const gridEvents = document.querySelectorAll(".draggableGridEvent");

    gridEvents.forEach((image) => {
        image.setAttribute("draggable", "true");
        image.setAttribute("tabindex", "-1");
        image.setAttribute("aria-hidden", "true");
        image.removeAttribute("role");
        image.setAttribute("aria-label", `Move event ${getGridEventName(image)} from this grid cell`);

        if (image.dataset.dragEnabled === "true") {
            return;
        }

        image.dataset.dragEnabled = "true";

        image.addEventListener("dragstart", function (ev) {
            ev.dataTransfer.effectAllowed = "move";
            ev.dataTransfer.setData(
                "text/plain",
                JSON.stringify({
                    source: "gridEvent",
                    eventId: ev.currentTarget.dataset.eventId,
                    fromCellId: ev.currentTarget.dataset.fromCellId,
                    imageSrc: ev.currentTarget.src,
                    imageAlt: getGridEventName(ev.currentTarget)
                })
            );
        });
    });
}

function enableGridEventMouseDrag() {
    document.addEventListener("dragstart", function (ev) {
        const eventImage = ev.target.closest(".draggableGridEvent");

        if (!eventImage) {
            return;
        }

        ev.stopPropagation();

        nativeGridEventDragStarted = true;
        pointerGridEventData = null;
        pointerGridEventStart = null;

        eventImage.setAttribute("draggable", "true");

        ev.dataTransfer.effectAllowed = "move";
        ev.dataTransfer.setData(
            "text/plain",
            JSON.stringify({
                source: "gridEvent",
                eventId: eventImage.dataset.eventId,
                fromCellId: eventImage.dataset.fromCellId,
                imageSrc: eventImage.src,
                imageAlt: getGridEventName(eventImage)
            })
        );
    }, true);
}


function enablePointerGridEventMove() {
    if (document.body.dataset.pointerGridEventMoveEnabled === "true") {
        return;
    }

    document.body.dataset.pointerGridEventMoveEnabled = "true";

    document.addEventListener("pointerdown", function (ev) {
        const eventImage = ev.target.closest(".draggableGridEvent");

        if (!eventImage) {
            return;
        }

        nativeGridEventDragStarted = false;
        pointerGridEventData = buildSelectedData(eventImage);
        pointerGridEventStart = {
            x: ev.clientX,
            y: ev.clientY,
            sourceCell: eventImage.closest(".gridCell")
        };

        const parentCell = eventImage.closest(".gridCell");

        if (parentCell) {
            parentCell.classList.add("keyboardSelected");
        }
    }, { passive: true });

    document.addEventListener("pointerup", function (ev) {
        if (!pointerGridEventData || !pointerGridEventStart || nativeGridEventDragStarted) {
            pointerGridEventData = null;
            pointerGridEventStart = null;
            nativeGridEventDragStarted = false;
            return;
        }

        const movedX = Math.abs(ev.clientX - pointerGridEventStart.x);
        const movedY = Math.abs(ev.clientY - pointerGridEventStart.y);
        const movedEnough = movedX > 8 || movedY > 8;

        const targetElement = document.elementFromPoint(ev.clientX, ev.clientY);
        const targetCell = targetElement ? targetElement.closest(".gridCell") : null;

        document.querySelectorAll(".gridCell.keyboardSelected").forEach((cell) => {
            cell.classList.remove("keyboardSelected");
        });

        if (
            movedEnough &&
            targetCell &&
            targetCell.dataset.id !== pointerGridEventData.fromCellId
        ) {
            ev.preventDefault();
            placeEventInCell(targetCell, pointerGridEventData);
        }

        pointerGridEventData = null;
        pointerGridEventStart = null;
        nativeGridEventDragStarted = false;
    }, { passive: false });

    document.addEventListener("pointercancel", function () {
        pointerGridEventData = null;
        pointerGridEventStart = null;
        nativeGridEventDragStarted = false;

        document.querySelectorAll(".gridCell.keyboardSelected").forEach((cell) => {
            cell.classList.remove("keyboardSelected");
        });
    });
}

function placeLibraryFunctionInCell(cell, originalItem, dragData) {
    const functionName = dragData.imageAlt || getFunctionNameFromItem(originalItem);

    clearFunctionFromCell(cell);

    const newImage = createGridImage({
        functionId: originalItem.dataset.functionId || dragData.functionId,
        category: originalItem.dataset.category || dragData.category,
        imageSrc: dragData.imageSrc,
        imageAlt: functionName,
        targetCellId: cell.dataset.id
    });

    cell.prepend(newImage);

    markCellOccupied(cell, originalItem.dataset.category || dragData.category);
    updateCellLabel(cell);

    saveFunctionInGrid(cell, originalItem);

    enableDrag();
    enableMobileDrag();
    refreshDeleteButtonsIfAvailable();

    setTimeout(() => {
        updateEffectsAccessibilityLabelForReader();
    }, 500);

    cell.focus();
}

function placeGridFunctionInCell(cell, dragData) {
    const fromCell = document.querySelector(`.gridCell[data-id="${dragData.fromCellId}"]`);

    if (!fromCell) {
        return;
    }

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

    clearFunctionFromCell(cell);

    const draggedImage = createGridImage({
        functionId: dragData.functionId,
        category: dragData.category,
        imageSrc: dragData.imageSrc,
        imageAlt: dragData.imageAlt,
        targetCellId: cell.dataset.id
    });

    cell.prepend(draggedImage);
    markCellOccupied(cell, dragData.category);
    updateCellLabel(cell);

    clearFunctionFromCell(fromCell);

    if (targetData) {
        const swappedImage = createGridImage({
            functionId: targetData.functionId,
            category: targetData.category,
            imageSrc: targetData.imageSrc,
            imageAlt: targetData.imageAlt,
            targetCellId: fromCell.dataset.id
        });

        fromCell.prepend(swappedImage);
        markCellOccupied(fromCell, targetData.category);
        updateCellLabel(fromCell);
    } else if (fromCell.querySelector(".gridEventImage")) {
        markCellOccupied(fromCell, null);
        updateCellLabel(fromCell);
    } else {
        markCellAvailable(fromCell);
        updateCellLabel(fromCell);
    }

    moveFunctionInGrid(
        dragData.fromCellId,
        cell.dataset.id,
        dragData.functionId
    );

    enableDrag();
    enableMobileDrag();
    refreshDeleteButtonsIfAvailable();

    setTimeout(() => {
        updateEffectsAccessibilityLabelForReader();
    }, 500);

    cell.focus();
}

function placeEventInCell(cell, dragData) {
    if (!dragData.eventId) {
        announceKeyboardStatus("Event is missing.");
        return;
    }

    saveEventInGrid(cell, dragData);
}

function addEventImageToCell(cell, dragData) {
    let eventContainer = cell.querySelector(".gridEvents");

    if (!eventContainer) {
        eventContainer = document.createElement("div");
        eventContainer.classList.add("gridEvents");
        cell.appendChild(eventContainer);
    }

    eventContainer.removeAttribute("aria-hidden");

    const alreadyExists = eventContainer.querySelector(
        `.gridEventImage[data-event-id="${dragData.eventId}"]`
    );

    if (alreadyExists) {
        alreadyExists.dataset.fromCellId = cell.dataset.id;
        prepareGridEventElements();
        refreshDeleteButtonsIfAvailable();
        return;
    }

    const newEventImage = createGridEventImage({
        eventId: dragData.eventId,
        imageSrc: dragData.imageSrc,
        imageAlt: dragData.imageAlt,
        targetCellId: cell.dataset.id
    });

    eventContainer.appendChild(newEventImage);

    markCellOccupied(cell, null);
    updateCellLabel(cell);
    enableDrag();
    prepareGridEventElements();
    refreshDeleteButtonsIfAvailable();
}

function removeGridEventImage(fromCellId, eventId) {
    if (!fromCellId || !eventId) {
        return;
    }

    const fromCell = document.querySelector(`.gridCell[data-id="${fromCellId}"]`);

    if (!fromCell) {
        return;
    }

    const eventImage = fromCell.querySelector(`.gridEventImage[data-event-id="${eventId}"]`);

    if (eventImage) {
        eventImage.remove();
    }

    const eventContainer = fromCell.querySelector(".gridEvents");

    if (eventContainer && eventContainer.children.length === 0) {
        eventContainer.remove();
    }

    if (!fromCell.querySelector(".gridImage") && !fromCell.querySelector(".gridEventImage")) {
        markCellAvailable(fromCell);
    }

    updateCellLabel(fromCell);
    refreshDeleteButtonsIfAvailable();
}

function saveEventInGrid(cell, dragData) {
    const body = {
        event_id: dragData.eventId,
        cell_id: cell.dataset.id,
        route_order: 1
    };

    if (window.simulationClock && typeof window.simulationClock.minute !== "undefined") {
        body.simulation_minute = window.simulationClock.minute;
    }

    fetch("/grid/assign-event", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(body)
    })
        .then(response => response.json())
        .then(data => {
            console.log("Event saved:", data);

            if (!data.success) {
                announceKeyboardStatus(data.message || "This event cannot be placed here.");
                return;
            }

            addEventImageToCell(cell, dragData);

            if (dragData.source === "gridEvent" && dragData.fromCellId !== cell.dataset.id) {
                removeEventFromGrid(dragData.fromCellId, dragData.eventId, false);
                removeGridEventImage(dragData.fromCellId, dragData.eventId);
            }

            if (data.effectTotals) {
                updateEffectTable(data.effectTotals, data.qualityOfLife);
            }

            if (dragData.source === "eventLibrary") {
                selectedEventId = null;
                selectedEventImage = null;
                eventPlacementMode = false;
            }

            refreshDeleteButtonsIfAvailable();

            setTimeout(() => {
                updateEffectsAccessibilityLabelForReader();
            }, 100);

            updateCellLabel(cell);
            announceKeyboardStatus(getCellLabelText(cell));
            cell.focus();
        })
        .catch(error => {
            console.error("Event save error:", error);
            announceKeyboardStatus("Could not place this event.");
        });
}

function removeEventFromGrid(cellId, eventId, updateEffects = true) {
    fetch("/grid/remove-event", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cell_id: cellId,
            event_id: eventId
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Event removed:", data);

            if (updateEffects && data.success && data.effectTotals) {
                updateEffectTable(data.effectTotals, data.qualityOfLife);
            }
        })
        .catch(error => {
            console.error("Event remove error:", error);
        });
}

window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
        document
            .querySelectorAll(".selectedMobileCell, .keyboardSelected")
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
    if (mobileDragEnabled) {
        return;
    }

    mobileDragEnabled = true;

    document.addEventListener("touchstart", function (ev) {
        const touchedCell = ev.target.closest(".gridCell");

        let touchedItem = ev.target.closest(
            "#functionsList .functionItem, #eventsList li, .draggableGridFunction, .draggableGridEvent"
        );

        if (!touchedItem && touchedCell) {
            touchedItem =
                touchedCell.querySelector(".draggableGridFunction") ||
                touchedCell.querySelector(".draggableGridEvent");
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

            if (
                selectedMobileData.source === "gridEvent" &&
                selectedMobileData.fromCellId === touchedCell.dataset.id
            ) {
                clearMobileSelection();
                return;
            }

            placeSelectedMobileItem(touchedCell);
            return;
        }

        if (touchedItem) {
            ev.preventDefault();
            ev.stopPropagation();

            selectMobileItem(touchedItem);
        }
    }, { passive: false });
}

function selectMobileItem(item) {
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

    selectedMobileData = buildSelectedData(item);

    refreshDeleteButtonsIfAvailable();
}

function clearMobileSelection() {
    document
        .querySelectorAll("#functionsList .functionItem, #eventsList li, .draggableGridFunction, .draggableGridEvent, .gridCell")
        .forEach((item) => {
            item.classList.remove("selectedMobileCell");
            item.classList.remove("keyboardSelected");
            item.setAttribute("aria-selected", "false");
        });

    selectedMobileElement = null;
    selectedMobileData = null;
}

function placeSelectedMobileItem(cell) {
    if (!selectedMobileData || !selectedMobileElement) {
        return;
    }

    if (selectedMobileData.source === "library") {
        placeLibraryFunctionInCell(cell, selectedMobileElement, selectedMobileData);
        clearMobileSelection();
        return;
    }

    if (selectedMobileData.source === "grid") {
        placeGridFunctionInCell(cell, selectedMobileData);
        clearMobileSelection();
        return;
    }

    if (selectedMobileData.source === "eventLibrary" || selectedMobileData.source === "gridEvent") {
        placeEventInCell(cell, selectedMobileData);
        clearMobileSelection();
    }
}

function enableKeyboardDragDrop() {
    document.addEventListener("keydown", function (ev) {
        const isEnter = ev.key === "Enter";
        const isSpace = ev.key === " " || ev.key === "Spacebar" || ev.code === "Space";

        if (!isEnter && !isSpace) {
            return;
        }

        const deleteButton = ev.target.closest(".delete-btn");

        if (deleteButton) {
            ev.preventDefault();
            deleteButton.click();
            return;
        }

        const functionItem = ev.target.closest("#functionsList .functionItem");
        const eventItem = ev.target.closest("#eventsList li");
        const gridFunctionImage = ev.target.closest(".draggableGridFunction");
        const gridEventImage = ev.target.closest(".draggableGridEvent");
        const gridCell = ev.target.closest(".gridCell");

        if (!functionItem && !eventItem && !gridFunctionImage && !gridEventImage && !gridCell) {
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

            if (
                selectedKeyboardData.source === "gridEvent" &&
                selectedKeyboardData.fromCellId === gridCell.dataset.id
            ) {
                clearKeyboardSelection();
                announceKeyboardStatus("Selection cancelled.");
                return;
            }

            placeSelectedKeyboardItem(gridCell);
            return;
        }

        if (gridEventImage) {
            selectKeyboardItem(gridEventImage);
            return;
        }

        if (gridFunctionImage) {
            selectKeyboardItem(gridFunctionImage);
            return;
        }

        if (functionItem) {
            selectKeyboardItem(functionItem);
            return;
        }

        if (eventItem) {
            selectKeyboardItem(eventItem);
            return;
        }

        if (gridCell) {
            const imageInCell = gridCell.querySelector(".draggableGridFunction");

            if (imageInCell) {
                selectKeyboardItem(imageInCell);
                return;
            }

            announceKeyboardStatus("Empty grid cell. Select a function or event first, then press Enter or Space here to place it.");
        }
    });
}

function selectKeyboardItem(item) {
    clearKeyboardSelection();

    selectedKeyboardElement = item;

    const parentCell = item.closest(".gridCell");

    if (parentCell) {
        parentCell.classList.add("keyboardSelected");
    } else {
        item.classList.add("keyboardSelected");
    }

    selectedKeyboardData = buildSelectedData(item);

    if (!selectedKeyboardData) {
        return;
    }

    if (selectedKeyboardData.source === "eventLibrary" || selectedKeyboardData.source === "gridEvent") {
        announceKeyboardStatus(
            `${selectedKeyboardData.imageAlt} selected. Move to a grid cell and press Enter or Space to place it.`
        );
    } else if (selectedKeyboardData.source === "grid") {
        announceKeyboardStatus(
            `${selectedKeyboardData.imageAlt} selected. Move to another grid cell with Tab, Shift Tab, or arrow keys. Press Enter or Space to move or swap it.`
        );
    } else {
        announceKeyboardStatus(
            `${selectedKeyboardData.imageAlt} selected. Move to a grid cell and press Enter or Space to place it.`
        );
    }

    refreshDeleteButtonsIfAvailable();
}

function clearKeyboardSelection() {
    document
        .querySelectorAll("#functionsList .functionItem, #eventsList li, .draggableGridFunction, .draggableGridEvent, .gridCell")
        .forEach((item) => {
            item.classList.remove("selectedMobileCell");
            item.classList.remove("keyboardSelected");
        });

    selectedKeyboardElement = null;
    selectedKeyboardData = null;
}

function placeSelectedKeyboardItem(cell) {
    if (!selectedKeyboardData || !selectedKeyboardElement) {
        return;
    }

    if (selectedKeyboardData.source === "library") {
        placeLibraryFunctionInCell(cell, selectedKeyboardElement, selectedKeyboardData);
        clearKeyboardSelection();
        return;
    }

    if (selectedKeyboardData.source === "grid") {
        placeGridFunctionInCell(cell, selectedKeyboardData);
        clearKeyboardSelection();
        return;
    }

    if (selectedKeyboardData.source === "eventLibrary" || selectedKeyboardData.source === "gridEvent") {
        placeEventInCell(cell, selectedKeyboardData);
        clearKeyboardSelection();
    }
}

function buildSelectedData(item) {
    if (item.classList.contains("draggableGridFunction")) {
        return {
            source: "grid",
            functionId: item.dataset.functionId,
            fromCellId: item.dataset.fromCellId,
            category: item.dataset.category,
            imageSrc: item.src,
            imageAlt: getGridImageName(item)
        };
    }

    if (item.classList.contains("draggableGridEvent")) {
        return {
            source: "gridEvent",
            eventId: item.dataset.eventId,
            fromCellId: item.dataset.fromCellId,
            imageSrc: item.src,
            imageAlt: getGridEventName(item)
        };
    }

    if (item.closest("#eventsList")) {
        const image = item.querySelector("img");
        const eventName = getEventNameFromItem(item);

        return {
            source: "eventLibrary",
            itemId: item.id || "",
            eventId: item.dataset.eventId,
            imageSrc: image ? image.src : "",
            imageAlt: eventName
        };
    }

    const image = item.querySelector("img");
    const functionName = getFunctionNameFromItem(item);

    return {
        source: "library",
        itemId: item.id,
        functionId: item.dataset.functionId,
        category: item.dataset.category,
        imageSrc: image ? image.src : "",
        imageAlt: functionName
    };
}

function enableArrowKeyGridNavigation() {
    document.addEventListener("keydown", function (ev) {
        const currentCell = ev.target.closest(".gridCell");

        if (!currentCell) {
            return;
        }

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

function getReadableCellPosition(cell) {
    return {
        row: Number(cell.dataset.y) + 1,
        column: Number(cell.dataset.x) + 1
    };
}

function announceCurrentGridCell(cell) {
    announceKeyboardStatus(getCellLabelText(cell), 150);
}

function getCellLabelText(cell) {
    const position = getReadableCellPosition(cell);
    const image = cell.querySelector(".gridImage");
    const eventImages = cell.querySelectorAll(".gridEventImage");

    let label = `Grid cell row ${position.row}, column ${position.column}. `;

    if (image) {
        label += `Contains ${getGridImageName(image)}. `;
    } else {
        label += "Contains no function. ";
    }

    if (eventImages.length > 0) {
        const eventNames = Array.from(eventImages)
            .map((eventImage) => getGridEventName(eventImage))
            .join(", ");

        label += `Events: ${eventNames}. `;
    }

    if (!image && eventImages.length === 0) {
        label += "Empty cell. ";
    }

    return label;
}

function updateCellLabel(cell) {
    cell.setAttribute("aria-label", getCellLabelText(cell));
}

function setCellLabel(cell, name = null, category = null) {
    updateCellLabel(cell);
}

function showEffectsTooltip(cell, clientX, clientY, autoHide = false, announceTooltip = true) {
    const tooltip = document.getElementById("functionTooltip");

    if (!tooltip) {
        return;
    }

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

    if (announceTooltip) {
        const announcement = document.getElementById("tooltipAnnouncement");

        if (announcement) {
            // const tooltipText = tooltip.innerText.replace(/\s+/g, " ").trim();

            announcement.textContent = "";

            setTimeout(() => {
                announcement.textContent = tooltipText;
            }, 600);
        }
    }

    if (autoHide) {
        clearTimeout(tooltipHideTimeout);

        tooltipHideTimeout = setTimeout(() => {
            tooltip.classList.add("hidden");
            tooltip.hidden = true;
        }, 2500);
    }
}

function enableTooltip() {
    const tooltip = document.getElementById("functionTooltip");
    const gridCells = document.querySelectorAll(".gridCell");

    if (!tooltip) {
        return;
    }

    gridCells.forEach((cell) => {
        cell.setAttribute("aria-describedby", "functionTooltip");

        cell.addEventListener("mousemove", function (ev) {
            showEffectsTooltip(cell, ev.clientX, ev.clientY, false, true);
        });

        cell.addEventListener("mouseleave", function () {
            tooltip.classList.add("hidden");
            tooltip.hidden = true;
        });

        cell.addEventListener("focus", function () {
            const rect = cell.getBoundingClientRect();

            showEffectsTooltip(
                cell,
                rect.left + rect.width / 2,
                rect.top + rect.height / 2,
                false,
                false
            );
        });

        cell.addEventListener("blur", function () {
            tooltip.classList.add("hidden");
            tooltip.hidden = true;
        });
    });
}

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

function getEffectClass(value) {
    if (value > 0) {
        return "positiveEffect";
    }

    if (value < 0) {
        return "negativeEffect";
    }

    return "neutralEffect";
}

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

let lastEffectsAnnouncement = "";

function updateEffectsAccessibilityLabelForReader() {
    const effectsList = document.getElementById("effectsList");
    const effectsReader = document.getElementById("effectsReader");
    if (!effectsList || !effectsReader) return;

    const effectSpans = effectsList.querySelectorAll("[data-effect-category]");
    const effectsText = [];

    effectSpans.forEach((span) => {
        const category = span.dataset.effectCategory;
        const numericValue = Number(span.textContent.trim());
        const readableValue = numericValue < 0 ? `minus ${Math.abs(numericValue)}` : `${numericValue}`;
        effectsText.push(`${category} ${readableValue}`);
    });

    const qualityElement = document.getElementById("qualityOfLifeValue");
    if (qualityElement) {
        const total = Number(qualityElement.textContent.trim());
        const readableTotal = total < 0 ? `minus ${Math.abs(total)}` : `${total}`;
        effectsText.push(`Quality of Life ${readableTotal}`);
    }

    const fullText = `Effects updated. ${effectsText.join(". ")}.`;

    if (fullText === lastEffectsAnnouncement) return;
    lastEffectsAnnouncement = fullText;

    effectsReader.textContent = fullText;
}

function loadNeighborEffects(cell) {
    const cellId = cell.dataset.id;

    if (!cellId) {
        return;
    }

    fetch("/grid/neighbor-effects", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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

function updateTooltipEffects(effectTotals, qualityOfLife = null) {
    let calculatedQualityOfLife = 0;

    Object.keys(effectTotals).forEach(function (category) {
        const value = Number(effectTotals[category]);
        calculatedQualityOfLife += value;

        const element = document.querySelector(`[data-tooltip-effect-category="${category}"]`);

        if (element) {
            element.textContent = value;
            element.classList.remove("positiveEffect", "negativeEffect", "neutralEffect");
            element.classList.add(getEffectClass(value));
        }
    });

    const qualityElement = document.getElementById("tooltipQualityOfLife");

    if (qualityElement) {
        const total = qualityOfLife ?? calculatedQualityOfLife;

        qualityElement.textContent = total;
        qualityElement.classList.remove("positiveEffect", "negativeEffect", "neutralEffect");
        qualityElement.classList.add(getEffectClass(total));
    }
}

function stopAutoScroll() {
    clearInterval(scrollInterval);
    scrollInterval = null;
}

function startAutoScroll(direction) {
    if (scrollInterval) {
        return;
    }

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

window.addEventListener("simulation:tick", (event) => {
    fetch("/grid/check-expired-events", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector(
                'meta[name="csrf-token"]'
            ).content
        },
        body: JSON.stringify({
            minute: event.detail.minute
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                return;
            }

            data.expiredEvents.forEach(expired => {
                removeGridEventImage(
                    expired.grid_cell_id,
                    expired.event_id
                );
            });

            updateEffectTable(
                data.effectTotals,
                data.qualityOfLife
            );
        });
});

let keyboardStatusTimeout = null;
let keyboardStatusElement = null;

function getKeyboardStatusElement() {
    if (keyboardStatusElement && document.body.contains(keyboardStatusElement)) {
        return keyboardStatusElement;
    }

    keyboardStatusElement = document.createElement("div");
    keyboardStatusElement.id = "keyboardDragStatus";
    keyboardStatusElement.className = "sr-only";
    keyboardStatusElement.setAttribute("role", "status");
    keyboardStatusElement.setAttribute("aria-live", "assertive");
    keyboardStatusElement.setAttribute("aria-atomic", "true");

    document.body.appendChild(keyboardStatusElement);

    return keyboardStatusElement;
}

function announceKeyboardStatus(message, delay = 100) {
    const status = getKeyboardStatusElement();

    clearTimeout(keyboardStatusTimeout);

    status.textContent = "";

    keyboardStatusTimeout = setTimeout(() => {
        status.textContent = message;
    }, delay);
}
