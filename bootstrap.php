<?php
// bootstrap.php
define('ROOT_PATH', __DIR__);

// Autoloader
spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    $parts = explode('\\', $class);
    $className = array_pop($parts);

    $baseDirs = [
        // Module models
        ROOT_PATH . '/Application/Model/modelM1/',
        ROOT_PATH . '/Application/Model/modelM2/',
        ROOT_PATH . '/Application/Model/modelM3/',
        ROOT_PATH . '/Application/Model/modelM4/',
        // Models (with 's' - for M1 Staff/Supplier)
        ROOT_PATH . '/Application/Models/',
        // Controllers
        ROOT_PATH . '/Application/Controller/',
        ROOT_PATH . '/Application/Controller/M1/',
        ROOT_PATH . '/Application/Controller/M2/',
        ROOT_PATH . '/Application/Controller/M3/',
        ROOT_PATH . '/Application/Controller/M4/',
        // Middleware & API gateways
        ROOT_PATH . '/Application/Middleware/',
        ROOT_PATH . '/Application/Middleware/API_gateways/',
        // Helpers
        ROOT_PATH . '/Application/Helpers/',
    ];

    foreach ($baseDirs as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        // fallback for lowercase filenames (e.g., authController.php)
        $lower = $dir . strtolower($className) . '.php';
        if (file_exists($lower)) {
            require_once $lower;
            return;
        }
    }
});

// Load shared helper functions
require_once ROOT_PATH . '/Application/Helpers/functions.php';
require_once ROOT_PATH . '/Data/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}