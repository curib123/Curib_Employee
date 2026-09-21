<?php
/**
 * tests/router.php | 2026-09-22
 * PHP built-in server router used only by CI smoke tests.
 */

$path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
$path = is_string($path) ? $path : '/';
$candidate = realpath(__DIR__ . '/..') . $path;

if ($path !== '/' && is_file($candidate))
{
    return FALSE;
}

require __DIR__ . '/../index.php';
