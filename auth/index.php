<?php
define('ENVIRONMENT', 'development');
defined('STDIN') OR define('STDIN', fopen('php://stdin', 'r'));

/*
|--------------------------------------------------------------------------
| Set the current directory correctly for CLI requests
|--------------------------------------------------------------------------
*/
if (defined('STDIN'))
{
    chdir(dirname(__FILE__));
}

// --------------------------------------------------------------------
// Fix for PHP-FPM: prevent CLI detection
// --------------------------------------------------------------------

if (php_sapi_name() !== 'cli') {
    $_SERVER['argv'] = [];
    $_SERVER['argc'] = 0;
}

/*
|--------------------------------------------------------------------------
| System folder name
|--------------------------------------------------------------------------
|
| Относительно auth/
|
*/
$system_path = '../dist_dev/vendor/codeigniter/framework/system';

/*
|--------------------------------------------------------------------------
| Application folder name
|--------------------------------------------------------------------------
*/
$application_folder = 'application';

/*
|--------------------------------------------------------------------------
| Resolve system path
|--------------------------------------------------------------------------
*/
if (($_temp = realpath($system_path)) !== FALSE)
{
    $system_path = $_temp . DIRECTORY_SEPARATOR;
}
else
{
    $system_path = rtrim($system_path, '/\\') . DIRECTORY_SEPARATOR;
}

if ( ! is_dir($system_path))
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly.';
    exit(3);
}

/*
|--------------------------------------------------------------------------
| Define path constants
|--------------------------------------------------------------------------
*/
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));

/*
|--------------------------------------------------------------------------
| Resolve application path
|--------------------------------------------------------------------------
*/
if (is_dir($application_folder))
{
    if (($_temp = realpath($application_folder)) !== FALSE)
    {
        $application_folder = $_temp;
    }
}
elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR))
{
    $application_folder = BASEPATH . trim($application_folder, '/\\') . DIRECTORY_SEPARATOR;
}
else
{
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your application folder path does not appear to be set correctly.';
    exit(3);
}

define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);
/*
|--------------------------------------------------------------------------
| Run CodeIgniter
|--------------------------------------------------------------------------
*/
require_once BASEPATH . 'core/CodeIgniter.php';
