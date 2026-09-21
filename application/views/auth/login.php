<?php
/**
 * application/views/auth/login.php | 2026-09-21
 * Bootstrap login page for generated-password authentication.
 */
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
    <title>Login | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= html_escape(base_url('assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="auth-side h-100 p-4 p-lg-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="brand-mark mb-4">C</div>
                            <h1 class="display-6 fw-bold">Welcome back.</h1>
                            <p class="lead opacity-75 mb-0">
                                Sign in with your email and the secure password generated when you registered.
                            </p>
                        </div>
                        <p class="small opacity-75 mt-5 mb-0">CodeIgniter 3 · Bootstrap 5 · Secure sessions</p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="p-4 p-lg-5">
                        <p class="text-uppercase small fw-semibold text-primary mb-2">Account access</p>
                        <h2 class="h3 mb-2">Sign in</h2>
                        <p class="text-secondary mb-4">Enter your registered email and generated password.</p>

                        <?= form_open('login', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    maxlength="190"
                                    autocomplete="email"
                                    value="<?= html_escape($old_email); ?>"
                                    required
                                >
                                <div class="invalid-feedback">Enter a valid email address.</div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    maxlength="255"
                                    autocomplete="current-password"
                                    required
                                >
                                <div class="invalid-feedback">Password is required.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
                        <?= form_close(); ?>

                        <p class="text-center text-secondary mt-4 mb-0">
                            New here?
                            <a href="<?= html_escape(site_url('register')); ?>" class="fw-semibold text-decoration-none">
                                Create an account
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php $this->load->view('components/modals/alert.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
