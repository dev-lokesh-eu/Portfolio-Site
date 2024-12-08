<?php
// Check if .env file exists
$envFile = __DIR__ . '/../../../.env';

if (file_exists($envFile)) {
    // Read each line from the .env file
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key-value pairs
        $keyValue = explode('=', $line, 2);
        if (count($keyValue) === 2) {
            $key = trim($keyValue[0]);
            $value = trim($keyValue[1]);
            putenv("$key=$value");
        }
    }
} else {
    error_log("Environment file (.env) not found!");
}
?>
