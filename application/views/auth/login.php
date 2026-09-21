<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$flash = isset($flash) && is_array($flash) ? $flash : array();
$old_email = isset($old_email) ? $old_email : '';
$validation_errors = array();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Login | Curib Employee</title>
    <link rel="stylesheet" href="<?= html_escape(base_url('assets/css/app.css')); ?>">
</head>
<body>
<main class="auth-shell">
    <section class="auth-card" aria-labelledby="loginTitle">
        <aside class="auth-side">
            <div>
                <span class="brand-mark" aria-hidden="true">C</span>
                <h1>Welcome back.</h1>
                <p>Sign in with your registered email and secure account password.</p>
            </div>
            <p>Simple employee management with protected server-side sessions.</p>
        </aside>
        <div class="auth-main">
            <p class="eyebrow">Account access</p>
            <h2 id="loginTitle">Sign in</h2>
            <p class="text-muted">Use the password generated when you registered, or your changed password.</p>

            <?= form_open('login', array('class' => 'form stack-lg', 'novalidate' => 'novalidate')); ?>
                <div class="field">
                    <label class="label" for="email">Email address</label>
                    <input class="input" type="email" id="email" name="email" maxlength="190" autocomplete="email" value="<?= html_escape($old_email); ?>" placeholder="name@example.com" required>
                    <span class="field-error">Enter a valid email address.</span>
                </div>
                <div class="field">
                    <label class="label" for="password">Password</label>
                    <input class="input" type="password" id="password" name="password" maxlength="72" autocomplete="current-password" placeholder="Your password" required>
                    <span class="field-error">Password is required.</span>
                </div>
                <button class="btn btn-primary btn-block" type="submit" data-submitting-text="Signing in…">Sign in</button>
            <?= form_close(); ?>

            <p class="auth-footer">New here? <a href="<?= html_escape(site_url('register')); ?>"><strong>Create an account</strong></a></p>
        </div>
    </section>
</main>
<?php $this->load->view('components/modals/alert.php'); ?>
<script src="<?= html_escape(base_url('assets/js/app.js')); ?>" defer></script>
</body>
</html>
