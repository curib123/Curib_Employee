<?php
/**
 * application/views/errors/html/error_php.php | 2026-09-22
 * Safe PHP diagnostic page used in non-production environments.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$asset_url = function_exists('base_url') ? base_url('assets/css/app.css') : 'assets/css/app.css';
$home_url = function_exists('site_url') ? site_url() : '/';
$safe_asset_url = htmlspecialchars((string) $asset_url, ENT_QUOTES, 'UTF-8');
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
    <link href="<?= $safe_asset_url; ?>" rel="stylesheet">
</head>
<body class="error-page">
    <main class="error-card">
        <div class="brand-mark error-brand" aria-hidden="true">C</div>
        <p class="error-code">PHP</p>
        <h1>Application diagnostic</h1>
        <p><?= $safe_message; ?></p>
        <p class="error-detail"><?= $safe_filepath; ?>:<?= $safe_line; ?></p>
    </main>
</body>
</html>
