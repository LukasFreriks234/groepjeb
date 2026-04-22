document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('mouseover', function (e) {

        const cell = e.target.closest('.grid-cell');
        if (!cell) return;

        // alleen als er iets in zit
        if (!cell.querySelector('.function-item') && !cell.querySelector('.grid-image')) {
            return;
        }

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

                // 🔥 reset cell
                cell.innerHTML = '';
                cell.classList.remove('occupied');
                cell.classList.add('available');

                // 🔥 drag opnieuw activeren
                if (typeof enableDrag === "function") {
                    enableDrag();
                }
            });
        };
    });

});