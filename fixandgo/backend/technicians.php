<?php
/**
 * Fix&Go — Public Technicians API
 * Returns all active users with role = 'phone_technician'
 * along with their profile info and shop assignment.
 *
 * This is a PUBLIC endpoint — no auth required (landing page).
 * GET ?action=list  → array of technicians
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Suppress PHP warnings/notices from leaking into JSON output
error_reporting(0);

try {
    $pdo    = require __DIR__ . '/db.php';
    $action = $_GET['action'] ?? 'list';

    if ($action !== 'list') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        exit;
    }

    // Check if technician_profiles table exists — graceful fallback if migration not run yet
    $hasProfileTable = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.tables
         WHERE table_schema = DATABASE()
           AND table_name = 'technician_profiles'"
    )->fetchColumn() > 0;

    if ($hasProfileTable) {
        $sql = "SELECT u.id,
                       u.first_name,
                       u.last_name,
                       u.avatar_url,
                       u.created_at,
                       COALESCE(tp.specialization, '')         AS specialization,
                       COALESCE(tp.experience_years, 0)        AS experience_years,
                       COALESCE(tp.bio, '')                    AS bio,
                       COALESCE(tp.availability, 'available')  AS availability,
                       COALESCE(tp.rating_avg, 0.00)           AS rating_avg,
                       COALESCE(tp.rating_count, 0)            AS rating_count,
                       s.name  AS shop_name,
                       s.city  AS shop_city
                FROM users u
                LEFT JOIN technician_profiles tp ON tp.user_id = u.id
                LEFT JOIN shop_members sm         ON sm.user_id = u.id
                LEFT JOIN shops s                 ON s.id = sm.shop_id AND s.is_active = 1
                WHERE u.role      = 'phone_technician'
                  AND u.is_active = 1
                ORDER BY u.first_name ASC, u.last_name ASC";
    } else {
        // Migration not run yet — query without profile join
        $sql = "SELECT u.id,
                       u.first_name,
                       u.last_name,
                       u.avatar_url,
                       u.created_at,
                       ''           AS specialization,
                       0            AS experience_years,
                       ''           AS bio,
                       'available'  AS availability,
                       0.00         AS rating_avg,
                       0            AS rating_count,
                       s.name  AS shop_name,
                       s.city  AS shop_city
                FROM users u
                LEFT JOIN shop_members sm ON sm.user_id = u.id
                LEFT JOIN shops s         ON s.id = sm.shop_id AND s.is_active = 1
                WHERE u.role      = 'phone_technician'
                  AND u.is_active = 1
                ORDER BY u.first_name ASC, u.last_name ASC";
    }

    $technicians = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // Count completed repairs per technician
    $repairMap = [];
    try {
        $rows = $pdo->query(
            "SELECT technician_id, COUNT(*) AS repairs_done
             FROM bookings
             WHERE status = 'completed' AND technician_id IS NOT NULL
             GROUP BY technician_id"
        )->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $repairMap[(int)$row['technician_id']] = (int)$row['repairs_done'];
        }
    } catch (Exception $e) {
        // bookings table may not exist yet — ignore
    }

    foreach ($technicians as &$tech) {
        $tech['repairs_done']     = $repairMap[(int)$tech['id']] ?? 0;
        $tech['experience_years'] = (int)$tech['experience_years'];
        $tech['rating_avg']       = (float)$tech['rating_avg'];
        $tech['rating_count']     = (int)$tech['rating_count'];
    }
    unset($tech);

    echo json_encode([
        'success'     => true,
        'technicians' => $technicians,
        'total'       => count($technicians),
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
    ]);
}
