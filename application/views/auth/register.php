<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$flash = isset($flash) && is_array($flash) ? $flash : array();
$validation_errors = isset($validation_errors) && is_array($validation_errors) ? $validation_errors : array();
$old_input = isset($old_input) && is_array($old_input) ? $old_input : array();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Register | Curib Employee</title>
    <link rel="stylesheet" href="<?= html_escape(base_url('assets/css/app.css')); ?>">
</head>
<body>
<main class="auth-shell">
    <section class="auth-card" aria-labelledby="registerTitle">
        <aside class="auth-side">
            <div>
                <span class="brand-mark" aria-hidden="true">C</span>
                <h1>Create your account.</h1>
                <p>Complete your employee profile. A strong password is generated securely after registration.</p>
            </div>
            <p>The generated password is shown once and is never stored as plaintext.</p>
        </aside>
        <div class="auth-main">
            <div class="row-between">
                <div>
                    <p class="eyebrow">Registration</p>
                    <h2 id="registerTitle">Your information</h2>
                    <p class="text-muted">All fields are required.</p>
                </div>
                <a class="btn btn-secondary" href="<?= html_escape(site_url('login')); ?>">Back to login</a>
            </div>

            <?= form_open('register', array('class' => 'form form-grid', 'novalidate' => 'novalidate')); ?>
                <div class="field">
                    <label class="label" for="firstname">First name</label>
                    <input class="input" type="text" id="firstname" name="firstname" maxlength="100" autocomplete="given-name" value="<?= html_escape(isset($old_input['firstname']) ? $old_input['firstname'] : ''); ?>" placeholder="First name" required>
                    <span class="field-error">First name is required.</span>
                </div>
                <div class="field">
                    <label class="label" for="lastname">Last name</label>
                    <input class="input" type="text" id="lastname" name="lastname" maxlength="100" autocomplete="family-name" value="<?= html_escape(isset($old_input['lastname']) ? $old_input['lastname'] : ''); ?>" placeholder="Last name" required>
                    <span class="field-error">Last name is required.</span>
                </div>
                <div class="field">
                    <label class="label" for="birthday">Birthday</label>
                    <input class="input" type="date" id="birthday" name="birthday" min="1900-01-01" max="<?= html_escape($today); ?>" value="<?= html_escape(isset($old_input['birthday']) ? $old_input['birthday'] : ''); ?>" required>
                    <span class="field-error">Enter a valid birthday.</span>
                </div>
                <div class="field">
                    <label class="label" for="contactno">Contact number</label>
                    <input class="input" type="tel" id="contactno" name="contactno" minlength="7" maxlength="20" pattern="[0-9+()\-\s]{7,20}" inputmode="tel" autocomplete="tel" value="<?= html_escape(isset($old_input['contactno']) ? $old_input['contactno'] : ''); ?>" placeholder="09xx xxx xxxx" required>
                    <span class="field-error">Enter a valid contact number.</span>
                </div>
                <div class="field field-full">
                    <label class="label" for="address">Address</label>
                    <textarea class="textarea" id="address" name="address" minlength="5" maxlength="255" autocomplete="street-address" placeholder="Complete address" required><?= html_escape(isset($old_input['address']) ? $old_input['address'] : ''); ?></textarea>
                    <span class="field-error">Address must contain at least 5 characters.</span>
                </div>
                <div class="field field-full">
                    <label class="label" for="email">Email address</label>
                    <input class="input" type="email" id="email" name="email" maxlength="190" autocomplete="email" value="<?= html_escape(isset($old_input['email']) ? $old_input['email'] : ''); ?>" placeholder="name@example.com" required>
                    <span class="field-error">Enter a valid email address.</span>
                </div>
                <div class="field-full notice notice-info">You do not choose a password here. The server generates a strong password after successful registration.</div>
                <button class="btn btn-primary btn-block field-full" type="submit" data-submitting-text="Creating account…">Register &amp; generate password</button>
            <?= form_close(); ?>
        </div>
    </section>
</main>
<?php $this->load->view('components/modals/alert.php'); ?>
<script src="<?= html_escape(base_url('assets/js/app.js')); ?>" defer></script>
</body>
</html>
