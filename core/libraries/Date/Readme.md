# Zero\Lib\Date

Zero\Lib\Date is a lightweight PHP 8 library for date and time manipulation without using external dependencies. The library is designed to be simple, efficient, and adheres to ISO 8601 format by default.

## Features

- Get the current date and time (`now` method).
- Parse custom date strings (`parse` method).
- Add or subtract days from a date.
- Format dates in custom formats.
- Get human-readable differences between two dates.
- Set timezones dynamically.

## Requirements

- PHP 8.0 or later

## Installation

Simply include the `Date` class in your project directory under the namespace `Zero\Lib`. No additional dependencies are required.

## Usage

### Create a Date Instance

```php
use Zero\Lib\Date;

// Current date and time
$date = Date::now();
echo $date->format(); // Default ISO 8601 format

// Parse a custom date string
$date = Date::parse('2025-01-01');
echo $date->format();
```

### Add or Subtract Days

```php
use Zero\Lib\Date;

$date = Date::now();

// Add 5 days
$date->addDays(5);
echo "Add 5 days: " . $date->format();

// Subtract 10 days
$date->subtractDays(10);
echo "Subtract 10 days: " . $date->format();
```

### Human-Readable Differences

```php
use Zero\Lib\Date;

$date1 = Date::parse('2025-01-01');
$date2 = Date::now();

echo $date1->diffForHumans($date2); // e.g., "1 year ago"
```

### Set a Custom Timezone

```php
use Zero\Lib\Date;

$date = Date::now();
$date->setTimeZone('America/New_York');
echo $date->format();
```

### Format Dates

```php
use Zero\Lib\Date;

$date = Date::now();
echo $date->format('Y-m-d H:i:s'); // Custom format
```

## Example Code

```php
use Zero\Lib\Date;

$date = Date::now();
echo $date->format() . "\n";

$date->addDays(5);
echo "Add 5 days: " . $date->format() . "\n";

$date->subtractDays(10);
echo "Subtract 10 days: " . $date->format() . "\n";

$anotherDate = Date::parse('2025-01-01');
echo "Difference: " . $date->diffForHumans($anotherDate) . "\n";
```
