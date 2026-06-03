document.addEventListener('DOMContentLoaded', () => {
    const oneOff = document.getElementById('oneOff');
    const recurring = document.getElementById('recurring');
    const recurringFields = document.getElementById('recurringFields');

    function toggleRecurringFields() {
        recurringFields.style.display =
            recurring.checked ? 'block' : 'none';
    }

    oneOff.addEventListener('change', toggleRecurringFields);
    recurring.addEventListener('change', toggleRecurringFields);

    // Initial state
    toggleRecurringFields();
});


const dynamic = document.getElementById('dynamic');
const dynamicEventBox = document.getElementById('dynamicEventBox');

function toggleDynamicEvent() {
    dynamicEventBox.style.display =
        dynamic.checked ? 'block' : 'none';
}

dynamic.addEventListener('change', toggleDynamicEvent);

toggleDynamicEvent();