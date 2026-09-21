<?php
/**
 * application/views/errors/html/error_php.php | 2026-09-22
 * Safe PHP diagnostic page used in non-production environments.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$home_url = function_exists('site_url') ? site_url() : '/';
$safe_home_url = htmlspecialchars((string) $home_url, ENT_QUOTES, 'UTF-8');

$safe_message = htmlspecialchars(strip_tags((string) $message), ENT_QUOTES, 'UTF-8');
$safe_filepath = htmlspecialchars((string) $filepath, ENT_QUOTES, 'UTF-8');
$safe_line = (int) $line;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Error | Curib Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center bg-body-tertiary p-4">
    <main class="container text-center bg-white border rounded-4 shadow-sm p-5">
        <div class="badge text-bg-primary fs-4 p-2 mb-3" aria-hidden="true">C</div>
        <p class="text-uppercase small fw-bold text-primary mb-2">PHP</p>
        <h1 class="h2 mb-3">Application diagnostic</h1>
        <p class="text-secondary"><?= $safe_message; ?></p>
        <p class="small font-monospace text-break text-secondary"><?= $safe_filepath; ?>:<?= $safe_line; ?></p>
    </main>
</body>
</html>
