/**
 * assets/js/app.js | 2026-09-21
 * Shared Bootstrap modal, validation, and first-login prompt behavior.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            const targetSelector = this.getAttribute('data-bs-target');

            if (!targetSelector) {
                return;
            }

            const target = document.querySelector(targetSelector);

            if (!target || typeof window.bootstrap === 'undefined' || !window.bootstrap.Modal) {
                return;
            }

            event.preventDefault();
            const instance = window.bootstrap.Modal.getOrCreateInstance(target);
            instance.show();
        });
    });

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
