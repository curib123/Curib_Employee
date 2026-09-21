/**
 * assets/js/employees.js | 2026-09-21
 * Bootstrap modal behavior and client-side validation for Employee CRUD.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const employeeModalElement = document.getElementById('employeeModal');
    const deleteModalElement = document.getElementById('deleteModal');
    const infoModalElement = document.getElementById('infoModal');
    const alertModalElement = document.getElementById('alertModal');
    const employeeForm = document.getElementById('employeeForm');
    const deleteForm = document.getElementById('deleteForm');

    const employeeModal = bootstrap.Modal.getOrCreateInstance(employeeModalElement);
    const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalElement);
    const infoModal = bootstrap.Modal.getOrCreateInstance(infoModalElement);
    const alertModal = bootstrap.Modal.getOrCreateInstance(alertModalElement);

    const modalTitle = document.getElementById('employeeModalLabel');
    const submitButton = document.getElementById('employeeSubmitButton');

    const fields = {
        firstname: document.getElementById('firstname'),
        lastname: document.getElementById('lastname'),
        birthday: document.getElementById('birthday'),
        address: document.getElementById('address'),
        contactno: document.getElementById('contactno')
    };

    function endpoint(baseUrl, id) {
        return baseUrl.replace(/\/+$/, '') + '/' + encodeURIComponent(id);
    }

    function configureCreateMode(resetForm) {
        if (resetForm) {
            employeeForm.reset();
        }

        employeeForm.classList.remove('was-validated');
        employeeForm.action = employeeForm.dataset.storeUrl;
        modalTitle.textContent = 'Add Employee';
        submitButton.textContent = 'Save Employee';
    }

    function configureUpdateMode(id) {
        employeeForm.classList.remove('was-validated');
        employeeForm.action = endpoint(employeeForm.dataset.updateUrl, id);
        modalTitle.textContent = 'Edit Employee';
        submitButton.textContent = 'Update Employee';
    }

    function fillEmployeeForm(button) {
        fields.firstname.value = button.dataset.firstname || '';
        fields.lastname.value = button.dataset.lastname || '';
        fields.birthday.value = button.dataset.birthday || '';
        fields.address.value = button.dataset.address || '';
        fields.contactno.value = button.dataset.contactno || '';
    }

    function formatBirthday(value) {
        const date = new Date(value + 'T00:00:00');

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    document.querySelectorAll('[data-action="add-employee"]').forEach(function (button) {
        button.addEventListener('click', function () {
            configureCreateMode(true);
            employeeModal.show();
        });
    });

    document.querySelectorAll('[data-action="edit"]').forEach(function (button) {
        button.addEventListener('click', function () {
            configureUpdateMode(button.dataset.id);
            fillEmployeeForm(button);
            employeeModal.show();
        });
    });

    document.querySelectorAll('[data-action="info"]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('infoId').textContent = button.dataset.id || '';
            document.getElementById('infoName').textContent =
                (button.dataset.firstname || '') + ' ' + (button.dataset.lastname || '');
            document.getElementById('infoBirthday').textContent =
                formatBirthday(button.dataset.birthday || '');
            document.getElementById('infoAge').textContent =
                (button.dataset.age || '0') + ' years old';
            document.getElementById('infoAddress').textContent = button.dataset.address || '';
            document.getElementById('infoContactno').textContent = button.dataset.contactno || '';

            infoModal.show();
        });
    });

    document.querySelectorAll('[data-action="delete"]').forEach(function (button) {
        button.addEventListener('click', function () {
            const fullName =
                (button.dataset.firstname || '') + ' ' + (button.dataset.lastname || '');

            document.getElementById('deleteEmployeeName').textContent = fullName.trim();
            deleteForm.action = endpoint(deleteForm.dataset.deleteUrl, button.dataset.id);
            deleteModal.show();
        });
    });

    employeeForm.addEventListener('submit', function (event) {
        if (!employeeForm.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        employeeForm.classList.add('was-validated');
    });

    const validationMode = employeeModalElement.dataset.validationMode;
    const validationId = employeeModalElement.dataset.validationId;

    if (validationMode === 'update' && validationId) {
        configureUpdateMode(validationId);
    } else if (validationMode === 'create') {
        configureCreateMode(false);
    }

    const shouldShowAlert = alertModalElement.dataset.autoShow === '1';
    const shouldReopenUpsert = alertModalElement.dataset.reopenUpsert === '1';

    if (shouldShowAlert) {
        if (shouldReopenUpsert) {
            alertModalElement.addEventListener('hidden.bs.modal', function () {
                employeeModal.show();
            }, { once: true });
        }

        alertModal.show();
    } else if (validationMode === 'create' || validationMode === 'update') {
        employeeModal.show();
    }
});
