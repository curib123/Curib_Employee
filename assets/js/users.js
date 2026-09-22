/**
 * assets/js/users.js | 2026-09-22
 * Shared behavior for the user modal dialog.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const userModal = document.getElementById('userModal');

    if (!userModal) {
        return;
    }

    const form = document.getElementById('userForm');
    const modalTitle = document.getElementById('userModalLabel');
    const userIdField = document.getElementById('userId');
    const submitButton = document.getElementById('userSubmitButton');
    const fields = {
        firstname: document.getElementById('userFirstname'),
        lastname: document.getElementById('userLastname'),
        birthday: document.getElementById('userBirthday'),
        contactno: document.getElementById('userContact'),
        address: document.getElementById('userAddress'),
        email: document.getElementById('userEmail')
    };

    function resetFormState() {
        form.reset();
        userIdField.value = '0';
        modalTitle.textContent = 'Add User';
        submitButton.textContent = 'Save User';
        form.setAttribute('action', form.getAttribute('data-store-url'));
        form.setAttribute('method', 'post');
    }

    function populateForm(user) {
        userIdField.value = String(user.id || '0');
        modalTitle.textContent = 'Edit User';
        submitButton.textContent = 'Update User';

        for (const key in fields) {
            if (Object.prototype.hasOwnProperty.call(fields, key) && fields[key]) {
                fields[key].value = user[key] || '';
            }
        }

        form.setAttribute('action', form.getAttribute('data-update-url') + '/' + String(user.id || '0'));
        form.setAttribute('method', 'post');
    }

    userModal.addEventListener('hidden.bs.modal', function () {
        resetFormState();
    });

    document.querySelectorAll('[data-user-mode]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const mode = this.getAttribute('data-user-mode');
            const userId = this.getAttribute('data-user-id') || '0';

            if (mode === 'edit' && userId !== '0') {
                populateForm({
                    id: userId,
                    firstname: this.getAttribute('data-user-firstname') || '',
                    lastname: this.getAttribute('data-user-lastname') || '',
                    birthday: this.getAttribute('data-user-birthday') || '',
                    contactno: this.getAttribute('data-user-contact') || '',
                    address: this.getAttribute('data-user-address') || '',
                    email: this.getAttribute('data-user-email') || ''
                });
                return;
            }

            resetFormState();
        });
    });

    form.addEventListener('submit', function () {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
    });
});
