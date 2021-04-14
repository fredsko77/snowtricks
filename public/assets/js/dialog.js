var dialogs = document.querySelectorAll('[aria-toggle]');

if (dialogs !== undefined) {
    dialogs.forEach((dialog) => {
        dialog.addEventListener('click', () => {
            const target = dialog.getAttribute('aria-toggle');
            let targetElement = document.querySelector(`[aria-dialog="${target}"]`);
            targetElement.classList.replace('fade', 'show');
        });
    });
}

var closeButtons = document.querySelectorAll('[aria-action]');

if (closeButtons !== undefined) {
    closeButtons.forEach((closeButton) => {
        if (closeButton.getAttribute('aria-action') === 'close') {
            closeButton.addEventListener('click', () => {
                document.querySelectorAll('[aria-dialog]').forEach((dialog) => {
                    if (dialog.classList.contains('show')) {
                        dialog.classList.replace('show', 'fade');
                    }
                });
            });
        }
    });
}