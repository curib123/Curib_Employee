<?php
/**
 * application/views/components/modals/employee.php | 2026-09-21
 * Employee Upsert, Delete confirmation, and Info modal components.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$old_input = isset($old_input) && is_array($old_input) ? $old_input : array();
$validation_context = isset($validation_context) && is_array($validation_context)
    ? $validation_context
    : array();

$validation_mode = isset($validation_context['mode']) ? $validation_context['mode'] : '';
$validation_id = isset($validation_context['id']) ? (int) $validation_context['id'] : 0;
?>
<div
    class="modal fade"
    id="employeeModal"
    tabindex="-1"
    aria-labelledby="employeeModalLabel"
    aria-hidden="true"
    data-validation-mode="<?= html_escape($validation_mode); ?>"
    data-validation-id="<?= $validation_id; ?>"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <?= form_open(
                'employees/store',
                array(
                    'id' => 'employeeForm',
                    'class' => 'needs-validation',
                    'novalidate' => 'novalidate',
                    'data-store-url' => site_url('employees/store'),
                    'data-update-url' => site_url('employees/update')
                )
            ); ?>
                <div class="modal-header bg-body-tertiary px-4 py-3">
                    <div>
                        <p class="text-uppercase small fw-semibold text-primary mb-1">Employee record</p>
                        <h2 class="modal-title fs-5" id="employeeModalLabel">Add Employee</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">First Name</label>
                            <input
                                type="text"
                                class="form-control form-control-lg"
                                id="firstname"
                                name="firstname"
                                maxlength="100"
                                autocomplete="given-name"
                                value="<?= html_escape(isset($old_input['firstname']) ? $old_input['firstname'] : ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">First name is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input
                                type="text"
                                class="form-control form-control-lg"
                                id="lastname"
                                name="lastname"
                                maxlength="100"
                                autocomplete="family-name"
                                value="<?= html_escape(isset($old_input['lastname']) ? $old_input['lastname'] : ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="birthday" class="form-label">Birthday</label>
                            <input
                                type="date"
                                class="form-control form-control-lg"
                                id="birthday"
                                name="birthday"
                                min="1900-01-01"
                                max="<?= html_escape($today); ?>"
                                value="<?= html_escape(isset($old_input['birthday']) ? $old_input['birthday'] : ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Enter a valid birthday that is not in the future.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="contactno" class="form-label">Contact No.</label>
                            <input
                                type="tel"
                                class="form-control form-control-lg"
                                id="contactno"
                                name="contactno"
                                maxlength="20"
                                pattern="[0-9+()\-\s]{7,20}"
                                inputmode="tel"
                                autocomplete="tel"
                                value="<?= html_escape(isset($old_input['contactno']) ? $old_input['contactno'] : ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Enter a valid 7 to 20 character contact number.</div>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea
                                class="form-control form-control-lg"
                                id="address"
                                name="address"
                                rows="3"
                                maxlength="255"
                                autocomplete="street-address"
                                required
                            ><?= html_escape(isset($old_input['address']) ? $old_input['address'] : ''); ?></textarea>
                            <div class="invalid-feedback">Address is required.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="employeeSubmitButton">Save Employee</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="deleteEmployeeModal"
    tabindex="-1"
    aria-labelledby="deleteEmployeeModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <?= form_open(
                'employees/delete/0',
                array(
                    'id' => 'deleteEmployeeForm',
                    'data-delete-url' => site_url('employees/delete')
                )
            ); ?>
                <div class="modal-header bg-body-tertiary px-4 py-3">
                    <h2 class="modal-title fs-5" id="deleteEmployeeModalLabel">Delete employee?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0">
                        You are about to permanently delete
                        <strong id="deleteEmployeeName">this employee</strong>.
                        This cannot be undone.
                    </p>
                </div>
                <div class="modal-footer bg-body-tertiary px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Employee</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="employeeInfoModal"
    tabindex="-1"
    aria-labelledby="employeeInfoModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-body-tertiary px-4 py-3">
                <div>
                    <p class="text-uppercase small fw-semibold text-primary mb-1">Employee profile</p>
                    <h2 class="modal-title fs-5" id="employeeInfoModalLabel">Employee Information</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <dl class="row g-2 mb-0">
                    
                    <dt class="col-sm-4 text-secondary small text-uppercase">ID</dt>
                    <dd class="col-sm-8" id="infoId"></dd>

                    <dt class="col-sm-4 text-secondary small text-uppercase">Name</dt>
                    <dd class="col-sm-8" id="infoName"></dd>

                    <dt class="col-sm-4 text-secondary small text-uppercase">Birthday</dt>
                    <dd class="col-sm-8" id="infoBirthday"></dd>

                    <dt class="col-sm-4 text-secondary small text-uppercase">Age</dt>
                    <dd class="col-sm-8" id="infoAge"></dd>

                    <dt class="col-sm-4 text-secondary small text-uppercase">Address</dt>
                    <dd class="col-sm-8 text-break" id="infoAddress"></dd>

                    <dt class="col-sm-4 text-secondary small text-uppercase">Contact No.</dt>
                    <dd class="col-sm-8" id="infoContactno"></dd>
                </dl>
            </div>
            <div class="modal-footer bg-body-tertiary px-4 py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>