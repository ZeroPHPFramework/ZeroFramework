# Database Class Documentation

This repository contains a `Database` class and a `DatabaseConnection` class that allows interaction with a database using PDO. The `Database` class provides static methods for basic CRUD operations, while the `DatabaseConnection` class handles the database connection and query execution.

## Classes

### `Database`

The `Database` class provides a set of static methods for interacting with the database. These methods call the corresponding methods in the `DatabaseConnection` class.

#### Methods

- `fetch($query, $bind=null, $params=null, $debug=false)`
  - Fetches data based on a given query.
- `select($query, $bind=null, $params=null, $debug=false)`
  - Executes a select query.
- `first($query, $bind=null, $params=null, $debug=false)`

  - Fetches the first row of data based on a given query.

- `create($query, $bind=null, $params=null, $debug=false)`

  - Executes a query that inserts data into the database.

- `update($query, $bind=null, $params=null, $debug=false)`

  - Executes a query that updates data in the database.

- `delete($query, $bind=null, $params=null, $debug=false)`

  - Executes a query that deletes data from the database.

- `query($query, $bind=null, $params=null, $debug=false)`

  - Executes a custom query and returns the result.

- `escape($string)`

  - Escapes a string for safe database usage.

- `write()`

  - Sets the connection for write operations.

- `read()`
  - Sets the connection for read operations.

### `DatabaseConnection`

The `DatabaseConnection` class is responsible for establishing a connection to the database and executing queries.

#### Properties

- `$connection`

  - Holds the PDO connection object.

- `$connector`

  - Defines the connection type (either read or write).

- `$driver`
  - Holds the database driver configuration.

#### Methods

- `__construct()`

  - Initializes the connection and driver.

- `connect()`

  - Establishes the database connection using the appropriate driver.

- `setConnector($connector)`

  - Sets the connection type (read/write).

- `isWrite($query)`

  - Determines if the given query is a write operation (e.g., INSERT, UPDATE, DELETE).

- `escape($string)`

  - Escapes a string for use in SQL queries.

- `query($query, $bind=null, $params=null, $state='fetch', $debug=false)`

  - Executes a query and returns the result.

- `fetch($query, $bind=null, $params=null, $debug=false)`

  - Fetches data based on a given query.

- `first($query, $bind=null, $params=null, $debug=false)`

  - Fetches the first result of a query.

- `create($query, $bind=null, $params=null, $debug=false)`

  - Executes an insert query.

- `update($query, $bind=null, $params=null, $debug=false)`

  - Executes an update query.

- `delete($query, $bind=null, $params=null, $debug=false)`
  - Executes a delete query.
