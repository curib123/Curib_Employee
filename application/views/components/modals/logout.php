<?php
/**
 * application/views/components/modals/logout.php | 2026-09-21
 * Shared logout confirmation modal.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div
    class="modal fade"
    id="logoutModal"
    tabindex="-1"
    aria-labelledby="logoutModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <?= form_open('logout'); ?>
                <div class="modal-header border-0 bg-body-tertiary px-4 py-3">
                    <h2 class="modal-title fs-5" id="logoutModalLabel">Sign out?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary mb-0">Your current session will end and you will return to the login page.</p>
                </div>
                <div class="modal-footer border-0 bg-body-tertiary px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Stay signed in</button>
                    <button type="submit" class="btn btn-danger px-4">Logout</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
