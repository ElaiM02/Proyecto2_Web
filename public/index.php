<?php
session_start();

use App\Core\Router;

require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/Controller.php';
require __DIR__ . '/../app/Core/Model.php';
require __DIR__ . '/../app/Core/View.php';

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

// Debug mode configuration
if (isset($env['DEBUG']) && filter_var($env['DEBUG'], FILTER_VALIDATE_BOOLEAN)) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Database connection
require_once __DIR__ . '/../config/database.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$router = new Router();

// Define routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/tickets', 'TicketController@index');
// Crear Ticket
$router->get('/tickets/create', 'TicketController@create');   // Mostrar formulario
$router->post('/tickets/create', 'TicketController@store');   // Guardar ticket
$router->get('/tickets/show', 'TicketController@show');
$router->get('/tickets/asignados', 'TicketController@misAsignados');
$router->post('/tickets/asignar', 'TicketController@asignar');
$router->post('/tickets/aceptar-solucion', 'TicketController@aceptarSolucion');
$router->post('/tickets/denegar-solucion', 'TicketController@denegarSolucion');


// Auth Routes
$router->add('GET', '/login', 'AuthController@login');
$router->add('POST', '/login', 'AuthController@authenticate');
$router->add('GET', '/logout', 'AuthController@logout');

// Profile Routes
$router->add('GET', '/profile', 'ProfileController@edit');
$router->add('POST', '/profile/update', 'ProfileController@update');

//User Routes
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/create', 'UserController@store');
$router->get('/users/edit/{id}', 'UserController@edit');
$router->post('/users/edit/{id}', 'UserController@update');
$router->get('/users/deactivate/{id}', 'UserController@deactivate');


try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
