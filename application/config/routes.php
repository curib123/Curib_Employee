<?php
/**
 * application/config/routes.php | 2026-09-21
 * Authentication, first-login password flow, dashboard, and Employee CRUD routes.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login']['get'] = 'auth/login';
$route['login']['post'] = 'auth/login_submit';
$route['register']['get'] = 'auth/register';
$route['register']['post'] = 'auth/register_submit';
$route['password/change']['post'] = 'auth/change_password';
$route['password/skip']['post'] = 'auth/skip_password_change';
$route['logout']['post'] = 'auth/logout';

$route['dashboard']['get'] = 'dashboard/index';

$route['employees']['get'] = 'employees/index';
$route['employees/store']['post'] = 'employees/store';
$route['employees/update/(:num)']['post'] = 'employees/update/$1';
$route['employees/delete/(:num)']['post'] = 'employees/delete/$1';
