<?php
use Zero\Lib\Router;

use App\Controllers\HomeController;   


Router::get('/', [HomeController::class, 'index']);
// Router::get('/webhooks', [Webhook::class, 'index'], [AuthMiddleware::class]);