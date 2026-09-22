<?php
/**
 * application/views/components/modals/change_password.php | 2026-09-21
 * One-time first-login password change modal with Change and Skip actions.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$show_first_login_password_prompt = !empty($show_first_login_password_prompt);
$validation_errors = isset($validation_errors) && is_array($validation_errors)
    ? $validation_errors
    : array();
$has_password_errors = !empty($validation_errors);
?>
<div
    class="modal fade"
    id="firstLoginPasswordModal"
    tabindex="-1"
    aria-labelledby="firstLoginPasswordModalLabel"
    aria-hidden="true"
    data-first-login-password-modal="<?= $show_first_login_password_prompt && !$has_password_errors ? '1' : '0'; ?>"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <?= form_open(
                'password/change',
                array(
                    'id' => 'changePasswordForm',
                    'class' => 'needs-validation',
                    'novalidate' => 'novalidate'
                )
            ); ?>
                <div class="modal-header border-0 bg-body-tertiary px-4 pt-4 pb-3">
                    <div>
                        <span class="badge rounded-pill text-bg-primary mb-2">First login</span>
                        <h2 class="modal-title fs-4" id="firstLoginPasswordModalLabel">
                            Change your generated password?
                        </h2>
                    </div>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-primary border-0 mb-4" role="alert">
                        <div class="fw-semibold mb-1">Secure your account</div>
                        <div class="small">Replace the generated password now, or skip once and continue using it. This prompt will not appear again after either choice.</div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="new_password"
                            name="new_password"
                            minlength="12"
                            maxlength="20"
                            autocomplete="off"
                            required
                        >
                        <div class="form-text">
                            At least 12 characters and maximum of 20 characters with uppercase, lowercase, a number, and a symbol.
                        </div>
                        <div class="invalid-feedback">Enter a strong new password.</div>
                    </div>

                    <div>
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="confirm_password"
                            name="confirm_password"
                            minlength="12"
                            maxlength="20"
                            autocomplete="off"
                            required
                        >
                        <div class="invalid-feedback">Confirm your new password.</div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-body-tertiary p-4 pt-3">
                    <button
                        type="submit"
                        class="btn btn-outline-secondary px-4"
                        form="skipPasswordForm"
                    >
                        Skip
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        Change Password
                    </button>
                </div>
            <?= form_close(); ?>

            <?= form_open('password/skip', array('id' => 'skipPasswordForm')); ?>
            <?= form_close(); ?>
        </div>
    </div>
</div>
