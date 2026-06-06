const oneOff = document.getElementById('oneOff');
const recurring = document.getElementById('recurring');
const recurringFields = document.getElementById('recurringFields');
const oneOffFields = document.getElementById('oneOffFields');
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

//one Off
toggleOneOffFields();
oneOff.addEventListener('change', toggleOneOffFields);
recurring.addEventListener('change', toggleOneOffFields);

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

function toggleOneOffFields() {
    oneOffFields.style.display =
        oneOff.checked ? 'block' : 'none';
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

