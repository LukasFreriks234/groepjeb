console.log("DELETE HOVER ACTIVE");

// wacht tot grid cells bestaan
const interval = setInterval(() => {

    const cells = document.querySelectorAll('.grid-cell');

    if (cells.length === 0) return;

    console.log("cells ready:", cells.length);

    clearInterval(interval); // stop zodra gevonden

    cells.forEach(cell => {

        // alleen als er een functie in zit (image)
        if (!cell.querySelector('img')) return;

        // knop maken
        const btn = document.createElement('button');
        btn.innerText = '❌';
        btn.className = 'delete-btn';

        btn.style.position = 'absolute';
        btn.style.top = '2px';
        btn.style.right = '2px';
        btn.style.display = 'none';
        btn.style.zIndex = '9999';
        btn.style.cursor = 'pointer';

        cell.style.position = 'relative';

        // 👉 HOVER LOGIC (dit wilde je)
        cell.addEventListener('mouseenter', () => {
            btn.style.display = 'block';
        });

        cell.addEventListener('mouseleave', () => {
            btn.style.display = 'none';
        });

        // 👉 DELETE LOGIC
        btn.onclick = function (e) {
            e.stopPropagation();

            const id = cell.getAttribute('data-id');

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
                cell.innerHTML = '';
            });
        };

        cell.appendChild(btn);
    });

}, 200);