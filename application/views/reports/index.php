<?php defined('BASEPATH') OR exit('No direct script access allowed'); $active_nav = 'reports'; $query = array('start_date' => $start_date, 'end_date' => $end_date); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <?php $this->load->view('components/layout.php'); ?>

    <main class="app-main container py-3 py-lg-3">
           <section class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-primary page-hero">
            <div class="card-body p-3 p-lg-3">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-2 text-light">
                        Reports
                    </h1>
                    <p class="lead text-light mb-0">Registration, employee, and age statistics. </p>
                </div>
               
            </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="get" action="<?= html_escape(site_url('reports')); ?>" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="start_date">From</label>
                        <input class="form-control" type="date" id="start_date" name="start_date" value="<?= html_escape($start_date); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="end_date">To</label>
                        <input class="form-control" type="date" id="end_date" name="end_date" value="<?= html_escape($end_date); ?>">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" type="submit">Apply date range</button>
                    </div>
                </form>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-6">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Registration report</h2>
                        </div>
                        <p class="text-secondary mb-3"><?= (int) $registration_count; ?> registrations in the selected date range.</p>
                        <div class="btn-group flex-wrap report-export-group" role="group" aria-label="Registration export options">
                            <a class="btn btn-outline-success" href="<?= html_escape(site_url('reports/export') . '?report=registrations&format=csv&' . http_build_query($query)); ?>">CSV</a>
                            <a class="btn btn-outline-success" href="<?= html_escape(site_url('reports/export') . '?report=registrations&format=xlsx&' . http_build_query($query)); ?>">Excel</a>
                            <a class="btn btn-outline-danger" href="<?= html_escape(site_url('reports/export') . '?report=registrations&format=pdf&' . http_build_query($query)); ?>">PDF</a>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Monthly registration report</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-3">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($monthly_report)): ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-secondary py-3">No data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($monthly_report as $row): ?>
                                            <tr>
                                                <td><?= html_escape($row['registration_month']); ?></td>
                                                <td class="text-end"><?= (int) $row['total']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="btn-group flex-wrap report-export-group" role="group" aria-label="Monthly registration export options">
                            <a class="btn btn-outline-success btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=monthly&format=csv&' . http_build_query($query)); ?>">CSV</a>
                            <a class="btn btn-outline-success btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=monthly&format=xlsx&' . http_build_query($query)); ?>">Excel</a>
                            <a class="btn btn-outline-danger btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=monthly&format=pdf&' . http_build_query($query)); ?>">PDF</a>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Age statistics report</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-3">
                                <thead>
                                    <tr>
                                        <th>Age range</th>
                                        <th class="text-end">Users</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($age_report)): ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-secondary py-3">No data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($age_report as $row): ?>
                                            <tr>
                                                <td><?= html_escape($row['age_range']); ?></td>
                                                <td class="text-end"><?= (int) $row['total']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="btn-group flex-wrap report-export-group" role="group" aria-label="Age statistics export options">
                            <a class="btn btn-outline-success btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=age&format=csv'); ?>">CSV</a>
                            <a class="btn btn-outline-success btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=age&format=xlsx'); ?>">Excel</a>
                            <a class="btn btn-outline-danger btn-sm" href="<?= html_escape(site_url('reports/export') . '?report=age&format=pdf'); ?>">PDF</a>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Employee report</h2>
                        <p class="text-secondary mb-3">Export the separate employee records dataset.</p>
                        <div class="btn-group flex-wrap report-export-group" role="group" aria-label="Employee export options">
                            <a class="btn btn-outline-success" href="<?= html_escape(site_url('reports/export') . '?report=employees&format=csv'); ?>">CSV</a>
                            <a class="btn btn-outline-success" href="<?= html_escape(site_url('reports/export') . '?report=employees&format=xlsx'); ?>">Excel</a>
                            <a class="btn btn-outline-danger" href="<?= html_escape(site_url('reports/export') . '?report=employees&format=pdf'); ?>">PDF</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php $this->load->view('components/modals/alert.php'); ?>
    <?php $this->load->view('components/modals/logout.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
