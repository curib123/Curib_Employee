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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <main class="min-vh-100 d-flex align-items-center py-5">
        <section class="container text-center">
            <div class="badge rounded-3 text-bg-primary fs-4 p-2 mb-3">C</div>
            <h1 class="h3 fw-bold">Your account is ready.</h1>
            <p class="text-secondary col-lg-6 mx-auto mb-0">
                Copy the generated password, then sign in from the Login page with your email and that password.
            </p>
        </section>
    </main>

    <?php $this->load->view('components/modals/generated_password.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= html_escape(base_url('assets/js/auth.js')); ?>"></script>
</body>
</html>
