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
$page_titles = array(
    'dashboard' => 'Dashboard',
    'employees' => 'Employees',
    'users' => 'Users',
    'reports' => 'Reports',
    'account' => 'My Account'
);
$current_uri = trim($this->uri->uri_string(), '/');
$uri_section = $current_uri === '' ? 'dashboard' : explode('/', $current_uri)[0];
$active_nav = isset($page_titles[$active_nav]) && $active_nav !== '' ? $active_nav : $uri_section;
$page_title = isset($page_titles[$active_nav]) ? $page_titles[$active_nav] : 'Curib Employee';
?>


<link rel="stylesheet" href="<?= html_escape(base_url('assets/css/navigation.css?v=3')); ?>">
<aside class="app-sidebar" aria-label="Main navigation">
    <a class="app-sidebar-brand" href="<?= html_escape(site_url('dashboard')); ?>">
        <span class="badge rounded-3 text-bg-light text-primary fs-5 p-2" aria-hidden="true">C</span>
        <span>Curib Employee</span>
    </a>

    <div class="app-sidebar-label">Workspace</div>
    <nav class="app-sidebar-nav">
        <a class="<?= $active_nav === 'dashboard' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('dashboard')); ?>" <?= $active_nav === 'dashboard' ? 'aria-current="page"' : ''; ?>>Dashboard</a>
        <a class="<?= $active_nav === 'employees' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('employees')); ?>" <?= $active_nav === 'employees' ? 'aria-current="page"' : ''; ?>>Employees</a>
        <a class="<?= $active_nav === 'users' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('users')); ?>" <?= $active_nav === 'users' ? 'aria-current="page"' : ''; ?>>Users</a>
        <a class="<?= $active_nav === 'reports' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('reports')); ?>" <?= $active_nav === 'reports' ? 'aria-current="page"' : ''; ?>>Reports</a>
        <a class="<?= $active_nav === 'account' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('account')); ?>" <?= $active_nav === 'account' ? 'aria-current="page"' : ''; ?>>Account</a>
    </nav>

    <div class="app-sidebar-footer">
        <button type="button" class="btn btn-outline-light rounded-3" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
    </div>
</aside>
<header class="app-topbar">
    <h1><?= html_escape($page_title); ?></h1>
    <div class="app-topbar-user">
        <?php if (!empty($current_user['profile_picture'])): ?>
            <img src="<?= html_escape(base_url($current_user['profile_picture'])); ?>" alt="Profile picture">
        <?php else: ?>
            <span class="app-topbar-avatar"><?= html_escape(strtoupper(substr($first_name, 0, 1))); ?></span>
        <?php endif; ?>
        <div>
            <strong><?= html_escape($full_name); ?></strong>
            <span><?= html_escape($current_user['email']); ?></span>
        </div>
    </div>
</header>
