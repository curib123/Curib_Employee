<?php
/**
 * application/config/routes.php | 2026-09-21
 * Routes for the Employee CRUD module.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'employees';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['employees'] = 'employees/index';
$route['employees/store']['post'] = 'employees/store';
$route['employees/update/(:num)']['post'] = 'employees/update/$1';
$route['employees/delete/(:num)']['post'] = 'employees/delete/$1';
