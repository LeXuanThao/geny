document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const spinner = document.querySelector('.spinner');
    const submitButton = document.querySelector('button[type="submit"]');

    form.addEventListener('submit', function () {
        spinner.classList.remove('hidden');
        submitButton.disabled = true;
    });

    form.addEventListener('input', function () {
        const email = form.querySelector('input[name="email"]').value;
        const password = form.querySelector('input[name="password"]').value;

        if (email && password.length >= 8) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    });
});
