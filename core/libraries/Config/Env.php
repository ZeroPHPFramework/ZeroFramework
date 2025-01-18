<?php

function loadEnvFiles(): array
{
    $files = ['.env', '.env.staging', '.env.production'];
    $envData = [];

    // Load files and merge based on priority
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($content as $line) {
                // Skip comments and invalid lines
                if (strpos(trim($line), '#') === 0 || !str_contains($line, '=')) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Handle array values
                if (preg_match('/^\[(.*)]$/', $value, $matches)) {
                    $items = array_map('trim', explode(',', $matches[1]));
                    $envData[$key] = $items;
                } else {
                    $envData[$key] = $value;
                }
            }
        }
    }

    // Reverse order to give higher priority to .env.production
    $_ENV['CONFIG'] = $envData;

    return $envData;
}

function getConfig(string $key, $default = null)
{
    return $_ENV['CONFIG'][$key] ?? $default;
}