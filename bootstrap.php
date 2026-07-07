<?php
// bootstrap.php – place this in your project root (KTMeDOIS/)

define('ROOT_PATH', __DIR__);

spl_autoload_register(function ($class) {
    // Remove namespace (we'll add namespaces later if needed)
    $class = ltrim($class, '\\');
    $parts = explode('\\', $class);
    $className = array_pop($parts);

    // List of all directories where classes might live
    $baseDirs = [
        // Models
        ROOT_PATH . '/Application/Model/modelM1/',
        ROOT_PATH . '/Application/Model/modelM2/',
        ROOT_PATH . '/Application/Model/modelM3/',
        ROOT_PATH . '/Application/Model/modelM4/',
        
        // Controllers
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
        // Try exact match (e.g. AuthController.php)
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        
        // Try lowercase match (e.g. authController.php) – solves the case issue
        $lowerFile = $dir . strtolower($className) . '.php';
        if (file_exists($lowerFile)) {
            require_once $lowerFile;
            return;
        }
    }
});

// Load shared helpers
require_once ROOT_PATH . '/Application/Helpers/helpers.php';

// Load database connection
require_once ROOT_PATH . '/Data/db.php';