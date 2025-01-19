# Zero\Lib\Cookie Class

The `Cookie` class provides methods to securely set, retrieve, and delete encrypted cookies in PHP. This class ensures that cookie values are encrypted and decrypted using the `Crypto` class, adding a layer of security for sensitive data.

## Features

- Set encrypted cookies with optional expiration, path, domain, and security settings.
- Retrieve the decrypted value of an encrypted cookie.
- Delete cookies securely with configurable settings.

## Usage

### Setting a Cookie

```php
use Zero\Lib\Cookie;

Cookie::set(
    'cookie_name',   // Cookie name
    'cookie_value',  // Cookie value
    3600,            // Expiry time in seconds (default 3600s or 1 hour)
    '/',             // Path (default '/')
    '',              // Domain (default '')
    false,           // Secure flag (default false)
    true             // HTTPOnly flag (default true)
);
```

### Retrieving a Cookie

```php
use Zero\Lib\Cookie;

$value = Cookie::get('cookie_name');  // Returns the decrypted value or null if not found
```

### Deleting a Cookie

```php
use Zero\Lib\Cookie;

Cookie::delete(
    'cookie_name',  // Cookie name
    '/',            // Path (default '/')
    '',             // Domain (default '')
    false,          // Secure flag (default false)
    true            // HTTPOnly flag (default true)
);
```

## Parameters

- **`set` method**:

  - `$name` (string): The name of the cookie.
  - `$value` (string): The value to be stored in the cookie.
  - `$expiry` (int): The expiration time in seconds (default is 3600).
  - `$path` (string): The path on the server in which the cookie will be available (default is `/`).
  - `$domain` (string): The domain that the cookie is available to (default is `''`).
  - `$secure` (bool): Indicates that the cookie should only be transmitted over HTTPS (default is `false`).
  - `$httpOnly` (bool): When true, the cookie is only accessible through the HTTP protocol (default is `true`).

- **`get` method**:

  - `$name` (string): The name of the cookie to retrieve.

- **`delete` method**:
  - `$name` (string): The name of the cookie to delete.
  - `$path` (string): The path on the server where the cookie was available (default is `/`).
  - `$domain` (string): The domain where the cookie was available (default is `''`).
  - `$secure` (bool): Whether the cookie should only be transmitted over HTTPS (default is `false`).
  - `$httpOnly` (bool): Whether the cookie is accessible only via the HTTP protocol (default is `true`).
