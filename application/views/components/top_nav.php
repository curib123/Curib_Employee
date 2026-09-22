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
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold me-lg-4" href="<?= html_escape(site_url('dashboard')); ?>">
            <span class="badge rounded-3 text-bg-primary fs-5 p-2" aria-hidden="true">C</span>
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

        <div class="collapse navbar-collapse py-3 py-lg-0" id="appNavbar">
            <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item">
                    <a
                        class="nav-link px-lg-3 rounded-3 <?= $active_nav === 'dashboard' ? 'active fw-semibold text-primary bg-primary-subtle' : 'text-body-secondary'; ?>"
                        href="<?= html_escape(site_url('dashboard')); ?>"
                        <?= $active_nav === 'dashboard' ? 'aria-current="page"' : ''; ?>
                    >
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link px-lg-3 rounded-3 <?= $active_nav === 'employees' ? 'active fw-semibold text-primary bg-primary-subtle' : 'text-body-secondary'; ?>"
                        href="<?= html_escape(site_url('employees')); ?>"
                        <?= $active_nav === 'employees' ? 'aria-current="page"' : ''; ?>
                    >
                        Employees
                    </a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 mt-3 mt-lg-0">
                <div class="text-lg-end">
                    <div class="small fw-semibold"><?= html_escape($full_name); ?></div>
                    <div class="small text-secondary"><?= html_escape($current_user['email']); ?></div>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm rounded-pill px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal"
                >
                    Logout
                </button>
            </div>
        </div>
    </div>
</nav>
