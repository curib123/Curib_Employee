<?php
/**
 * application/views/errors/html/error_404.php | 2026-09-22
 * Friendly HTML 404 page.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$safe_heading = htmlspecialchars(strip_tags((string) $heading), ENT_QUOTES, 'UTF-8');
$safe_message = htmlspecialchars(strip_tags((string) $message), ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $safe_heading; ?> | Curib Employee</title>
    <link href="<?= html_escape(base_url('assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body class="error-page">
    <main class="error-card">
        <div class="brand-mark error-brand" aria-hidden="true">C</div>
        <p class="error-code">404</p>
        <h1><?= $safe_heading; ?></h1>
        <p><?= $safe_message; ?></p>
        <a class="error-link" href="<?= html_escape(site_url()); ?>">Return to the application</a>
    </main>
</body>
</html>
