document.addEventListener('DOMContentLoaded', function () {

    // Check of een grid cell een functie bevat
    function cellHasFunction(cell) {
        return cell.querySelector('.functionItem') || cell.querySelector('.gridImage');
    }

    // Maak een delete-knop aan voor een gevulde grid cell
    function createDeleteButton(cell) {
        if (!cell) return;

        // Alleen een delete-knop tonen als er een functie/image in de cell staat
        if (!cellHasFunction(cell)) {
            removeDeleteButton(cell);
            return;
        }

        // Voorkom dat er meerdere delete-knoppen in dezelfde cell komen
        if (cell.querySelector('.delete-btn')) {
            return;
        }

        // Maak de delete-knop aan
        const image = cell.querySelector('.gridImage');
        console.log('CELL:', cell);
        console.log('IMAGE:', image);
        console.log('ALT:', image?.alt);
        const functionName = image?.alt || 'function';
        console.log('FUNCTION NAME:', functionName);
        const btn = document.createElement('button');
        btn.innerHTML = '✕';
        btn.className = 'delete-btn';
        btn.type = 'button';
        btn.setAttribute('aria-label', `Remove ${functionName}`);

        cell.appendChild(btn);

        // PC: klik op delete-knop
        btn.addEventListener('click', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            deleteFunctionFromCell(cell);
        });

        // Mobiel: touch op delete-knop
        btn.addEventListener('touchstart', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            deleteFunctionFromCell(cell);
        }, { passive: false });
    }

    // Verwijder delete-knop uit een cell
    function removeDeleteButton(cell) {
        const btn = cell.querySelector('.delete-btn');

        if (btn) {
            btn.remove();
        }
    }

    // Check alle grid cells opnieuw en voeg/verwijder delete-knoppen
    function refreshDeleteButtons() {
        document.querySelectorAll('.gridCell').forEach(function (cell) {
            if (cellHasFunction(cell)) {
                createDeleteButton(cell);
            } else {
                removeDeleteButton(cell);
            }
        });
    }

    // Maak refreshDeleteButtons beschikbaar voor andere scripts
    window.refreshDeleteButtons = refreshDeleteButtons;

    // Verwijder functie uit een grid cell, ook in de database
    function deleteFunctionFromCell(cell) {
        const id = cell.dataset.id;

        if (!id) {
            console.error('Grid cell id ontbreekt');
            return;
        }

        // Belangrijk voor mobiel:
        // als er nog een functie geselecteerd was, haal die selectie weg.
        // Anders kan een verwijderde functie terugkomen bij de volgende tik.
        if (typeof clearMobileSelection === 'function') {
            clearMobileSelection();
        }

        // Stuur request naar Laravel om de functie uit MySQL te verwijderen
        fetch('/remove-function', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            // Maak de cell visueel leeg
            cell.innerHTML = '';
            cell.classList.remove('occupied', 'selectedMobileCell');
            cell.classList.add('available');

            delete cell.dataset.category;

            // Update de effect table en Quality of Life direct met de nieuwe totalen
            if (data.success && data.effectTotals) {
                updateEffectTable(data.effectTotals, data.qualityOfLife);
            }

            // Delete buttons opnieuw checken
            refreshDeleteButtons();

            // Activeer drag opnieuw voor function items
            if (typeof enableDrag === 'function') {
                enableDrag();
            }

            // Activeer mobile drag opnieuw als die bestaat
            if (typeof enableMobileDrag === 'function') {
                enableMobileDrag();
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
        });
    }

    // PC: delete-knop maken bij hover
    document.body.addEventListener('mouseover', function (e) {
        const cell = e.target.closest('.gridCell');
        if (!cell) return;

        createDeleteButton(cell);
    });

    // Mobiel: delete-knop maken bij touch op een cell
    document.body.addEventListener('touchstart', function (e) {
        const cell = e.target.closest('.gridCell');
        if (!cell) return;

        createDeleteButton(cell);
    }, { passive: true });

    // Automatisch opnieuw checken als de grid verandert
    // Bijvoorbeeld na drag/drop of mobiel wisselen.
    const grid = document.querySelector('.metropolisGrid');

    if (grid) {
        const observer = new MutationObserver(function () {
            refreshDeleteButtons();
        });

        observer.observe(grid, {
            childList: true,
            subtree: true
        });
    }

    // Bij pagina-load meteen delete-knoppen maken voor gevulde cells
    refreshDeleteButtons();
});