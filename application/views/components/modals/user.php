<?php
/**
 * application/views/components/modals/user.php | 2026-09-22
 * User upsert modal used for both create and edit flows.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div
    class="modal fade"
    id="userModal"
    tabindex="-1"
    aria-labelledby="userModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <?= form_open('users/store', array('id' => 'userForm', 'class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                <div class="modal-header bg-body-tertiary px-4 py-3">
                    <div>
                        <p class="text-uppercase small fw-semibold text-primary mb-1">User record</p>
                        <h2 class="modal-title fs-5" id="userModalLabel">Add User</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" id="userId" name="user_id" value="0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="userFirstname" class="form-label">First Name</label>
                            <input type="text" class="form-control form-control-lg" id="userFirstname" name="firstname" maxlength="100" required>
                            <div class="invalid-feedback">First name is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="userLastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control form-control-lg" id="userLastname" name="lastname" maxlength="100" required>
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="userBirthday" class="form-label">Birthday</label>
                            <input type="date" class="form-control form-control-lg" id="userBirthday" name="birthday" max="<?= html_escape(date('Y-m-d')); ?>" required>
                            <div class="invalid-feedback">Birthday is required.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="userContact" class="form-label">Contact No.</label>
                            <input type="tel" class="form-control form-control-lg" id="userContact" name="contactno" maxlength="20" required>
                            <div class="invalid-feedback">Contact number is required.</div>
                        </div>

                        <div class="col-12">
                            <label for="userAddress" class="form-label">Address</label>
                            <textarea class="form-control form-control-lg" id="userAddress" name="address" rows="3" maxlength="255" required></textarea>
                            <div class="invalid-feedback">Address is required.</div>
                        </div>

                        <div class="col-12">
                            <label for="userEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-lg" id="userEmail" name="email" maxlength="190" required>
                            <div class="invalid-feedback">Enter a valid email address.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="userSubmitButton">Save User</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
