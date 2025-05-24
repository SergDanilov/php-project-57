import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Обработчик для data-method и data-confirm
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-method][data-confirm]').forEach(link => {
        link.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm(this.dataset.confirm)) {
                        return;
                }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.href;
            form.style.display = 'none';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = this.dataset.method;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

            form.appendChild(methodInput);
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
