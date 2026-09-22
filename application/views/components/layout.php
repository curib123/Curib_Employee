<?php
/**
 * application/views/components/layout.php
 * Shared application shell for authenticated routes.
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

<link rel="stylesheet" href="<?= html_escape(base_url('assets/css/navigation.css?v=4')); ?>">
<link rel="stylesheet" href="<?= html_escape(base_url('assets/css/app.css?v=1')); ?>">

<aside class="app-sidebar" aria-label="Main navigation">
    <a class="app-sidebar-brand" href="<?= html_escape(site_url('dashboard')); ?>">
        <span class="app-brand-mark" aria-hidden="true">C</span>
        <span>Curib Employee</span>
    </a>

    <div class="app-sidebar-label">Workspace</div>
    <nav class="app-sidebar-nav">
        <a class="<?= $active_nav === 'dashboard' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('dashboard')); ?>" <?= $active_nav === 'dashboard' ? 'aria-current="page"' : ''; ?>>
            <svg class="app-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
            <span>Dashboard</span>
        </a>
        <a class="<?= $active_nav === 'employees' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('employees')); ?>" <?= $active_nav === 'employees' ? 'aria-current="page"' : ''; ?>>
            <svg class="app-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Employees</span>
        </a>
        <a class="<?= $active_nav === 'users' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('users')); ?>" <?= $active_nav === 'users' ? 'aria-current="page"' : ''; ?>>
            <svg class="app-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            <span>Users</span>
        </a>
        <a class="<?= $active_nav === 'reports' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('reports')); ?>" <?= $active_nav === 'reports' ? 'aria-current="page"' : ''; ?>>
            <svg class="app-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3"/><path d="M2 21h20"/></svg>
            <span>Reports</span>
        </a>
        <a class="<?= $active_nav === 'account' ? 'active is-active' : ''; ?>" href="<?= html_escape(site_url('account')); ?>" <?= $active_nav === 'account' ? 'aria-current="page"' : ''; ?>>
            <svg class="app-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
            <span>Account</span>
        </a>
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
            <span><?= html_escape(isset($current_user['email']) ? $current_user['email'] : ''); ?></span>
        </div>
    </div>
</header>
