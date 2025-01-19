# Response Class Documentation

The `Response` class is a utility designed to manage and send HTTP responses in PHP. It supports different content types such as JSON, HTML, plain text, and XML, and allows setting custom headers and status codes.

## Methods

### 1. `headers(array $headers): self`

Sets custom headers for the response.

- **Parameters:**
  - `array $headers`: Key-value pairs of headers to be added to the response.
- **Returns:** `self` (for method chaining)

### 2. `status(int $status): self`

Sets the HTTP status code for the response.

- **Parameters:**
  - `int $status`: The HTTP status code to be sent (e.g., `200`, `404`).
- **Returns:** `self` (for method chaining)

### 3. `json($data): self`

Sets the response content as JSON.

- **Parameters:**
  - `$data`: The data to be encoded as JSON (array or object).
- **Returns:** `self` (for method chaining)

### 4. `text(string $text): self`

Sets the response content as plain text.

- **Parameters:**
  - `string $text`: The plain text content.
- **Returns:** `self` (for method chaining)

### 5. `html(string $html): self`

Sets the response content as HTML.

- **Parameters:**
  - `string $html`: The HTML content.
- **Returns:** `self` (for method chaining)

### 6. `xml(string $xml): self`

Sets the response content as XML.

- **Parameters:**
  - `string $xml`: The XML content.
- **Returns:** `self` (for method chaining)

### 7. `api(string $status, $data = null): self`

Sets the response content for an API with a standardized format.

- **Parameters:**
  - `string $status`: The status of the response (e.g., "success", "error").
  - `$data`: The data to be sent in the response (optional).
- **Returns:** `self` (for method chaining)

### 8. `__invoke()`

Invokes the response, sending the status code, headers, and content to the client.

- **Note:** This method is triggered when the instance is called as a function.

### Private Method

#### `sendHeaders(array $headers)`

Adds and sends the custom headers to the response.

- **Parameters:**
  - `array $headers`: Additional custom headers to send.

## Example Usage

```php
use Zero\Lib\Response;

// Send a JSON response
Response::status(200)->json(['message' => 'Success'])->__invoke();

// Send an error response in API format
Response::status(400)->api('error', 'Invalid input')->__invoke();

// Send HTML content
Response::html('<h1>Welcome</h1>')->__invoke();
```

## Features

- Supports multiple content types: JSON, HTML, plain text, and XML.
- Allows setting custom headers for the response.
- Can send standardized API responses with both success and error formats.
- Method chaining support for better readability and usability.
