document.addEventListener("DOMContentLoaded", function () {
    const relatedFunctionSelect = document.getElementById("related_function");
    const relationshipInputs = document.querySelectorAll(".relationship-effect-input");

    if (!relatedFunctionSelect || relationshipInputs.length === 0) {
        return;
    }

    function toggleRelationshipInputs() {
        const hasRelationship = relatedFunctionSelect.value !== "";

        relationshipInputs.forEach(function (input) {
            input.disabled = !hasRelationship;

            if (!hasRelationship) {
                input.value = 0;
                input.setAttribute("aria-disabled", "true");
                input.readOnly = true;
            } else {
                input.removeAttribute("aria-disabled");
                input.readOnly = false;
            }
        });
    }

    relatedFunctionSelect.addEventListener("change", toggleRelationshipInputs);

    toggleRelationshipInputs();
});