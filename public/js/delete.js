document.addEventListener("DOMContentLoaded", function () {
    function cellHasFunction(cell) {
        return cell.querySelector(".functionItem") || cell.querySelector(".gridImage");
    }

    function cellHasEvents(cell) {
        return cell.querySelector(".gridEventImage");
    }

    function cellHasContent(cell) {
        return cellHasFunction(cell) || cellHasEvents(cell);
    }

    function createDeleteButton(cell) {
        if (!cell) return;

        if (!cellHasContent(cell)) {
            removeDeleteButton(cell);
            return;
        }

        if (cell.querySelector(".delete-btn")) {
            return;
        }

        const btn = document.createElement("button");
        btn.innerHTML = "✕";
        btn.className = "delete-btn";
        btn.type = "button";
        btn.setAttribute("aria-label", "Remove function and events from this cell");

        cell.appendChild(btn);

        btn.addEventListener("click", function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            deleteCellContent(cell);
        });

        btn.addEventListener("touchstart", function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            deleteCellContent(cell);
        }, { passive: false });
    }

    function removeDeleteButton(cell) {
        const btn = cell.querySelector(".delete-btn");

        if (btn) {
            btn.remove();
        }
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

    window.refreshDeleteButtons = refreshDeleteButtons;

    function deleteCellContent(cell) {
        const id = cell.dataset.id;

        if (!id) {
            console.error("Grid cell id ontbreekt");
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
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content")
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                cell.innerHTML = "";
                cell.classList.remove("occupied", "selectedMobileCell", "keyboardSelected");
                cell.classList.add("available");

                delete cell.dataset.category;

                if (typeof updateCellLabel === "function") {
                    updateCellLabel(cell);
                } else {
                    cell.setAttribute(
                        "aria-label",
                        "Empty cell. Press Enter or Space to place a selected function here."
                    );
                }

                if (data.success && data.effectTotals) {
                    updateEffectTable(data.effectTotals, data.qualityOfLife);
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
            })
            .catch(error => {
                console.error("Delete error:", error);
            });
    }

    document.body.addEventListener("mouseover", function (e) {
        const cell = e.target.closest(".gridCell");

        if (!cell) return;

        createDeleteButton(cell);
    });

    document.body.addEventListener("touchstart", function (e) {
        const cell = e.target.closest(".gridCell");

        if (!cell) return;

        createDeleteButton(cell);
    }, { passive: true });

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