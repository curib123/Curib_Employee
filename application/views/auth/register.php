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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <main class="min-vh-100 d-flex align-items-center py-4 py-lg-5">
        <div class="container">
            <section class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card border-0 shadow-lg overflow-hidden rounded-4">
            <div class="row g-0">
                <div class="col-lg-4">
                    <div class="h-100 p-4 p-lg-5 d-flex flex-column justify-content-between bg-primary text-white">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 mb-4">
                                <span class="badge text-bg-light text-primary fs-5 p-2">C</span>
                                <span class="fw-semibold">Curib Employee</span>
                            </div>
                            <h1 class="h2 fw-bold">Create your account</h1>
                            <p class="opacity-75 mb-0">
                                Complete your profile. A strong password will be generated securely after registration.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-top border-light border-opacity-25">
                            <p class="small opacity-75 mb-0">Your generated password is shown only once after successful registration.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="p-4 p-md-5">
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-4">
                            <div>
                                <span class="badge rounded-pill text-bg-primary-subtle text-primary-emphasis mb-3">Registration</span>
                                <h2 class="h2 fw-bold mb-2">Your information</h2>
                                <p class="text-secondary mb-0">Complete all required fields to create your account.</p>
                            </div>
                            <a href="<?= html_escape(site_url('login')); ?>" class="btn btn-outline-secondary align-self-sm-start">
                               < Back to Login
                            </a>
                        </div>

                        <?= form_open('register', array('class' => 'needs-validation', 'novalidate' => 'novalidate')); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input
                                      
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="firstname"
                                        name="firstname"
                                        maxlength="50"
                                        autocomplete="off"
                                        value="<?= html_escape(isset($old_input['firstname']) ? $old_input['firstname'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">First name is required.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input
                                    
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="lastname"
                                        name="lastname"
                                        maxlength="50"
                                        autocomplete="off"
                                        value="<?= html_escape(isset($old_input['lastname']) ? $old_input['lastname'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Last name is required.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="birthday" class="form-label">Birthday</label>
                                    <input
                                        autocomplete="off"
                                        type="date"
                                        class="form-control form-control-lg"
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
                                        autocomplete="off"
                                        type="tel"
                                        class="form-control form-control-lg"
                                        id="contactno"
                                        name="contactno"
                                        maxlength="11"
                                        pattern="[0-9+()\-\s]{7,20}"
                                        inputmode="tel"
                                        value="<?= html_escape(isset($old_input['contactno']) ? $old_input['contactno'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Enter a valid contact number.</div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea
                                        class="form-control form-control-lg"
                                        id="address"
                                        name="address"
                                        rows="3"
                                        maxlength="255"
                                        autocomplete="off"
                                        required
                                    ><?= html_escape(isset($old_input['address']) ? $old_input['address'] : ''); ?></textarea>
                                    <div class="invalid-feedback">Address is required.</div>
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input
                                        type="email"
                                        class="form-control form-control-lg"
                                        id="email"
                                        name="email"
                                        maxlength="190"
                                        autocomplete="off"
                                        value="<?= html_escape(isset($old_input['email']) ? $old_input['email'] : ''); ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-primary d-flex align-items-start gap-3 mb-0" role="alert">
                                        <div>
                                            <div class="fw-semibold mb-1">Password is generated for you</div>
                                            <div class="small">A strong password will be shown once after successful registration. Save it before continuing.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Register &amp; Generate Password
                                    </button>
                                </div>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php $this->load->view('components/modals/alert.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
