<?php
namespace Zero\Lib;

class Request
{
    /**
     * Get all request data
     *
     * @return array
     */
    public static function all()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Get a specific input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function input($key, $default = null)
    {
        return self::all()[$key] ?? $default;
    }

    /**
     * Get the request method
     *
     * @return string
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get the current URI
     *
     * @return string
     */
    public static function uri()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }
}