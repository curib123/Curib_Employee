<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$active_nav = 'dashboard';
$validation_errors = isset($validation_errors) && is_array($validation_errors) ? $validation_errors : array();
$show_first_login_password_prompt = !empty($show_first_login_password_prompt);
$prompt_action_failed = !empty($flash) && isset($flash['type']) && in_array($flash['type'], array('danger', 'warning'), TRUE);
$reopen_modal = $show_first_login_password_prompt && (!empty($validation_errors) || $prompt_action_failed) ? 'firstLoginPasswordModal' : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Dashboard | Curib Employee</title>
    <link rel="stylesheet" href="<?= html_escape(base_url('assets/css/app.css')); ?>">
</head>
<body>
<?php $this->load->view('components/top_nav.php'); ?>
<main class="container page stack-lg">
    <section class="card card-pad-lg hero">
        <div class="hero-grid">
            <div>
                <span class="badge">Dashboard</span>
                <h1>Hello, <?= html_escape($current_user['firstname']); ?>.</h1>
                <p>Your secure employee workspace is ready. Manage records from one clear interface.</p>
            </div>
            <a class="btn btn-primary" href="<?= html_escape(site_url('employees')); ?>">Manage employees</a>
        </div>
    </section>
    <section class="stats-grid" aria-label="Dashboard summary">
        <article class="card card-pad stat-card"><p class="eyebrow">Total employees</p><div class="stat-value"><?= (int) $employee_count; ?></div><p class="text-muted">Current records in the employee database.</p></article>
        <article class="card card-pad stat-card"><p class="eyebrow">Signed in as</p><div class="stat-value break-word"><?= html_escape($current_user['email']); ?></div><p class="text-muted">Authentication is protected by server-side database sessions.</p></article>
        <article class="card card-pad stat-card"><p class="eyebrow">Quick route</p><div class="stat-value">Employees</div><p class="text-muted">Add, inspect, edit, and delete records with validated actions.</p></article>
    </section>
</main>
<?php $this->load->view('components/modals/change_password.php'); ?>
<?php $this->load->view('components/modals/alert.php'); ?>
<?php $this->load->view('components/modals/logout.php'); ?>
<script src="<?= html_escape(base_url('assets/js/app.js')); ?>" defer></script>
</body>
</html>
