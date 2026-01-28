<?php declare(strict_types=1);
// ---------------------------------------------------------
// Simple .env loader
// ---------------------------------------------------------
$envFile = __DIR__ . '/../.env';

// $envFile = base_path('.env');
// dd($envFile);
if (!file_exists($envFile)) {
    // $exampleFile = base_path('.env.example');
     $exampleFile = __DIR__ . '.env.example';
// dd($exampleFile);
    if (file_exists($exampleFile)) {
        // Read content from .env.example
        $exampleContent = file_get_contents($exampleFile);

        if ($exampleContent === false) {
            die("<p style='color:red;'>Error: Failed to read .env.example file.</p>");
        }

        // Write content into .env
        $result = file_put_contents($envFile, $exampleContent);

        if ($result === false) {
            die("<p style='color:red;'>Error: Failed to create .env file from .env.example. Check folder permissions.</p>");
        }

        echo "<p style='color:orange;'>Warning: .env not found. A new one has been created from .env.example.</p>";
    } else {
        die("<p style='color:red;'>Error: No .env or .env.example file found. Please create one in the project root.</p>");
    }
}

// Continue with loader
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value, " \t\n\r\0\x0B\"'");
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}