const oneOff = document.getElementById('oneOff');
const recurring = document.getElementById('recurring');
const recurringFields = document.getElementById('recurringFields');
const dynamic = document.getElementById('dynamic');
const dynamicEventBox = document.getElementById('dynamicEventBox');
const recurrencePattern = document.querySelector('select[name="recurrencePattern"]');
const dailyFields = document.getElementById("dailyFields");
const weeklyFields = document.getElementById("weeklyFields");
const monthlyFields = document.getElementById("monthlyFields");
const yearlyFields = document.getElementById("yearlyFields");

const eachRadio = document.getElementById("each");
const onTheRadio = document.getElementById("onThe");

const eachFields = document.getElementById("eachFields");
const onTheFields = document.getElementById("onTheFields");

// recurring
toggleRecurringFields();
oneOff.addEventListener('change', toggleRecurringFields);
recurring.addEventListener('change', toggleRecurringFields);

//dynamic
dynamic.addEventListener('change', toggleDynamicEvent);
toggleDynamicEvent();

//daily
toggleDailyFields();
recurrencePattern.addEventListener("change", toggleDailyFields);

//weekly
toggleWeeklyFields();
recurrencePattern.addEventListener("change", toggleWeeklyFields);

//monthly
toggleMonthlyFields();
recurrencePattern.addEventListener("change", toggleMonthlyFields);

//yearly
toggleYearlyFields();
recurrencePattern.addEventListener("change", toggleYearlyFields);

//on the or each
toggleMonthlyTypeFields();
eachRadio.addEventListener("change", toggleMonthlyTypeFields);
onTheRadio.addEventListener("change", toggleMonthlyTypeFields);


function toggleRecurringFields() {
    recurringFields.style.display =
        recurring.checked ? 'block' : 'none';
}

function toggleDynamicEvent() {
    dynamicEventBox.style.display =
        dynamic.checked ? 'block' : 'none';
}

function toggleDailyFields() {
    if (recurrencePattern.value === "daily") {
        dailyFields.style.display = "block";
    } else {
        dailyFields.style.display = "none";
    }
}

function toggleWeeklyFields() {
    if (recurrencePattern.value === "weekly") {
        weeklyFields.style.display = "block";
    } else {
        weeklyFields.style.display = "none";
    }
}

function toggleMonthlyFields() {
    if (recurrencePattern.value === "monthly") {
        monthlyFields.style.display = "block";
    } else {
        monthlyFields.style.display = "none";
    }
}

function toggleYearlyFields() {
    if (recurrencePattern.value === "yearly") {
        yearlyFields.style.display = "block";
    } else {
        yearlyFields.style.display = "none";
    }
}

function toggleMonthlyTypeFields() {
    eachFields.style.display =
        eachRadio.checked ? "block" : "none";

    onTheFields.style.display =
        onTheRadio.checked ? "block" : "none";
}

const selectedRoute = [];
const hiddenRouteInput = document.getElementById("routeCells");

document.querySelectorAll(".miniGridCell").forEach((cell) => {

    cell.setAttribute("role", "gridcell");
    cell.setAttribute("tabindex", "0");
    cell.setAttribute("aria-selected", "false");

    cell.addEventListener("click", () => toggleCell(cell));
    cell.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            toggleCell(cell);
        }
    });
});

function toggleCell(cell) {

    const gridId = cell.dataset.gridId;
    const index = selectedRoute.indexOf(gridId);

    const isSelected = index > -1;

    if (isSelected) {
        removeFromRoute(cell, gridId, index);
    } else {
        addToRoute(cell, gridId);
    }

    updateRoute();
    refreshNumbers();
}

function addToRoute(cell, gridId) {

    selectedRoute.push(gridId);

    cell.classList.add("selected", "routeSelected");

    cell.setAttribute("aria-selected", "true");
    cell.setAttribute(
        "aria-label",
        `Grid cell ${gridId} selected as route step ${selectedRoute.length}`
    );

    cell.dataset.order = selectedRoute.length;
}

function removeFromRoute(cell, gridId, index) {

    selectedRoute.splice(index, 1);

    cell.classList.remove("selected", "routeSelected");

    cell.setAttribute("aria-selected", "false");
    cell.setAttribute(
        "aria-label",
        `Grid cell ${gridId} removed from route`
    );

    cell.dataset.order = "";
}

function updateRoute() {
    hiddenRouteInput.value = JSON.stringify(selectedRoute);
}

function refreshNumbers() {

    document.querySelectorAll(".miniGridCell.routeSelected").forEach((cell) => {

        const id = cell.dataset.gridId;
        const order = selectedRoute.indexOf(id) + 1;

        cell.dataset.order = order;

        cell.setAttribute(
            "aria-label",
            `Grid cell ${id} is step ${order} in route`
        );
    });
}