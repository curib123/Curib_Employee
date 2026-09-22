<?php defined('BASEPATH') OR exit('No direct script access allowed'); $active_nav = 'account'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<?php $this->load->view('components/layout.php'); ?>
<main class="app-main container py-4 py-lg-5">
    <section class="card border-0 shadow-sm rounded-4 overflow-hidden page-hero account-hero">
        <div class="card-body p-4 p-lg-5">
            <span class="badge rounded-pill text-light mb-2">Account</span>
            <h1 class="h2 fw-bold text-light mb-1">My Account</h1>
            <p class="text-light mb-0">Manage your profile details, picture, and password.</p>
        </div>
    </section>
    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm rounded-4 page-section-card"><div class="card-body p-4">
                <div class="form-section-title mb-4"><h2 class="h4 page-section-title mb-1">Profile details</h2><p class="page-section-copy mb-0">Keep your contact details current.</p></div>
                <?= form_open('account/profile', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="firstname">First name</label><input class="form-control" id="firstname" name="firstname" maxlength="100" value="<?= html_escape($current_user['firstname']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label" for="lastname">Last name</label><input class="form-control" id="lastname" name="lastname" maxlength="100" value="<?= html_escape($current_user['lastname']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label" for="birthday">Birthday</label><input class="form-control" type="date" id="birthday" name="birthday" min="1900-01-01" max="<?= html_escape(date('Y-m-d')); ?>" value="<?= html_escape($current_user['birthday']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label" for="contactno">Contact number</label><input class="form-control" type="tel" id="contactno" name="contactno" maxlength="20" pattern="[0-9+()\-\s]{7,20}" inputmode="tel" autocomplete="tel" value="<?= html_escape($current_user['contactno']); ?>" required></div>
                    <div class="col-12"><label class="form-label" for="address">Address</label><textarea class="form-control" id="address" name="address" maxlength="255" rows="3" required><?= html_escape($current_user['address']); ?></textarea></div>
                </div><button class="btn btn-primary mt-4" type="submit">Update account</button><?= form_close(); ?>
            </div></section>
        </div>
        <div class="col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 mb-4 page-section-card"><div class="card-body p-4">
                <div class="form-section-title mb-3"><h2 class="h5 page-section-title mb-1">Profile picture</h2><p class="page-section-copy small mb-0">Use a clear JPG, PNG, or WebP image.</p></div>
                <?php if (!empty($current_user['profile_picture'])): ?><img src="<?= html_escape(base_url($current_user['profile_picture'])); ?>" alt="Profile" class="account-profile-image mb-3" width="96" height="96"><?php endif; ?>
                <?= form_open_multipart('account/picture', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                    <input class="form-control" type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp" required><div class="form-text">JPG, PNG, or WebP; maximum 2 MB and 2000 x 2000 pixels.</div><button class="btn btn-outline-primary mt-3" type="submit">Upload picture</button>
                <?= form_close(); ?>
            </div></section>
            <section class="card border-0 shadow-sm rounded-4 page-section-card"><div class="card-body p-4">
                <div class="form-section-title mb-3"><h2 class="h5 page-section-title mb-1">Change password</h2><p class="page-section-copy small mb-0">Choose a strong password you do not reuse elsewhere.</p></div>
                <?= form_open('account/password', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                    <label class="form-label" for="current_password">Current password</label><input class="form-control mb-3" type="password" id="current_password" name="current_password" maxlength="72" autocomplete="current-password" required>
                    <label class="form-label" for="new_password">New password</label><input class="form-control mb-3" type="password" id="new_password" name="new_password" minlength="12" maxlength="72" autocomplete="new-password" required>
                    <label class="form-label" for="confirm_password">Confirm new password</label><input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="12" maxlength="72" autocomplete="new-password" required>
                    <div class="form-text">Use at least 12 characters with uppercase, lowercase, a number, and a symbol.</div><button class="btn btn-primary mt-3" type="submit">Update password</button>
                <?= form_close(); ?>
            </div></section>
        </div>
    </div>
</main>
<?php $this->load->view('components/modals/alert.php'); ?><?php $this->load->view('components/modals/logout.php'); ?><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body></html>
