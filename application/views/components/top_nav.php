<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$active_nav = isset($active_nav) ? $active_nav : '';
$current_user = isset($current_user) && is_array($current_user) ? $current_user : array();
$first_name = isset($current_user['firstname']) ? $current_user['firstname'] : 'User';
$last_name = isset($current_user['lastname']) ? $current_user['lastname'] : '';
$full_name = trim($first_name . ' ' . $last_name);
$email = isset($current_user['email']) ? $current_user['email'] : '';
?>
<nav class="topbar" aria-label="Main navigation">
    <div class="container topbar-inner">
        <a class="brand-link" href="<?= html_escape(site_url('dashboard')); ?>">
            <span class="brand-mark" aria-hidden="true">C</span><span>Curib Employee</span>
        </a>
        <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="appNavMenu" aria-label="Toggle navigation">
            <span class="nav-toggle-lines" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>
        <div class="nav-menu" id="appNavMenu" data-nav-menu>
            <div class="nav-links">
                <a class="nav-link <?= $active_nav === 'dashboard' ? 'is-active' : ''; ?>" href="<?= html_escape(site_url('dashboard')); ?>" <?= $active_nav === 'dashboard' ? 'aria-current="page"' : ''; ?>>Dashboard</a>
                <a class="nav-link <?= $active_nav === 'employees' ? 'is-active' : ''; ?>" href="<?= html_escape(site_url('employees')); ?>" <?= $active_nav === 'employees' ? 'aria-current="page"' : ''; ?>>Employees</a>
            </div>
            <div class="user-summary"><strong><?= html_escape($full_name); ?></strong><span><?= html_escape($email); ?></span></div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-modal-open="logoutModal">Logout</button>
        </div>
    </div>
</nav>
