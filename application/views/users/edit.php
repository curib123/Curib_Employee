<?php defined('BASEPATH') OR exit('No direct script access allowed'); $active_nav = 'users'; ?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit User | Curib Employee</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-body-tertiary">
<?php $this->load->view('components/layout.php'); ?>
<main class="app-main container py-4 py-lg-5">
    <div class="card border-0 bg-primary shadow-lg rounded-4 mb-4 page-hero"><div class="card-body p-4"><span class="badge rounded-pill text-light mb-2">User Management</span><h1 class="h2 fw-bold text-light mb-1">Edit User</h1><p class="text-light mb-0">Update account information.</p></div></div>
    <section class="card border-0 shadow-lg rounded-4 page-section-card"><div class="card-body p-4 p-lg-5">
        <?= form_open('users/update/' . (int) $user['Id'], array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
        <div class="row g-3"><div class="col-md-6"><label class="form-label" for="firstname">First name</label><input class="form-control form-control-lg" id="firstname" name="firstname" value="<?= html_escape($user['firstname']); ?>" required></div><div class="col-md-6"><label class="form-label" for="lastname">Last name</label><input class="form-control form-control-lg" id="lastname" name="lastname" value="<?= html_escape($user['lastname']); ?>" required></div><div class="col-md-6"><label class="form-label" for="birthday">Birthday</label><input class="form-control form-control-lg" type="date" id="birthday" name="birthday" min="1900-01-01" max="<?= html_escape(date('Y-m-d')); ?>" value="<?= html_escape($user['birthday']); ?>" required></div><div class="col-md-6"><label class="form-label" for="contactno">Contact number</label><input class="form-control form-control-lg" type="tel" id="contactno" name="contactno" maxlength="20" pattern="[0-9+()\-\s]{7,20}" inputmode="tel" value="<?= html_escape($user['contactno']); ?>" required></div><div class="col-12"><label class="form-label" for="address">Address</label><textarea class="form-control form-control-lg" id="address" name="address" rows="3" maxlength="255" required><?= html_escape($user['address']); ?></textarea></div><div class="col-12"><label class="form-label" for="email">Email address</label><input class="form-control form-control-lg" type="email" id="email" name="email" maxlength="190" value="<?= html_escape($user['email']); ?>" required></div></div>
        <div class="d-flex gap-2 mt-4"><a class="btn btn-outline-secondary" href="<?= html_escape(site_url('users')); ?>">Cancel</a><button class="btn btn-primary" type="submit">Update User</button></div><?= form_close(); ?>
    </div></section>
</main>
<?php $this->load->view('components/modals/alert.php'); ?><?php $this->load->view('components/modals/logout.php'); ?><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body></html>
