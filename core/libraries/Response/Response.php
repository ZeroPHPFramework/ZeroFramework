<?php

namespace Zero\Lib;

class Response
{
    /**
     * Send a JSON response
     *
     * @param array $data
     * @param int $status
     */
    public static function json(array $data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Send a plain text response
     *
     * @param string $text
     * @param int $status
     */
    public static function text($text, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/plain');
        echo $text;
    }

    /**
     * Send an HTML response
     *
     * @param string $html
     * @param int $status
     */
    public static function html($html, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/html');
        echo $html;
    }

    public static function redirect($url)
    {
        header('Location: ' . $url);
    }

    public static function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}