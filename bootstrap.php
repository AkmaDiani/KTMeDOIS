<?php
// bootstrap.php – place in project root (KTMeDOIS/)

define('ROOT_PATH', __DIR__);

spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    $parts = explode('\\', $class);
    $className = array_pop($parts);

    $baseDirs = [
        ROOT_PATH . '/Application/Model/modelM1/',
        ROOT_PATH . '/Application/Model/modelM2/',
        ROOT_PATH . '/Application/Model/modelM4/',
        ROOT_PATH . '/Application/Controller/M1/',
        ROOT_PATH . '/Application/Controller/M2/',
        ROOT_PATH . '/Application/Controller/M3/',      
        ROOT_PATH . '/Application/Controller/M4/',
        ROOT_PATH . '/Application/Models/',
        ROOT_PATH . '/Application/Middleware/',
        ROOT_PATH . '/Application/Middleware/API_gateways/',
        ROOT_PATH . '/Application/Helpers/',
    ];

    foreach ($baseDirs as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        $lower = $dir . strtolower($className) . '.php';
        if (file_exists($lower)) {
            require_once $lower;
            return;
        }
        
        $lcFirst = $dir . lcfirst($className) . '.php';
        if (file_exists($lcFirst)) {
            require_once $lcFirst;
            return;
        }
    }
});

require_once ROOT_PATH . '/Application/Helpers/functions.php';
require_once ROOT_PATH . '/Data/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}