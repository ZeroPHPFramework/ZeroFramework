<?php
require_once(core_path('bootstrap/autoload.php'));

use Zero\Lib\Router;


/**
 * Load routers
 */

require_once(base('routes/web.php'));


/**
 * Load all routes
 */
$finalUrl = $_SERVER['REQUEST_URI'];
$finalUrl = explode('?', $finalUrl);
Router::dispatch($finalUrl[0], $_SERVER['REQUEST_METHOD']);