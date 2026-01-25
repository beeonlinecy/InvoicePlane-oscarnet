<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'login';


$route['logout'] ='login/logout';
$route['dashboard'] ='dashboard/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

?>