<?php
/**
 * application/views/auth/registration_password.php | 2026-09-21
 * One-time generated password presentation before returning to Login.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Save Your Password | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= html_escape(base_url('assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <main class="auth-shell">
        <section class="text-center">
            <div class="brand-mark mx-auto mb-3">C</div>
            <h1 class="h4">Your account is ready.</h1>
            <p class="text-secondary mb-0">
                Copy the generated password, then sign in from the Login page with your email and that password.
            </p>
        </section>
    </main>

    <?php $this->load->view('components/modals/generated_password.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= html_escape(base_url('assets/js/auth.js')); ?>"></script>
</body>
</html>
