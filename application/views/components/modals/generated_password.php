<?php
/**
 * application/views/components/modals/generated_password.php | 2026-09-22
 * One-time Bootstrap generated-password modal shown immediately after registration.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div
    class="modal fade"
    id="generatedPasswordModal"
    tabindex="-1"
    aria-labelledby="generatedPasswordModalLabel"
    aria-hidden="true"
    data-auto-show="1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-body-tertiary px-4 pt-4 pb-3">
                <div>
                    <span class="badge rounded-pill text-bg-success mb-2">Registration complete</span>
                    <h1 class="modal-title fs-4" id="generatedPasswordModalLabel">Save your generated password</h1>
                </div>
            </div>

            <div class="modal-body p-4">
                <p class="text-secondary">
                    Copy this password now. You will use it with your email on the Login page.
                </p>

                <div class="alert alert-light border rounded-3 p-3 text-center fw-bold font-monospace text-break fs-5 mb-0" id="generatedPasswordValue">
                    <?= html_escape($generated_password); ?>
                </div>

                <div class="alert alert-warning mt-3 mb-0" role="alert">
                    This password is shown only once. Only its secure hash is stored in the database.
                </div>
            </div>

            <div class="modal-footer border-0 bg-body-tertiary p-4 pt-3">
                <button
                    type="button"
                    class="btn btn-primary btn-lg w-100"
                    id="copyPasswordButton"
                    data-login-url="<?= html_escape(site_url('login')); ?>"
                >
                    Copy Password &amp; Go to Login
                </button>
            </div>
        </div>
    </div>
</div>
