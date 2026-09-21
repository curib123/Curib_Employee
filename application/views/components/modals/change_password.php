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
        <div class="modal-content border-0 shadow-lg">
            <?= form_open(
                'password/change',
                array(
                    'id' => 'changePasswordForm',
                    'class' => 'needs-validation',
                    'novalidate' => 'novalidate'
                )
            ); ?>
                <div class="modal-header border-0 pb-0">
                    <div>
                        <span class="badge rounded-pill text-bg-primary mb-2">First login</span>
                        <h2 class="modal-title fs-4" id="firstLoginPasswordModalLabel">
                            Change your generated password?
                        </h2>
                    </div>
                </div>

                <div class="modal-body">
                    <p class="text-secondary">
                        You can replace the generated password now, or skip this once and continue using it.
                        This prompt will not appear again after either choice.
                    </p>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input
                            type="password"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                            minlength="12"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                        <div class="form-text">
                            At least 12 characters with uppercase, lowercase, a number, and a symbol.
                        </div>
                        <div class="invalid-feedback">Enter a strong new password.</div>
                    </div>

                    <div>
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input
                            type="password"
                            class="form-control"
                            id="confirm_password"
                            name="confirm_password"
                            minlength="12"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                        <div class="invalid-feedback">Confirm your new password.</div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button
                        type="submit"
                        class="btn btn-light"
                        form="skipPasswordForm"
                    >
                        Skip
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Change Password
                    </button>
                </div>
            <?= form_close(); ?>

            <?= form_open('password/skip', array('id' => 'skipPasswordForm')); ?>
            <?= form_close(); ?>
        </div>
    </div>
</div>
