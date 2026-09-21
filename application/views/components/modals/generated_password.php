<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal" id="generatedPasswordModal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="generatedPasswordModalLabel" data-static="1">
    <div class="modal-dialog">
        <section class="modal-card">
            <header class="modal-header">
                <div><span class="badge badge-success">Registration complete</span><h1 id="generatedPasswordModalLabel">Save your generated password</h1></div>
            </header>
            <div class="modal-body stack">
                <p class="text-muted">Copy this password now. You will use it with your email on the login page.</p>
                <div class="generated-password" id="generatedPasswordValue"><?= html_escape($generated_password); ?></div>
                <div class="notice notice-warning">This password is shown only once. Only its secure hash is stored in the database.</div>
            </div>
            <footer class="modal-footer">
                <button class="btn btn-primary btn-block" type="button" id="copyPasswordButton" data-login-url="<?= html_escape(site_url('login')); ?>">Copy password &amp; go to login</button>
            </footer>
        </section>
    </div>
</div>
