<?php
/**
 * Fix&Go — Base Controller
 * All controllers extend this.
 */
namespace App\Core;

abstract class Controller
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Send JSON response and exit.
     */
    protected function json(bool $success, string $message, array $data = [], int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
        exit;
    }

    /**
     * Require authenticated session or return 401.
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(false, 'Unauthorized. Please log in.', ['expired' => true], 401);
        }
    }

    /**
     * Require a specific role.
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        $userRole = $_SESSION['user_role'] ?? '';
        if (!in_array($userRole, $roles, true)) {
            $this->json(false, 'Access denied.', [], 403);
        }
    }

    /**
     * Render a view file.
     */
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/Views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View not found: $path";
            exit;
        }
        require $viewFile;
    }
}
