<?php
/**
 * Fix&Go — Database Singleton
 * Provides a single shared PDO instance.
 */
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Use APP_ROOT if defined (from bootstrap), else resolve from file location
            $root   = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
            $config = require $root . '/app/Core/config.php';

            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    $config['db_host'],
                    $config['db_name'],
                    $config['db_charset']
                );
                self::$instance = new PDO($dsn, $config['db_user'], $config['db_pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log('[Fix&Go DB] ' . $e->getMessage());
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
                exit;
            }
        }
        return self::$instance;
    }
}
