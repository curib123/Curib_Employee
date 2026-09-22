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

        <div class="row g-4 mt-4">
            <div class="col-sm-6 col-xl-3"><section class="card border-0 shadow-lg rounded-4 h-100"><div class="card-body p-4"><p class="text-uppercase small fw-semibold text-secondary mb-2">Registered users</p><div class="display-5 fw-bold mb-2"><?= (int) $registered_user_count; ?></div><p class="text-secondary mb-0">All user accounts.</p></div></section></div>
            <div class="col-sm-6 col-xl-3"><section class="card border-0 shadow-lg rounded-4 h-100"><div class="card-body p-4"><p class="text-uppercase small fw-semibold text-secondary mb-2">New users today</p><div class="display-5 fw-bold mb-2"><?= (int) $new_users_today; ?></div><p class="text-secondary mb-0">Accounts created today.</p></div></section></div>
            <div class="col-sm-6 col-xl-3"><section class="card border-0 shadow-lg rounded-4 h-100"><div class="card-body p-4"><p class="text-uppercase small fw-semibold text-secondary mb-2">New users this month</p><div class="display-5 fw-bold mb-2"><?= (int) $new_users_month; ?></div><p class="text-secondary mb-0">Accounts created this month.</p></div></section></div>
            <div class="col-sm-6 col-xl-3"><section class="card border-0 shadow-lg rounded-4 h-100"><div class="card-body p-4"><p class="text-uppercase small fw-semibold text-secondary mb-2">Age statistics</p><div class="h5 mb-2">Average: <?= html_escape(number_format((float) $age_statistics['average_age'], 1)); ?></div><p class="text-secondary mb-0">Youngest <?= (int) $age_statistics['youngest_age']; ?>, oldest <?= (int) $age_statistics['oldest_age']; ?>.</p></div></section></div>
            <div class="col-12"><section class="card border-0 shadow-lg rounded-4"><div class="card-body p-4"><h2 class="h5 mb-3">Users by age range</h2><div class="row g-3"><div class="col-6 col-md"><div class="border rounded-3 p-3"><div class="small text-secondary">Under 18</div><div class="h4 mb-0"><?= (int) $age_breakdown['under_18']; ?></div></div></div><div class="col-6 col-md"><div class="border rounded-3 p-3"><div class="small text-secondary">18-30</div><div class="h4 mb-0"><?= (int) $age_breakdown['age_18_30']; ?></div></div></div><div class="col-6 col-md"><div class="border rounded-3 p-3"><div class="small text-secondary">31-40</div><div class="h4 mb-0"><?= (int) $age_breakdown['age_31_40']; ?></div></div></div><div class="col-6 col-md"><div class="border rounded-3 p-3"><div class="small text-secondary">41-50</div><div class="h4 mb-0"><?= (int) $age_breakdown['age_41_50']; ?></div></div></div><div class="col-6 col-md"><div class="border rounded-3 p-3"><div class="small text-secondary">51+</div><div class="h4 mb-0"><?= (int) $age_breakdown['age_51_plus']; ?></div></div></div></div></div></section></div>
            <div class="col-12"><section class="card border-0 shadow-lg rounded-4"><div class="card-body p-4"><h2 class="h5 mb-3">Top user addresses</h2><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Address</th><th class="text-end">Users</th></tr></thead><tbody><?php foreach ($address_statistics as $address): ?><tr><td><?= html_escape($address['address']); ?></td><td class="text-end"><?= (int) $address['total']; ?></td></tr><?php endforeach; ?></tbody></table></div></div></section></div>
        </div>
    </main>

    <?php $this->load->view('components/modals/change_password.php'); ?>
    <?php $this->load->view('components/modals/alert.php'); ?>
    <?php $this->load->view('components/modals/logout.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
