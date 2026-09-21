<?php
/**
 * application/views/dashboard/index.php | 2026-09-21
 * Modern authenticated dashboard with top navigation and one-time password prompt.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$active_nav = 'dashboard';
$validation_errors = isset($validation_errors) && is_array($validation_errors)
    ? $validation_errors
    : array();
$show_first_login_password_prompt = !empty($show_first_login_password_prompt);
$reopen_modal = !empty($validation_errors) && $show_first_login_password_prompt
    ? 'firstLoginPasswordModal'
    : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= html_escape(base_url('assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <?php $this->load->view('components/top_nav.php'); ?>

    <main class="container py-4 py-lg-5 page-enter">
        <section class="surface-card hero-panel p-4 p-lg-5 mb-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge rounded-pill text-bg-primary mb-3">Dashboard</span>
                    <h1 class="display-6 fw-bold mb-2">
                        Hello, <?= html_escape($current_user['firstname']); ?>.
                    </h1>
                    <p class="lead text-secondary mb-0">
                        Your employee workspace is ready. Manage records securely from one simple interface.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= html_escape(site_url('employees')); ?>" class="btn btn-primary btn-lg">
                        Manage Employees
                    </a>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-md-6 col-xl-4">
                <section class="surface-card stat-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Total employees</p>
                    <div class="display-5 fw-bold mb-2"><?= (int) $employee_count; ?></div>
                    <p class="text-secondary mb-0">Current records in the employee database.</p>
                </section>
            </div>

            <div class="col-md-6 col-xl-4">
                <section class="surface-card stat-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Signed in as</p>
                    <div class="h4 fw-bold text-break mb-2"><?= html_escape($current_user['email']); ?></div>
                    <p class="text-secondary mb-0">Your session is protected by server-side authentication.</p>
                </section>
            </div>

            <div class="col-xl-4">
                <section class="surface-card stat-card p-4 h-100">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Quick route</p>
                    <h2 class="h4 fw-bold mb-2">Employee Management</h2>
                    <p class="text-secondary mb-3">Add, inspect, edit, and delete records using modal actions.</p>
                    <a href="<?= html_escape(site_url('employees')); ?>" class="btn btn-outline-primary">
                        Open Employees
                    </a>
                </section>
            </div>
        </div>
    </main>

    <?php $this->load->view('components/modals/change_password.php'); ?>
    <?php $this->load->view('components/modals/alert.php'); ?>
    <?php $this->load->view('components/modals/logout.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
