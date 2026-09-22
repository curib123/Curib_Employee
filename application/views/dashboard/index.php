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
$prompt_action_failed = !empty($flash)
    && isset($flash['type'])
    && in_array($flash['type'], array('danger', 'warning'), TRUE);

$reopen_modal = $show_first_login_password_prompt
    && (!empty($validation_errors) || $prompt_action_failed)
    ? 'firstLoginPasswordModal'
    : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <?php $this->load->view('components/top_nav.php'); ?>

    <main class="container py-4 py-lg-5">
        <section class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-primary">
            <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <span class="badge rounded-pill text-light mb-3">Dashboard</span>
                    <h1 class="display-5 fw-bold mb-2 text-light">
                        Hello, <?= html_escape($current_user['lastname']); ?>.
                    </h1>
                    <p class="lead text-light mb-0">
                        Your employee workspace is ready. Manage records and Ready to go.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= html_escape(site_url('employees')); ?>" class="btn btn-light btn-lg rounded-3 px-3 m-1">
                        Manage Employees
                    </a>
            
                </div>
            </div>
            </div>
        </section>

        <div class="row g-5">
            <div class="col-md-6 col-xl-4">
                <section class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-body p-4">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Total employees</p>
                    <div class="display-5 fw-bold mb-2"><?= (int) $employee_count; ?></div>
                    <p class="text-secondary mb-0">Current records in the employee database.</p>
                    </div>
                </section>
            </div>

            <div class="col-md-6 col-xl-4">
                <section class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-body p-4">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Signed in as</p>
                    <div class="h4 fw-bold text-break mb-2"><?= html_escape($current_user['email']); ?></div>
                    <p class="text-secondary mb-0">Your session is protected by server-side authentication.</p>
                    </div>
                </section>
            </div>
             <div class="col-xl-4">
                <section class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-body p-4">
                    <h2 class="h4 fw-bold mb-2">User Profile</h2>
                    <p class="text-secondary mb-1"> Fullname : <?= html_escape($current_user['firstname']); ?> <?= html_escape($current_user['lastname']); ?></p>
                     <p class="text-secondary mb-1"> Contact No. : <?= html_escape($current_user['contactno']); ?> </p>
                     <p class="text-secondary mb-1"> Address : <?= html_escape($current_user['address']); ?> </p>
                     <p class="text-secondary mb-1"> Birthday : <?= html_escape(date('M d, Y', strtotime($current_user['birthday']))); ?> </p>
                      <p class="text-secondary mb-1"> Age : <?= html_escape($current_user['age']); ?> </p>
                    </div>

                    </div>
                </section>
            </div>

            <div class="col-xl-4 mt-5">
                <section class="card border-0 shadow-lg rounded-4 h-100">
                    <div class="card-body p-4">
                    <p class="text-uppercase small fw-semibold text-secondary mb-2">Quick route</p>
                    <h2 class="h4 fw-bold mb-2">Employee Management</h2>
                    <p class="text-secondary mb-3">Add, inspect, edit, and delete records using modal actions.</p>
                    <a href="<?= html_escape(site_url('employees')); ?>" class="btn btn-outline-primary">
                        Open Employees
                    </a>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php $this->load->view('components/modals/change_password.php'); ?>
    <?php $this->load->view('components/modals/alert.php'); ?>
    <?php $this->load->view('components/modals/logout.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
