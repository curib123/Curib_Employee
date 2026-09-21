/**
 * assets/js/app.js | 2026-09-21
 * Shared Bootstrap modal, validation, and first-login prompt behavior.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });

    const alertModalElement = document.getElementById('alertModal');

    if (alertModalElement && alertModalElement.dataset.autoShow === '1') {
        const alertModal = bootstrap.Modal.getOrCreateInstance(alertModalElement);
        const reopenModalId = alertModalElement.dataset.reopenModal;

        if (reopenModalId) {
            alertModalElement.addEventListener('hidden.bs.modal', function () {
                const target = document.getElementById(reopenModalId);

                if (target) {
                    bootstrap.Modal.getOrCreateInstance(target).show();
                }
            }, { once: true });
        }

        alertModal.show();
        return;
    }

    const passwordModalElement = document.getElementById('firstLoginPasswordModal');

    if (
        passwordModalElement &&
        passwordModalElement.dataset.firstLoginPasswordModal === '1'
    ) {
        bootstrap.Modal.getOrCreateInstance(passwordModalElement).show();
    }
});
