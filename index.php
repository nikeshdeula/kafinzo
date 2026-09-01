<?php

error_reporting(0);
ini_set('display_errors', '0');

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');

session_start();

define('BASE_PATH', __DIR__ . '/');

// Simple Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $class = str_replace('App\\', 'app/', $class);
    $file = BASE_PATH . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Helper function to require views
function view($name, $data = [])
{
    extract($data);
    require BASE_PATH . "views/{$name}.php";
}

// Helper function to redirect
function redirect($path)
{
    header("Location: {$path}");
    exit();
}

$router = new \App\Core\Router();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

// Include routes
require BASE_PATH . 'routes/routes.php';

$router->route($uri, $method);
