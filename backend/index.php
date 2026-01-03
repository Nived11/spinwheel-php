<?php
// backend/index.php

// Error reporting (disable in production)
if (getenv('ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Include CORS headers
require_once __DIR__ . '/config/cors.php';

// Include routes
require_once __DIR__ . '/routes/api.php';

// Get request details
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path for production
$basePath = '/api';
$uri = str_replace($basePath, '', $uri);


// Route the request
handleRequest($method, $uri);
?>
