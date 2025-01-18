# Zero PHP Router

A lightweight, flexible PHP routing system with support for route groups, middleware, and parameter handling.

## Features

- HTTP method support (GET, POST, PUT, PATCH, DELETE)
- Route groups with prefix and middleware
- Route parameters
- Middleware support (both global and route-specific)
- Namespaced controllers
- Exception handling
- Nested route groups

## Basic Usage

```php
use Zero\Lib\Router;

// Define routes
Router::get('users', [UserController::class, 'index']);
Router::post('users', [UserController::class, 'store']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::put('users/{id}', [UserController::class, 'update']);
Router::delete('users/{id}', [UserController::class, 'destroy']);

// Dispatch the route
Router::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```

## Route Groups

Group routes with shared attributes like prefixes and middleware:

```php
Router::group(['prefix' => 'api', 'middleware' => ['AuthMiddleware']], function () {
    Router::group(['prefix' => 'v1'], function () {
        Router::get('users', [UserController::class, 'index']);
        Router::post('users', [UserController::class, 'store']);

        // This will be accessible at /api/v1/users/{id}
        Router::get('users/{id}', [UserController::class, 'show']);
    });
});
```

## Route Parameters

Define dynamic route parameters using curly braces:

```php
Router::get('users/{id}/posts/{postId}', [UserController::class, 'showPost']);
```

The parameters will be passed to your controller method in order:

```php
class UserController {
    public function showPost($userId, $postId) {
        // Access $userId and $postId here
    }
}
```

## Middleware

Create middleware classes that implement a `handle` method:

```php
class AuthMiddleware {
    public function handle() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
```

Apply middleware to routes or groups:

```php
// Single route middleware
Router::get('dashboard', [DashboardController::class, 'index'], ['AuthMiddleware']);

// Group middleware
Router::group(['middleware' => ['AuthMiddleware']], function () {
    Router::get('profile', [UserController::class, 'profile']);
    Router::put('profile', [UserController::class, 'updateProfile']);
});
```

## Error Handling

The router includes built-in error handling for:

- 404 Not Found responses
- Controller/method not found exceptions
- Middleware validation
- Route processing errors

## Controllers

Controllers should be classes with public methods that handle the route actions:

```php
namespace App\Controllers;

class UserController {
    public function index() {
        // Handle GET /users
    }

    public function show($id) {
        // Handle GET /users/{id}
    }
}
```
