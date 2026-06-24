document.addEventListener("DOMContentLoaded", function () {
    function cellHasFunction(cell) {
        return Boolean(
            cell.querySelector(".functionItem") ||
            cell.querySelector(".gridImage")
        );
    }

    function cellHasEvents(cell) {
        return Boolean(cell.querySelector(".gridEventImage"));
    }

    function cellHasContent(cell) {
        return cellHasFunction(cell) || cellHasEvents(cell);
    }

    function getCellContentLabel(cell) {
        const functionImage = cell.querySelector(".gridImage");
        const eventImages = cell.querySelectorAll(".gridEventImage");
        const names = [];

        if (functionImage) {
            names.push(
                functionImage.dataset.functionName ||
                functionImage.alt ||
                "function"
            );
        }

        eventImages.forEach(function (eventImage) {
            names.push(
                eventImage.dataset.eventName ||
                eventImage.alt ||
                "event"
            );
        });

        if (names.length === 0) {
            return "content";
        }

        return names.join(", ");
    }

    function createDeleteButton(cell) {
        if (!cell) {
            return;
        }

        if (!cellHasContent(cell)) {
            removeDeleteButton(cell);
            return;
        }

        const existingButton = cell.querySelector(".delete-btn");

        if (existingButton) {
            existingButton.setAttribute(
                "aria-label",
                `Remove ${getCellContentLabel(cell)} from this grid cell`
            );
            return;
        }

        const button = document.createElement("button");

        button.innerHTML = "✕";
        button.className = "delete-btn";
        button.type = "button";
        button.setAttribute("aria-label", `Remove ${getCellContentLabel(cell)} from this grid cell`);

        cell.appendChild(button);
    }

    function removeDeleteButton(cell) {
        const button = cell.querySelector(".delete-btn");

        if (button) {
            button.remove();
        }
    }

    function removeVisualCellContent(cell) {

        // activation visual
        const image = cell.querySelector('.gridEventImage');
        if (image) {
            const eventId = image.dataset.eventId;
            const eventsList = document.querySelector('#eventsList');

            const originalEvent = eventsList.querySelector(
                `.eventItem[data-event-id="${eventId}"]`
            );

            if (originalEvent) {
                originalEvent.setAttribute("active", "false");
                originalEvent.style.backgroundColor = "lightblue";
            }
        }
        const items = cell.querySelectorAll(
            ".functionItem, .gridImage, .gridEvents, .delete-btn"
        );

        items.forEach(function (item) {
            item.remove();
        });
    }

    function refreshDeleteButtons() {
        document.querySelectorAll(".gridCell").forEach(function (cell) {
            if (cellHasContent(cell)) {
                createDeleteButton(cell);
            } else {
                removeDeleteButton(cell);
            }
        });
    }

    function announceDeleteStatus(message) {
        if (typeof announceKeyboardStatus === "function") {
            announceKeyboardStatus(message);
            return;
        }

        let status = document.getElementById("deleteStatus");

        if (!status) {
            status = document.createElement("div");
            status.id = "deleteStatus";
            status.className = "sr-only";
            status.setAttribute("role", "status");
            status.setAttribute("aria-live", "polite");
            status.setAttribute("aria-atomic", "true");
            document.body.appendChild(status);
        }

        status.textContent = "";

        setTimeout(function () {
            status.textContent = message;
        }, 50);
    }

    function updateDeletedCell(cell) {
        removeVisualCellContent(cell);

        cell.classList.remove("occupied", "selectedMobileCell", "keyboardSelected");
        cell.classList.add("available");

        delete cell.dataset.category;

        if (typeof updateCellLabel === "function") {
            updateCellLabel(cell);
        } else {
            cell.setAttribute("aria-label", "Empty grid cell.");
        }

        refreshDeleteButtons();

        if (typeof enableDrag === "function") {
            enableDrag();
        }

        if (typeof enableMobileDrag === "function") {
            enableMobileDrag();
        }

        if (typeof updateEffectsAccessibilityLabelForReader === "function") {
            updateEffectsAccessibilityLabelForReader();
        }

        cell.focus();
        announceDeleteStatus("Content removed from this grid cell.");
    }

    function deleteCellContent(cell) {
        const id = cell.dataset.id;

        if (!id) {
            console.error("Grid cell id ontbreekt");
            announceDeleteStatus("Could not remove the content because the grid cell id is missing.");
            return;
        }

        if (typeof clearMobileSelection === "function") {
            clearMobileSelection();
        }

        if (typeof clearKeyboardSelection === "function") {
            clearKeyboardSelection();
        }

        fetch("/remove-function", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content")
            },
            body: JSON.stringify({
                id: id,
                cell_id: id
            })
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error(`Delete request failed with status ${response.status}`);
                }

                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    announceDeleteStatus(data.message || "Could not remove the content from this grid cell.");
                    return;
                }

                updateDeletedCell(cell);

                if (data.effectTotals && typeof updateEffectTable === "function") {
                    updateEffectTable(data.effectTotals, data.qualityOfLife);
                }
            })
            .catch(function (error) {
                console.error("Delete error:", error);
                announceDeleteStatus("The server did not confirm the delete, but the content was removed visually.");
                updateDeletedCell(cell);
            });
    }

    window.refreshDeleteButtons = refreshDeleteButtons;

    document.body.addEventListener("click", function (event) {
        const button = event.target.closest(".delete-btn");

        if (!button) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const cell = button.closest(".gridCell");

        if (!cell) {
            return;
        }

        deleteCellContent(cell);
    }, true);

    document.body.addEventListener("mouseover", function (event) {
        const cell = event.target.closest(".gridCell");

        if (!cell) {
            return;
        }

        createDeleteButton(cell);
    });

    document.body.addEventListener("focusin", function (event) {
        const cell = event.target.closest(".gridCell");

        if (!cell) {
            return;
        }

        createDeleteButton(cell);
    });

    const grid = document.querySelector(".metropolisGrid");

    if (grid) {
        const observer = new MutationObserver(function () {
            refreshDeleteButtons();
        });

        observer.observe(grid, {
            childList: true,
            subtree: true
        });
    }

    refreshDeleteButtons();
});
