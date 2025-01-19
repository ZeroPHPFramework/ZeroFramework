# Crypto Class

This class provides utility methods for hashing, validation, encryption, and decryption of data using various techniques. The class uses bcrypt for password hashing and OpenSSL for data encryption and decryption.

## Methods

### `getSalt()`

Private method that retrieves the salt value for encryption and hashing. It fetches the value from the environment variable `APP_KEY`. If the variable is not set, an exception is thrown.

#### Returns:

- `string`: The salt value.

#### Throws:

- `Exception`: If `APP_KEY` is not defined in the environment variables.

---

### `hash(string $value): string`

Generates a bcrypt hash of the given value, prefixed with a salt.

#### Parameters:

- `string $value`: The value to be hashed.

#### Returns:

- `string`: The bcrypt hashed value.

#### Throws:

- `Exception`: If there is any error during the hashing process.

---

### `validate(string $plainValue, string $hashedValue): bool`

Validates if the plain value matches the salted bcrypt hash.

#### Parameters:

- `string $plainValue`: The plain value to validate.
- `string $hashedValue`: The hashed value to compare against.

#### Returns:

- `bool`: Returns `true` if the values match, `false` otherwise.

#### Throws:

- `Exception`: If there is any error during the validation process.

---

### `encrypt(string $value): string`

Encrypts the given value using OpenSSL with AES-256-CBC encryption. The result is Base64-encoded.

#### Parameters:

- `string $value`: The value to encrypt.

#### Returns:

- `string`: The encrypted value in Base64 encoding.

#### Throws:

- `Exception`: If there is any error during the encryption process.

---

### `decrypt(string $value): string`

Decrypts the given Base64-encoded encrypted value using OpenSSL with AES-256-CBC decryption.

#### Parameters:

- `string $value`: The encrypted value in Base64 encoding.

#### Returns:

- `string`: The decrypted value.

#### Throws:

- `Exception`: If there is any error during the decryption process.

---

## Requirements

- PHP 7.0 or higher
- OpenSSL extension enabled
- `APP_KEY` environment variable defined for proper encryption and hashing

---

## Example Usage

```php
use Zero\Lib\Crypto;

try {
    // Hash a value
    $hashed = Crypto::hash('mySecretValue');

    // Validate the hash
    $isValid = Crypto::validate('mySecretValue', $hashed);

    // Encrypt a value
    $encrypted = Crypto::encrypt('mySecretValue');

    // Decrypt a value
    $decrypted = Crypto::decrypt($encrypted);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```
