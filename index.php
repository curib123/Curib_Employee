<?php
/** Front controller for Curib Employee. */
define('ENVIRONMENT', getenv('CI_ENV') ?: 'development');

switch (ENVIRONMENT)
{
    case 'development':
        // CI3 is a legacy framework; suppress framework-level deprecation noise on modern PHP while keeping real errors visible.
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        ini_set('display_errors', 1);
        break;
    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

if (!headers_sent())
{
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'; img-src 'self' data:; style-src 'self'; script-src 'self'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');

    $is_https = isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    if ($is_https)
    {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

$system_path = __DIR__ . '/vendor/codeigniter/framework/system';
$application_folder = __DIR__ . '/application';
$view_folder = '';

if (($temp = realpath($system_path)) !== FALSE)
{
    $system_path = $temp . DIRECTORY_SEPARATOR;
}
$system_path = rtrim($system_path, '/\\') . DIRECTORY_SEPARATOR;

if (!is_dir($system_path))
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'CodeIgniter system files were not found. Run "composer install" first.';
    exit(3);
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));
define('APPPATH', rtrim($application_folder, '/\\') . DIRECTORY_SEPARATOR);

if ($view_folder === '' && is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR))
{
    $view_folder = APPPATH . 'views';
}
define('VIEWPATH', rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR);
require_once BASEPATH . 'core/CodeIgniter.php';
