document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('mouseover', function (e) {

        // ✅ juiste class
        const cell = e.target.closest('.gridCell');
        if (!cell) return;

        // ✅ check juiste inhoud
        if (!cell.querySelector('.functionItem') && !cell.querySelector('.gridImage')) {
            return;
        }

        // knop al aanwezig?
        if (cell.querySelector('.delete-btn')) return;

        const btn = document.createElement('button');
        btn.innerHTML = '✕';
        btn.className = 'delete-btn';

        cell.appendChild(btn);

        btn.onclick = function (ev) {
            ev.stopPropagation();

            const id = cell.dataset.id;

            fetch('/remove-function', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: id })
            })
            .then(() => {

                // 🔥 cel leeg maken
                cell.innerHTML = '';
                cell.classList.remove('occupied');
                cell.classList.add('available');

                // 🔥 drag opnieuw activeren
                enableDrag();
            });
        };
    });

});