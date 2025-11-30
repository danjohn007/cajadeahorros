<?php
// Router script for PHP built-in server
// This mimics Apache's mod_rewrite behavior

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Check if it's a real file or directory
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    // Serve static files directly (not PHP files)
    if (!preg_match('/\.php$/', $path)) {
        return false;
    }
}

// Extract the URL path for routing
$url = trim($path, '/');
$_GET['url'] = $url;

// Include the main index.php  
require __DIR__ . '/index.php';
