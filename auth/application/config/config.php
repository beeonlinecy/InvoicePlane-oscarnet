<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
*/
$config['base_url'] = 'https://invonos.com/auth';  // URL твоего auth

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
*/
$config['index_page'] = '';  // используем mod_rewrite

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ directory.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
*/
$config['sess_driver'] = 'database';
$config['sess_cookie_name'] = 'invonos_session';  // единый cookie на все инстансы
$config['sess_expiration'] = 864000;              // 10 дней
$config['sess_save_path'] = 'ip_sessions';        // таблица в БД
$config['sess_match_ip'] = false;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = true;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']   = '';
$config['cookie_domain']   = '.invonos.com';  // видна для всех поддоменов / директорий
$config['cookie_path']     = '/';
$config['cookie_secure']   = false;           // true если HTTPS
$config['cookie_httponly'] = false;

/*
|--------------------------------------------------------------------------
| Other defaults (optional)
|--------------------------------------------------------------------------
*/
$config['encryption_key'] = 'YOUR_RANDOM_KEY_HERE'; // обязательно для сессий
$config['language'] = 'english';
$config['charset']  = 'UTF-8';
$config['log_threshold'] = 1;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
| 'csrf_regenerate' = Regenerate token on every submission
| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks

$config['csrf_protection'] = env('CSRF_PROTECTION', true);
$config['csrf_token_name'] = '_ip_csrf';
$config['csrf_cookie_name'] = 'ip_csrf_cookie';
$config['csrf_expire'] = 3600;
$config['csrf_regenerate'] = true;
$config['csrf_exclude_uris'] = array();

*/