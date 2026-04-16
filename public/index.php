<?php

use Core\Session;
use Core\ValidationException;

const BASE_PATH = __DIR__ . "/../";

require BASE_PATH . "vendor/autoload.php";

session_start();

require BASE_PATH . "Core/functions.php";

/* Composer Autoload will be used as stated in the above line requiring vendor autoload
// To update/generate autoload, run: composer install which will includes autoload files
// if autoload files was not added during composer install, run: composer dump-autoload
spl_autoload_register(function($class) {
    // Translate Core\Database (the "\" is caused by the namespace) to Core/Database
    //require base_path("Core/{$class}.php");
    $class = str_replace("\\", "/", $class);    // str_replace("\\", DIRECTORY_SEPARATOR, $class);
    require base_path("{$class}.php");
});
*/

require base_path("bootstrap.php");

//require base_path("Core/router.php");

$router = new \Core\Router; // alternative way of class instance instead of declaring "use Core\Router" above

$routes = require base_path("routes.php");
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

//routeToController($uri, $routes);

$method = $_POST["_method"] ?? $_SERVER['REQUEST_METHOD'];

try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    Session::flash("errors", $exception->errors);
    Session::flash("old", $exception->old);

    return redirect($router->previousUrl());
}

Session::unflash();