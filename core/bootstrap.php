<?php
// Require the autoload file to initialize the application core and dependencies
require_once(core_path('bootstrap/autoload.php'));

// Import the Router class from the Zero\Lib namespace
use Zero\Lib\Router;

/**
 * Load environment variables from the .env file or other configuration sources.
 * This function sets up the application's environment.
 */
loadEnvFiles();

/**
 * Include the application routes.
 * This file typically defines all the routes for the web application.
 */
require_once(base('routes/web.php'));

/**
 * Dispatch the incoming request to the appropriate route handler.
 * - Extract the current URL from the request URI.
 * - Remove any query parameters by splitting the URL at '?'.
 * - Use the Router to match and execute the route handler based on the URL path and HTTP method.
 */
$finalUrl = $_SERVER['REQUEST_URI']; // Get the requested URL from the server
$finalUrl = explode('?', $finalUrl); // Split the URL to isolate the path (ignore query parameters)
Router::dispatch($finalUrl[0], $_SERVER['REQUEST_METHOD']); // Dispatch the route
