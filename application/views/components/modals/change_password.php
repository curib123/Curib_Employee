<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$show_first_login_password_prompt = !empty($show_first_login_password_prompt);
$validation_errors = isset($validation_errors) && is_array($validation_errors) ? $validation_errors : array();
$has_password_errors = !empty($validation_errors);
?>
<div class="modal" id="firstLoginPasswordModal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="firstLoginPasswordModalLabel" data-auto-show="<?= $show_first_login_password_prompt && !$has_password_errors ? '1' : '0'; ?>" data-static="1">
    <div class="modal-dialog">
        <section class="modal-card">
            <?= form_open('password/change', array('id' => 'changePasswordForm', 'class' => 'form', 'novalidate' => 'novalidate')); ?>
                <header class="modal-header"><div><span class="badge">First login</span><h2 id="firstLoginPasswordModalLabel">Change your generated password?</h2></div></header>
                <div class="modal-body stack">
                    <p class="text-muted">You can replace the generated password now, or skip this one-time prompt and continue using it. After either choice, this prompt will not appear again.</p>
                    <div class="field">
                        <label class="label" for="new_password">New password</label>
                        <input class="input" type="password" id="new_password" name="new_password" minlength="12" maxlength="72" autocomplete="new-password" placeholder="At least 12 characters" required>
                        <span class="field-error">Enter a strong new password.</span>
                        <p class="help">Use uppercase, lowercase, a number, a symbol, and no spaces.</p>
                    </div>
                    <div class="field">
                        <label class="label" for="confirm_password">Confirm new password</label>
                        <input class="input" type="password" id="confirm_password" name="confirm_password" minlength="12" maxlength="72" autocomplete="new-password" placeholder="Repeat the new password" required>
                        <span class="field-error">Confirm your new password.</span>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button class="btn btn-secondary" type="submit" form="skipPasswordForm" data-submitting-text="Continuing…">Skip</button>
                    <button class="btn btn-primary" type="submit" data-submitting-text="Changing password…">Change password</button>
                </footer>
            <?= form_close(); ?>
            <?= form_open('password/skip', array('id' => 'skipPasswordForm', 'class' => 'form')); ?><?= form_close(); ?>
        </section>
    </div>
</div>
