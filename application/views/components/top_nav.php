<?php
/**
 * application/views/components/top_nav.php | 2026-09-21
 * Shared Bootstrap top navigation for authenticated routes.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$active_nav = isset($active_nav) ? $active_nav : '';
$current_user = isset($current_user) && is_array($current_user) ? $current_user : array();
$first_name = isset($current_user['firstname']) ? $current_user['firstname'] : 'User';
$last_name = isset($current_user['lastname']) ? $current_user['lastname'] : '';
$full_name = trim($first_name . ' ' . $last_name);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= html_escape(site_url('dashboard')); ?>">
            <span class="badge text-bg-primary fs-5 p-2" aria-hidden="true">C</span>
            <span>Curib Employee</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#appNavbar"
            aria-controls="appNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="appNavbar">
            <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item">
                    <a
                        class="nav-link <?= $active_nav === 'dashboard' ? 'active' : ''; ?>"
                        href="<?= html_escape(site_url('dashboard')); ?>"
                    >
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link <?= $active_nav === 'employees' ? 'active' : ''; ?>"
                        href="<?= html_escape(site_url('employees')); ?>"
                    >
                        Employees
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end">
                    <div class="small fw-semibold"><?= html_escape($full_name); ?></div>
                    <div class="small text-secondary"><?= html_escape($current_user['email']); ?></div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal"
                >
                    Logout
                </button>
            </div>
        </div>
    </div>
</nav>
