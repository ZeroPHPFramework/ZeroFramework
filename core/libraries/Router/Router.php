<?php

namespace Zero\Lib;

class Router
{
    private static $routes = [];
    private static $middlewares = [];
    private static $prefix = '';
    private static $groupMiddlewares = [];

    /**
     * Create a route group with shared attributes
     *
     * @param array $attributes
     * @param callable $callback
     */
    public static function group(array $attributes, callable $callback)
    {
        // Store the current state
        $previousPrefix = self::$prefix;
        $previousMiddlewares = self::$groupMiddlewares;

        // Update the current state with group attributes
        self::$prefix .= $attributes['prefix'] ?? '';
        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) 
                ? $attributes['middleware'] 
                : [$attributes['middleware']];
            self::$groupMiddlewares = array_merge(self::$groupMiddlewares, $middlewares);
        }

        // Execute the group's callback
        $callback();

        // Restore the previous state
        self::$prefix = $previousPrefix;
        self::$groupMiddlewares = $previousMiddlewares;
    }

    /**
     * Register a route for a specific method
     *
     * @param string $method
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    private static function addRoute($method, $route, $action, $middlewares = [])
    {
        // Combine the current prefix with the route
        $fullRoute = self::$prefix . '/' . trim($route, '/');
        $fullRoute = '/' . trim($fullRoute, '/');
        
        // Combine group middlewares with route-specific middlewares
        $allMiddlewares = array_merge(self::$groupMiddlewares, $middlewares);

        self::$routes[strtoupper($method)][$fullRoute] = $action;
        self::$middlewares[strtoupper($method)][$fullRoute] = $allMiddlewares;
    }

    /**
     * Register a GET route
     *
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    public static function get($route, $action, $middlewares = [])
    {
        self::addRoute('GET', $route, $action, $middlewares);
    }

    /**
     * Register a POST route
     *
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    public static function post($route, $action, $middlewares = [])
    {
        self::addRoute('POST', $route, $action, $middlewares);
    }

    /**
     * Register a PUT route
     *
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    public static function put($route, $action, $middlewares = [])
    {
        self::addRoute('PUT', $route, $action, $middlewares);
    }

    /**
     * Register a PATCH route
     *
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    public static function patch($route, $action, $middlewares = [])
    {
        self::addRoute('PATCH', $route, $action, $middlewares);
    }

    /**
     * Register a DELETE route
     *
     * @param string $route
     * @param array $action
     * @param array $middlewares
     */
    public static function delete($route, $action, $middlewares = [])
    {
        self::addRoute('DELETE', $route, $action, $middlewares);
    }

    /**
     * Dispatch the current request
     *
     * @param string $requestUri
     * @param string $requestMethod
     * @return mixed
     */
    public static function dispatch($requestUri, $requestMethod)
    {
        $requestUri = trim($requestUri, '/');
        $routes = self::$routes[$requestMethod] ?? [];

        foreach ($routes as $route => $action) {
            try {
                // Ensure the route string is correctly formatted for the regex pattern
                $routePattern = trim($route, '/');
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
                $pattern = "#^" . $pattern . "(?:/)?$#";

                // Perform the regex matching
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remove the full match

                    self::validateMiddlewares($route, $requestMethod);
                    return self::callAction($action, $matches);
                }
            } catch (\Exception $e) {
                error_log("Error processing route $route: " . $e->getMessage());
                return 'Error: An unexpected issue occurred while processing the request.';
            }
        }

        // If no route matches, return a 404 response
        http_response_code(404);
        return "404 Not Found";
    }

    /**
     * Validate and execute middlewares
     *
     * @param string $route
     * @param string $requestMethod
     * @throws \Exception
     */
    private static function validateMiddlewares($route, $requestMethod)
    {
        $middlewares = self::$middlewares[$requestMethod][$route] ?? [];

        foreach ($middlewares as $middleware) {
            if (!class_exists($middleware)) {
                throw new \Exception("Middleware {$middleware} not found");
            }

            $middlewareInstance = new $middleware();

            if (!method_exists($middlewareInstance, 'handle')) {
                throw new \Exception("Method handle not found in middleware {$middleware}");
            }

            $middlewareInstance->handle();
        }
    }

    /**
     * Call the specified controller and method
     *
     * @param array $action
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    private static function callAction($action, $params)
    {
        [$controller, $method] = $action;

        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }

        $controllerInstance = new $controller();
        

        if (!method_exists($controllerInstance, $method)) {
            throw new \Exception("Method {$method} not found in controller {$controller}");
        }

        $params = array_values($params);
        try {
            return call_user_func_array([$controllerInstance, $method], $params);
        } catch (\Exception $e) {
            return dd($e);
        }
    }
}