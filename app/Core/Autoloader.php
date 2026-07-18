<?php
/**
 * Fix&Go — PSR-4 Autoloader
 * Maps App\... namespace to app/... directory.
 */
spl_autoload_register(function (string $class): void {
    // Only handle App\ namespace
    if (strpos($class, 'App\\') !== 0) return;

    $relative = substr($class, 4); // strip 'App\'
    $file = dirname(__DIR__) . '/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
