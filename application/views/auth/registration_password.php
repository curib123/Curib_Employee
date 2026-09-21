<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Save Your Password | Curib Employee</title>
    <link rel="stylesheet" href="<?= html_escape(base_url('assets/css/app.css')); ?>">
</head>
<body>
<main class="auth-shell">
    <section class="card card-pad-lg text-center">
        <span class="brand-mark" aria-hidden="true">C</span>
        <h1>Your account is ready.</h1>
        <p class="text-muted">Copy the generated password before continuing to login.</p>
    </section>
</main>
<?php $this->load->view('components/modals/generated_password.php'); ?>
<script src="<?= html_escape(base_url('assets/js/app.js')); ?>" defer></script>
<script src="<?= html_escape(base_url('assets/js/auth.js')); ?>" defer></script>
</body>
</html>
