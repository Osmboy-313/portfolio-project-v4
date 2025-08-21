export function openCloseModal() {
    const overlay = document.querySelector("#overlay");

    if (!overlay) return;

    document.addEventListener('click', (e) => {
        const openBtn = e.target.closest('[data-modal-target]');
        const closeBtn = e.target.closest('[data-modal-close]');

        // Open modal
        if (openBtn) {
            e.preventDefault();
            const modal = document.querySelector(openBtn.dataset.modalTarget);
            if (!modal) return;

            console.log('Open button:', openBtn);

            // Inject dynamic content
            if (openBtn.dataset.title) {
                const titleEl = modal.querySelector('.modal__message-title');
                if (titleEl) titleEl.innerText = openBtn.dataset.title;
            }

            if (openBtn.dataset.label) {
                const label = modal.querySelector('label');
                if (label) label.innerText = openBtn.dataset.label;
            }

            if (openBtn.dataset.placeholder) {
                const input = modal.querySelector('input[type="text"]');
                if (input) input.placeholder = openBtn.dataset.placeholder;
            }

            if (openBtn.dataset.message) {
                const msg = modal.querySelector('.modal__message-text');
                if (msg) msg.innerText = openBtn.dataset.message;
            }

            if (openBtn.dataset.form) {
                const form = modal.querySelector('form');
                if (form) form.id = openBtn.dataset.form;
            }

            if (openBtn.dataset.formAction) {
                const form = modal.querySelector('form');
                if (form) form.action = openBtn.dataset.formAction;
            }

            if (openBtn.dataset.input) {
                const input = modal.querySelector('input[type=text]');
                if (input){
                    input.classList.add(openBtn.dataset.input);
                    input.name = openBtn.dataset.input;
                }
            }

            if (openBtn.dataset.deleteInput) {
                const input = modal.querySelector('input[type=submit]');
                if (input){
                    input.classList.add(openBtn.dataset.deleteInput);
                    input.name = openBtn.dataset.deleteInput;
                }
            }

            if (openBtn.dataset.deleteId) {
                const input = modal.querySelector('input.id-field');
                if (input){
                    input.value = openBtn.dataset.deleteId;
                    input.name = 'delete-id';
                }
            }

            if (openBtn.dataset.redirect) {
                const input = modal.querySelector('input.url-field');
                if (input){
                    input.value = openBtn.dataset.redirect;
                    input.name = 'redirect';
                }
            }

            // Clear input and errors
            const inputs = modal.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                input.value = '';
            });

            // Clear any existing errors
            const errorBoxes = modal.querySelectorAll('.modal__input-box');
            errorBoxes.forEach(box => {
                box.classList.remove('error');
                const errorBox = box.querySelector('.error-box');
                if (errorBox) errorBox.textContent = '';
            });

            // Clear alerts
            const alerts = modal.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.className = 'alert de-active';
            });

            modal.classList.add('active');
            overlay.classList.add('active');
        }

        // Close modal
        if (closeBtn) {
            e.preventDefault();
            const modal = closeBtn.closest('.modal');
            if (!modal) return;
            modal.classList.remove('active');
            overlay.classList.remove('active');
        }
    });

    // Close on overlay click
    overlay.addEventListener('click', () => {
        document.querySelectorAll('.modal.active').forEach(modal => {
            modal.classList.remove('active');
        });
        overlay.classList.remove('active');
    });

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('active');
        overlay.classList.remove('active');
    }

    return { closeModal };
}