<?php
/**
 * application/config/config.php | 2026-09-21
 * Core application, database-backed session, cookie, and CSRF settings.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$config['base_url'] = getenv('APP_BASE_URL') ?: 'http://localhost/Curib_Employee/';
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
$config['log_threshold'] = 1;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;

$config['encryption_key'] = getenv('CI_ENCRYPTION_KEY') ?: 'curib-employee-change-this-key';

/*
|--------------------------------------------------------------------------
| Database-backed sessions
|--------------------------------------------------------------------------
| CI3 stores the current session in the ci_sessions table.
| sess_destroy() deletes the current session row during logout.
*/
$config['sess_driver'] = 'database';
$config['sess_cookie_name'] = 'curib_employee_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;

$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = filter_var(getenv('COOKIE_SECURE') ?: '0', FILTER_VALIDATE_BOOLEAN);
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
