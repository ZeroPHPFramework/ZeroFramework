<?php

namespace Zero\Lib;

class Response
{
    /**
     * Holds custom headers for responses.
     *
     * @var array
     */
    private static $customHeaders = [];

    /**
     * HTTP status code for the response.
     *
     * @var int
     */
    private static $statusCode = 200;

    /**
     * Response content to send.
     *
     * @var array|string|null
     */
    private static $content = null;

    /**
     * Add or override global custom headers for responses.
     *
     * @param array $headers Key-value pairs of headers to set.
     * @return self
     */
    public static function headers(array $headers): self
    {
        foreach ($headers as $key => $value) {
            self::$customHeaders[$key] = $value;
        }
        return new self;
    }

    /**
     * Set the HTTP status code for the response.
     *
     * @param int $status HTTP status code.
     * @return self
     */
    public static function status(int $status): self
    {
        self::$statusCode = $status;
        return new self;
    }

    /**
     * Set the response content as JSON.
     *
     * @param array|object $data The data to be encoded as JSON.
     * @return self
     */
    public static function json($data): self
    {
        self::$customHeaders['Content-Type'] = 'application/json';
        self::$content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return new self;
    }

    /**
     * Set the response content as plain text.
     *
     * @param string $text The plain text content.
     * @return self
     */
    public static function text(string $text): self
    {
        self::$customHeaders['Content-Type'] = 'text/plain';
        self::$content = $text;
        return new self;
    }

    /**
     * Set the response content as HTML.
     *
     * @param string $html The HTML content.
     * @return self
     */
    public static function html(string $html): self
    {
        self::$customHeaders['Content-Type'] = 'text/html';
        self::$content = $html;
        return new self;
    }

    /**
     * Set the response content as xml.
     * @param string $xml The xml content.
     * @return self
     */

    public static function xml(string $xml): self
    {
        self::$customHeaders['Content-Type'] = 'application/xml';
        self::$content = $xml;
        return new self;
    }

    /**
     * Set the response content for an API with a standardized format.
     *
     * @param int $code The status code.
     * @param string $status The status message.
     * @param array|string|null $data The data to be sent in the response.
     * @param string|null $error The error message, if any.
     * @return self
     */
    public static function api(string $status, $data = null): self
    {
        // Standard API response format
        $response = [
            'code' => self::$statusCode,
            'status' => $status,
            'data' => null,
            'error' => null
        ];

        if(self::$statusCode >= 400) {
            $response['error'] = $data;
            $response['status'] = $status;
        } else {
            $response['data'] = $data;
            $response['status'] = $status;
        }
       

        self::$customHeaders['Content-Type'] = 'application/json';
        self::$content = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return new self;
    }

    /**
     * Invoke the Response to send the content.
     * This method is triggered when the instance is called as a function.
     */
    public function __invoke()
    {
        // Send the response when invoked
        http_response_code(self::$statusCode);
        self::sendHeaders([]);
        echo self::$content;
        exit;
    }

    /**
     * Add and send custom headers.
     *
     * @param array $headers Key-value pairs of additional headers to send.
     */
    private static function sendHeaders(array $headers)
    {
        $allHeaders = array_merge(self::$customHeaders, $headers);
        foreach ($allHeaders as $key => $value) {
            header("{$key}: {$value}");
        }
    }
}
