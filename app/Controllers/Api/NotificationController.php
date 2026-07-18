<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\NotificationModel;

class NotificationController extends Controller
{
    private NotificationModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new NotificationModel();
    }

    // GET /api/notifications?action=list|unread
    // POST /api/notifications?action=mark_read|mark_all_read|delete
    public function handle(): void
    {
        $this->requireAuth();
        $userId = (int) $_SESSION['user_id'];
        $role   = $_SESSION['user_role'] ?? '';

        if ($role === 'supervisor') {
            $this->json(false, 'Supervisors do not have notifications.', [], 403);
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        if ($method === 'GET') {
            if ($action === 'list') {
                $limit  = (int)($_GET['limit']  ?? 50);
                $offset = (int)($_GET['offset'] ?? 0);
                $this->json(true, 'OK', [
                    'notifications' => $this->model->list($userId, $limit, $offset),
                    'total'         => $this->model->countTotal($userId),
                    'limit'         => $limit,
                    'offset'        => $offset,
                ]);
            }
            if ($action === 'unread') {
                $this->json(true, 'OK', ['unread_count' => $this->model->countUnread($userId)]);
            }
            $this->json(false, 'Unknown action.', [], 400);
        }

        if ($method === 'POST') {
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            if ($action === 'mark_read') {
                $ids = $body['ids'] ?? [];
                if (!$ids) $this->json(false, 'No IDs provided.', [], 422);
                $this->json(true, 'Marked as read.', ['affected' => $this->model->markRead($userId, $ids)]);
            }
            if ($action === 'mark_all_read') {
                $this->json(true, 'All marked as read.', ['affected' => $this->model->markAllRead($userId)]);
            }
            if ($action === 'delete') {
                $ids = $body['ids'] ?? [];
                if (!$ids) $this->json(false, 'No IDs provided.', [], 422);
                $this->json(true, 'Deleted.', ['affected' => $this->model->delete($userId, $ids)]);
            }
            $this->json(false, 'Unknown action.', [], 400);
        }

        $this->json(false, 'Method not allowed.', [], 405);
    }
}
