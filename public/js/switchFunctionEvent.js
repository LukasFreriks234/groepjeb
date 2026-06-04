document.querySelectorAll('.tabButton').forEach(button => {
    button.addEventListener('click', () => {
        const target = button.dataset.target;

        document.querySelectorAll('.tabButton').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');

        document.querySelectorAll('.tabContent').forEach(section => {
            section.style.display = 'none';
        });

         document.getElementById(target).style.display = 'block';
    });
});