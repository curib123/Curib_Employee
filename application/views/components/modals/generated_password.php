<?php
/**
 * application/views/components/modals/generated_password.php | 2026-09-21
 * One-time generated-password modal shown immediately after registration.
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
        <div class="modal-content border-0 shadow-lg password-modal">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge rounded-pill text-bg-success mb-2">Registration complete</span>
                    <h1 class="modal-title fs-4" id="generatedPasswordModalLabel">Save your password</h1>
                </div>
            </div>
            <div class="modal-body">
                <p class="text-secondary">
                    This password is shown only once. Copy it now and keep it somewhere secure.
                </p>

                <div class="generated-password-box" id="generatedPasswordValue">
                    <?= html_escape($generated_password); ?>
                </div>

                <div class="alert alert-warning mt-3 mb-0" role="alert">
                    The database stores only a secure password hash. We cannot show this same password again later.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button
                    type="button"
                    class="btn btn-primary w-100"
                    id="copyPasswordButton"
                    data-dashboard-url="<?= html_escape(site_url('dashboard')); ?>"
                >
                    Copy Password &amp; Continue
                </button>
            </div>
        </div>
    </div>
</div>
