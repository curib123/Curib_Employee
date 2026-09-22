<?php
/**
 * application/config/routes.php | 2026-09-21
 * Authentication, first-login password flow, dashboard, and Employee CRUD routes.
 */

// This file lets you re-map URI requests to specific controller functions.
defined('BASEPATH') OR exit('No direct script access allowed');


// Default route and error handling
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


// Authentication and account management routes
$route['login']['get'] = 'auth/login';
$route['login']['post'] = 'auth/login_submit';
$route['register']['get'] = 'auth/register';
$route['register']['post'] = 'auth/register_submit';
$route['password/change']['post'] = 'auth/change_password';
$route['password/skip']['post'] = 'auth/skip_password_change';
$route['account']['get'] = 'account/index';
$route['account/profile']['post'] = 'account/update_profile';
$route['account/password']['post'] = 'account/change_password';
$route['account/picture']['post'] = 'account/upload_picture';
$route['logout']['post'] = 'auth/logout';


// Dashboard route
$route['dashboard']['get'] = 'dashboard/index';


// Employee and User management routes
$route['employees']['get'] = 'employees/index';
$route['employees/store']['post'] = 'employees/store';
$route['employees/update/(:num)']['post'] = 'employees/update/$1';
$route['employees/delete/(:num)']['post'] = 'employees/delete/$1';
$route['users']['get'] = 'users/index';
$route['users/store']['post'] = 'users/store';
$route['users/edit/(:num)']['get'] = 'users/edit/$1';
$route['users/update/(:num)']['post'] = 'users/update/$1';
$route['users/delete/(:num)']['post'] = 'users/delete/$1';
$route['reports']['get'] = 'reports/index';
$route['reports/export']['get'] = 'reports/export';
