<?php
/**
 * application/config/config.php | 2026-09-21
 * Core application, security, cookie, CSRF, and database-session configuration.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$app_base_url = getenv('APP_BASE_URL');

if (!$app_base_url && ENVIRONMENT === 'production')
{
    header('HTTP/1.1 500 Internal Server Error');
    exit('APP_BASE_URL must be configured in production.');
}

if (!$app_base_url)
{
    $https = isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $scheme = $https ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST'])
        ? preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', (string) $_SERVER['HTTP_HOST'])
        : 'localhost';
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '/index.php';
    $base_path = str_replace('\\', '/', dirname($script_name));
    $base_path = $base_path === '/' ? '' : rtrim($base_path, '/');

    $app_base_url = $scheme . '://' . ($host ?: 'localhost') . $base_path . '/';
}

$config['base_url'] = rtrim($app_base_url, '/') . '/';
$config['index_page'] = '';
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['allow_get_array'] = TRUE;
$config['log_threshold'] = ENVIRONMENT === 'production' ? 1 : 2;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;

$encryption_key = getenv('CI_ENCRYPTION_KEY');

if (!$encryption_key)
{
    if (ENVIRONMENT === 'production')
    {
        header('HTTP/1.1 500 Internal Server Error');
        exit('CI_ENCRYPTION_KEY must be configured in production.');
    }

    $encryption_key = hash('sha256', APPPATH . php_uname('n') . __FILE__);
}

$config['encryption_key'] = $encryption_key;

/*
|--------------------------------------------------------------------------
| Database-backed sessions
|--------------------------------------------------------------------------
*/
$config['sess_driver'] = 'database';
$config['sess_cookie_name'] = 'curib_employee_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;

$is_https = isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
$secure_cookie_env = filter_var(getenv('COOKIE_SECURE') ?: '0', FILTER_VALIDATE_BOOLEAN);

$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = $secure_cookie_env || $is_https;
$config['cookie_httponly'] = TRUE;
$config['cookie_samesite'] = 'Lax';

$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;

$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'curib_csrf_token';
$config['csrf_cookie_name'] = 'curib_csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
