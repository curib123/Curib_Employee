<?php
/**
 * application/views/auth/register.php | 2026-09-21
 * Bootstrap registration page; passwords are generated securely by the server.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$flash = isset($flash) && is_array($flash) ? $flash : array();
$validation_errors = isset($validation_errors) && is_array($validation_errors)
    ? $validation_errors
    : array();
$old_input = isset($old_input) && is_array($old_input) ? $old_input : array();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= html_escape(base_url('assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-card">
            <div class="row g-0">
                <div class="col-lg-4">
                    <div class="auth-side h-100 p-4 p-lg-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="brand-mark mb-4">C</div>
                            <h1 class="h2 fw-bold">Create your account</h1>
                            <p class="opacity-75 mb-0">
                                Complete your profile. A strong password will be generated securely after registration.
                            </p>
                        </div>
                        <p class="small opacity-75 mt-5 mb-0">Your generated password is shown only once.</p>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="p-4 p-lg-5">
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                            <div>
                                <p class="text-uppercase small fw-semibold text-primary mb-2">Registration</p>
                                <h2 class="h3 mb-1">Your information</h2>
                                <p class="text-secondary mb-0">All fields are required.</p>
                            </div>
                            <a href="<?= html_escape(site_url('login')); ?>" class="btn btn-light align-self-sm-start">
                                Back to Login
                            </a>
                        </div>

                        <?= form_open('register', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="firstname"
                                        name="firstname"
                                        maxlength="100"
                                        autocomplete="given-name"
                                        value="<?= html_escape(isset($old_input['firstname']) ? $old_input['firstname'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">First name is required.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="lastname"
                                        name="lastname"
                                        maxlength="100"
                                        autocomplete="family-name"
                                        value="<?= html_escape(isset($old_input['lastname']) ? $old_input['lastname'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Last name is required.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="birthday" class="form-label">Birthday</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="birthday"
                                        name="birthday"
                                        min="1900-01-01"
                                        max="<?= html_escape($today); ?>"
                                        value="<?= html_escape(isset($old_input['birthday']) ? $old_input['birthday'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Enter a valid birthday.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="contactno" class="form-label">Contact No.</label>
                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="contactno"
                                        name="contactno"
                                        maxlength="20"
                                        pattern="[0-9+()\-\s]{7,20}"
                                        autocomplete="tel"
                                        inputmode="tel"
                                        value="<?= html_escape(isset($old_input['contactno']) ? $old_input['contactno'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Enter a valid contact number.</div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea
                                        class="form-control"
                                        id="address"
                                        name="address"
                                        rows="3"
                                        maxlength="255"
                                        autocomplete="street-address"
                                        required
                                    ><?= html_escape(isset($old_input['address']) ? $old_input['address'] : ''); ?></textarea>
                                    <div class="invalid-feedback">Address is required.</div>
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        maxlength="190"
                                        autocomplete="email"
                                        value="<?= html_escape(isset($old_input['email']) ? $old_input['email'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info mb-0" role="alert">
                                        You do not choose a password here. A strong password is generated after successful registration.
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                        Register &amp; Generate Password
                                    </button>
                                </div>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php $this->load->view('components/modals/alert.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
