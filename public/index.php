<?php
session_start();

// Define Root Constants for consistent asset loading
$scriptName = $_SERVER['SCRIPT_NAME'];
$publicRoot = dirname($scriptName) . '/';
$appRoot = dirname(dirname($scriptName)) . '/';

define('PUBLIC_ROOT', $publicRoot);
define('APP_ROOT', $appRoot);
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Autoloader
spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'App\\';
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Load Config/Database just to make sure
// use App\Config\Database; // Not strictly needed if autoload works

// Router
use App\Core\Router;

$router = new Router();

// Define Routes
// Main Entry Point (Login Page)
$router->add('GET', '/', 'AuthController@index');
$router->add('POST', '/', 'AuthController@index'); // Handle login/register post here for now

// Admin Routes
$router->add('GET', '/admin/dashboard', 'AdminController@dashboard');
$router->add('GET', '/admin/manage-users', 'AdminController@manageUsers');
$router->add('POST', '/admin/manage-users', 'AdminController@manageUsers');
$router->add('GET', '/admin/activity-logs', 'AdminController@activityLogs');
$router->add('GET', '/admin/user-status', 'AdminController@userStatus');
$router->add('GET', '/admin/user-details', 'AdminController@userDetails');
$router->add('GET', '/admin/dashboard-api', 'AdminController@dashboardApi');
$router->add('GET', '/admin/edit-user', 'AdminController@editUser');
$router->add('POST', '/admin/edit-user', 'AdminController@editUser');
$router->add('GET', '/admin/profile', 'AdminController@profile');
$router->add('POST', '/admin/profile', 'AdminController@profile');
$router->add('GET', '/admin/register-user', 'AdminController@registerUser');
$router->add('POST', '/admin/register-user', 'AdminController@registerUser');
$router->add('GET', '/admin/submissions', 'AdminController@submissions');
$router->add('GET', '/admin/view-activity', 'AdminController@viewActivity');
$router->add('POST', '/admin/view-activity', 'AdminController@viewActivity');
$router->add('GET', '/admin/edit-activity', 'AdminController@editActivity');
$router->add('POST', '/admin/edit-activity', 'AdminController@editActivity');

// User Routes
$router->add('GET', '/user/home', 'UserController@home');
$router->add('GET', '/user/profile', 'UserController@profile');
$router->add('POST', '/user/profile', 'UserController@profile');
$router->add('GET', '/user/add-activity', 'UserController@addActivity');
$router->add('POST', '/user/add-activity', 'UserController@addActivity');
$router->add('GET', '/user/edit-activity', 'UserController@editActivity');
$router->add('POST', '/user/edit-activity', 'UserController@editActivity');
$router->add('GET', '/user/view-activity', 'UserController@viewActivity');
$router->add('GET', '/user/submissions-progress', 'UserController@submissionsProgress');

// HR Routes
$router->add('GET', '/hr/dashboard', 'HRController@dashboard');
$router->add('GET', '/hr/profile', 'HRController@profile');
$router->add('POST', '/hr/profile', 'HRController@profile');

$router->dispatch();
