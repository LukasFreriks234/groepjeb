document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('mouseover', function (e) {

        // Zoek de grid cell waar je muis overheen gaat
        const cell = e.target.closest('.gridCell');
        if (!cell) return;

        // Alleen een delete-knop tonen als er een functie/image in de cell staat
        if (!cell.querySelector('.functionItem') && !cell.querySelector('.gridImage')) {
            return;
        }

        // Voorkom dat er meerdere delete-knoppen in dezelfde cell komen
        if (cell.querySelector('.delete-btn')) return;

        // Maak de delete-knop aan
        const btn = document.createElement('button');
        btn.innerHTML = '✕';
        btn.className = 'delete-btn';

        cell.appendChild(btn);

        btn.onclick = function (ev) {
            ev.stopPropagation();

            // Haal de id van de grid cell op
            const id = cell.dataset.id;

            // Stuur request naar Laravel om de functie uit MySQL te verwijderen
            fetch('/remove-function', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {

                // Maak de cell visueel leeg
                cell.innerHTML = '';
                cell.classList.remove('occupied');
                cell.classList.add('available');

                // Update de effect table direct met de nieuwe totalen
                if (data.success && data.effectTotals) {
                    updateEffectTable(data.effectTotals);
                }

                // Activeer drag opnieuw voor function items
                enableDrag();
            })
            .catch(error => {
                console.error('Delete error:', error);
            });
        };
    });

});