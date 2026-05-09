<?php
/**
 * Fix&Go — Product Transfer API
 * Handles product transfers between Owner → Supervisor → Sales Person
 * 
 * GET  ?action=get_staff_list&role=supervisor|sales_person  → Get available staff for transfer
 * GET  ?action=get_my_products                              → Get products I currently hold
 * GET  ?action=get_pending_transfers                        → Get transfers pending my acceptance
 * GET  ?action=get_transfer_history&product_id=X            → Get transfer history for a product
 * POST action=send_to_staff                                 → Send product to supervisor/sales person
 * POST action=accept_transfer                               → Accept a pending transfer
 * POST action=reject_transfer                               → Reject a pending transfer
 */

// Catch all errors and return JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Custom error handler to return JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'PHP Error: ' . $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
});

// Custom exception handler
set_exception_handler(function($exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Exception: ' . $exception->getMessage(),
        'file' => basename($exception->getFile()),
        'line' => $exception->getLine()
    ]);
    exit;
});

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Authentication required
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

try {
    $pdo = require __DIR__ . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

// Test endpoint
if (isset($_GET['test'])) {
    echo json_encode([
        'success' => true,
        'message' => 'API is working',
        'user_id' => $userId,
        'user_role' => $userRole,
        'method' => $method
    ]);
    exit;
}

// ============================================================
// GET REQUESTS
// ============================================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    // ── Get staff list for transfer ──
    if ($action === 'get_staff_list') {
        // Only owner and supervisor can get staff lists
        if (!in_array($userRole, ['owner', 'supervisor'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $targetRole = $_GET['role'] ?? '';
        
        if ($userRole === 'owner') {
            // Owner can send to supervisors
            if ($targetRole !== 'supervisor') {
                echo json_encode(['success' => false, 'message' => 'Owners can only send to supervisors.']);
                exit;
            }
            
            // Get ALL active supervisors (no assignment needed)
            $stmt = $pdo->prepare("
                SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.created_at
                FROM users u
                WHERE u.role = 'supervisor'
                  AND u.is_active = 1
                  AND u.is_verified = 1
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute();
            
        } else if ($userRole === 'supervisor') {
            // Supervisor can send to sales persons
            if ($targetRole !== 'sales_person') {
                echo json_encode(['success' => false, 'message' => 'Supervisors can only send to sales persons.']);
                exit;
            }
            
            // Get ALL active sales persons (no assignment needed)
            $stmt = $pdo->prepare("
                SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.created_at
                FROM users u
                WHERE u.role = 'sales_person'
                  AND u.is_active = 1
                  AND u.is_verified = 1
                ORDER BY u.first_name, u.last_name
            ");
            $stmt->execute();
        }
        
        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'staff' => $staff,
            'count' => count($staff)
        ]);
        exit;
    }
    
    // ── Get my products (products I currently hold) ──
    if ($action === 'get_my_products') {
        // Only owner, supervisor, and sales_person can view their products
        if (!in_array($userRole, ['owner', 'supervisor', 'sales_person'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT sp.id, sp.category, sp.brand, sp.item_description, sp.qty, 
                   sp.srp, sp.image_path, sp.status, sp.holder_type,
                   sp.created_at, sp.updated_at,
                   u_supplier.first_name AS supplier_first_name,
                   u_supplier.last_name AS supplier_last_name,
                   u_supplier.email AS supplier_email
            FROM supplier_products sp
            JOIN users u_supplier ON u_supplier.id = sp.supplier_id
            WHERE sp.current_holder_id = ?
              AND sp.status IN ('owner_received', 'sent_to_owner')
            ORDER BY sp.updated_at DESC
        ");
        $stmt->execute([$userId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'products' => $products,
            'count' => count($products)
        ]);
        exit;
    }
    
    // ── Get pending transfers (transfers sent to me) ──
    if ($action === 'get_pending_transfers') {
        // Only supervisor and sales_person can receive transfers
        if (!in_array($userRole, ['supervisor', 'sales_person'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT pt.id, pt.product_id, pt.quantity, pt.status, pt.notes,
                   pt.transferred_at, pt.transfer_type,
                   sp.category, sp.brand, sp.item_description, sp.srp, sp.image_path,
                   u_from.first_name AS from_first_name,
                   u_from.last_name AS from_last_name,
                   u_from.email AS from_email,
                   u_from.role AS from_role
            FROM product_transfers pt
            JOIN supplier_products sp ON sp.id = pt.product_id
            JOIN users u_from ON u_from.id = pt.from_user_id
            WHERE pt.to_user_id = ?
              AND pt.status = 'pending'
            ORDER BY pt.transferred_at DESC
        ");
        $stmt->execute([$userId]);
        $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'transfers' => $transfers,
            'count' => count($transfers)
        ]);
        exit;
    }
    
    // ── Get transfer history for a product ──
    if ($action === 'get_transfer_history') {
        $productId = (int)($_GET['product_id'] ?? 0);
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required.']);
            exit;
        }
        
        // Verify user has access to this product
        $stmt = $pdo->prepare("
            SELECT sp.id, sp.current_holder_id, sp.supplier_id
            FROM supplier_products sp
            WHERE sp.id = ?
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }
        
        // Check if user is involved with this product
        $hasAccess = false;
        if ($userRole === 'owner' && $product['current_holder_id'] == $userId) {
            $hasAccess = true;
        } else if ($userRole === 'supervisor' && $product['current_holder_id'] == $userId) {
            $hasAccess = true;
        } else if ($userRole === 'sales_person' && $product['current_holder_id'] == $userId) {
            $hasAccess = true;
        } else if ($userRole === 'supplier' && $product['supplier_id'] == $userId) {
            $hasAccess = true;
        }
        
        if (!$hasAccess) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        // Get transfer history
        $stmt = $pdo->prepare("
            SELECT pth.id, pth.action, pth.quantity, pth.notes, pth.created_at,
                   u_from.first_name AS from_first_name,
                   u_from.last_name AS from_last_name,
                   u_from.email AS from_email,
                   u_from.role AS from_role,
                   u_to.first_name AS to_first_name,
                   u_to.last_name AS to_last_name,
                   u_to.email AS to_email,
                   u_to.role AS to_role
            FROM product_transfer_history pth
            LEFT JOIN users u_from ON u_from.id = pth.from_user_id
            JOIN users u_to ON u_to.id = pth.to_user_id
            WHERE pth.product_id = ?
            ORDER BY pth.created_at DESC
        ");
        $stmt->execute([$productId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'history' => $history,
            'count' => count($history)
        ]);
        exit;
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ============================================================
// POST REQUESTS
// ============================================================
if ($method === 'POST') {
    $rawBody = file_get_contents('php://input');
    $body = json_decode($rawBody, true) ?? [];
    $action = $body['action'] ?? '';
    
    // Debug logging
    error_log("POST Request - Raw Body: " . $rawBody);
    error_log("POST Request - Parsed Body: " . print_r($body, true));
    error_log("POST Request - Action: " . $action);
    
    if (empty($action)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Unknown action.',
            'debug' => [
                'raw_body' => $rawBody,
                'parsed_body' => $body,
                'action' => $action
            ]
        ]);
        exit;
    }
    
    // ── Send product to staff ──
    if ($action === 'send_to_staff') {
        // Only owner and supervisor can send products
        if (!in_array($userRole, ['owner', 'supervisor'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $productId = (int)($body['product_id'] ?? 0);
        $toUserId = (int)($body['to_user_id'] ?? 0);
        $quantity = (int)($body['quantity'] ?? 1);
        $notes = trim($body['notes'] ?? '');
        
        if (!$productId || !$toUserId || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Product ID, recipient, and quantity required.']);
            exit;
        }
        
        // Verify product exists and user owns it
        $stmt = $pdo->prepare("
            SELECT sp.id, sp.qty, sp.current_holder_id, sp.holder_type
            FROM supplier_products sp
            WHERE sp.id = ? AND sp.current_holder_id = ?
        ");
        $stmt->execute([$productId, $userId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            // Debug: Check if product exists at all
            $stmt = $pdo->prepare("SELECT id, current_holder_id, holder_type FROM supplier_products WHERE id = ?");
            $stmt->execute([$productId]);
            $productDebug = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $debugMsg = $productDebug 
                ? "Product exists but holder mismatch. Product holder: {$productDebug['current_holder_id']}, Your ID: {$userId}"
                : "Product ID {$productId} not found in database";
            
            echo json_encode(['success' => false, 'message' => 'Product not found or you do not own it.', 'debug' => $debugMsg]);
            exit;
        }
        
        if ($product['qty'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Insufficient quantity available.']);
            exit;
        }
        
        // Verify recipient exists and has correct role
        $stmt = $pdo->prepare("SELECT id, role, first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$toUserId]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$recipient) {
            echo json_encode(['success' => false, 'message' => 'Recipient not found.']);
            exit;
        }
        
        // Determine transfer type and validate
        $transferType = '';
        if ($userRole === 'owner' && $recipient['role'] === 'supervisor') {
            $transferType = 'owner_to_supervisor';
        } else if ($userRole === 'supervisor' && $recipient['role'] === 'sales_person') {
            $transferType = 'supervisor_to_sales';
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid transfer: ' . $userRole . ' cannot send to ' . $recipient['role'] . '.']);
            exit;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // For owner → supervisor transfers, auto-accept and handle quantity-based transfer
            if ($transferType === 'owner_to_supervisor') {
                // Reduce quantity from owner's product
                $stmt = $pdo->prepare("
                    UPDATE supplier_products
                    SET qty = qty - ?
                    WHERE id = ? AND current_holder_id = ?
                ");
                $stmt->execute([$quantity, $productId, $userId]);
                
                // Check if supervisor already has this product
                $stmt = $pdo->prepare("
                    SELECT id, qty FROM supplier_products
                    WHERE category = (SELECT category FROM supplier_products WHERE id = ?)
                      AND brand = (SELECT brand FROM supplier_products WHERE id = ?)
                      AND item_description = (SELECT item_description FROM supplier_products WHERE id = ?)
                      AND current_holder_id = ?
                      AND holder_type = 'supervisor'
                    LIMIT 1
                ");
                $stmt->execute([$productId, $productId, $productId, $toUserId]);
                $supervisorProduct = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($supervisorProduct) {
                    // Supervisor already has this product - increase quantity
                    $stmt = $pdo->prepare("
                        UPDATE supplier_products
                        SET qty = qty + ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantity, $supervisorProduct['id']]);
                    $supervisorProductId = $supervisorProduct['id'];
                } else {
                    // Create new product entry for supervisor
                    $stmt = $pdo->prepare("
                        INSERT INTO supplier_products 
                        (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes, current_holder_id, holder_type)
                        SELECT supplier_id, category, brand, item_description, ?, srp, image_path, 'verified', 
                               CONCAT(COALESCE(notes, ''), ' [Received from owner]'), ?, 'supervisor'
                        FROM supplier_products
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantity, $toUserId, $productId]);
                    $supervisorProductId = $pdo->lastInsertId();
                }
                
                // Create transfer record as 'accepted'
                $stmt = $pdo->prepare("
                    INSERT INTO product_transfers 
                    (product_id, from_user_id, to_user_id, transfer_type, quantity, status, notes, responded_at)
                    VALUES (?, ?, ?, ?, ?, 'accepted', ?, NOW())
                ");
                $stmt->execute([$productId, $userId, $toUserId, $transferType, $quantity, $notes]);
                $transferId = $pdo->lastInsertId();
                
                // Add to transfer history
                $stmt = $pdo->prepare("
                    INSERT INTO product_transfer_history
                    (product_id, from_user_id, to_user_id, action, quantity, notes)
                    VALUES (?, ?, ?, 'sent_to_supervisor', ?, ?)
                ");
                $stmt->execute([$productId, $userId, $toUserId, $quantity, $notes]);
                
                
            } else {
                // For supervisor → sales person, also auto-accept and handle quantity-based transfer
                // Reduce quantity from supervisor's product
                $stmt = $pdo->prepare("
                    UPDATE supplier_products
                    SET qty = qty - ?
                    WHERE id = ? AND current_holder_id = ?
                ");
                $stmt->execute([$quantity, $productId, $userId]);
                
                // Check if sales person already has this product
                $stmt = $pdo->prepare("
                    SELECT id, qty FROM supplier_products
                    WHERE category = (SELECT category FROM supplier_products WHERE id = ?)
                      AND brand = (SELECT brand FROM supplier_products WHERE id = ?)
                      AND item_description = (SELECT item_description FROM supplier_products WHERE id = ?)
                      AND current_holder_id = ?
                      AND holder_type = 'sales_person'
                    LIMIT 1
                ");
                $stmt->execute([$productId, $productId, $productId, $toUserId]);
                $salesPersonProduct = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($salesPersonProduct) {
                    // Sales person already has this product - increase quantity
                    $stmt = $pdo->prepare("
                        UPDATE supplier_products
                        SET qty = qty + ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantity, $salesPersonProduct['id']]);
                    $salesPersonProductId = $salesPersonProduct['id'];
                } else {
                    // Create new product entry for sales person
                    $stmt = $pdo->prepare("
                        INSERT INTO supplier_products 
                        (supplier_id, category, brand, item_description, qty, srp, image_path, status, notes, current_holder_id, holder_type)
                        SELECT supplier_id, category, brand, item_description, ?, srp, image_path, 'verified', 
                               CONCAT(COALESCE(notes, ''), ' [Received from supervisor]'), ?, 'sales_person'
                        FROM supplier_products
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantity, $toUserId, $productId]);
                    $salesPersonProductId = $pdo->lastInsertId();
                }
                
                // Create transfer record as 'accepted'
                $stmt = $pdo->prepare("
                    INSERT INTO product_transfers 
                    (product_id, from_user_id, to_user_id, transfer_type, quantity, status, notes, responded_at)
                    VALUES (?, ?, ?, ?, ?, 'accepted', ?, NOW())
                ");
                $stmt->execute([$productId, $userId, $toUserId, $transferType, $quantity, $notes]);
                $transferId = $pdo->lastInsertId();
                
                // Add to transfer history
                $stmt = $pdo->prepare("
                    INSERT INTO product_transfer_history
                    (product_id, from_user_id, to_user_id, action, quantity, notes)
                    VALUES (?, ?, ?, 'sent_to_sales', ?, ?)
                ");
                $stmt->execute([$productId, $userId, $toUserId, $quantity, $notes]);
            }
            
            // Send notification to recipient (optional - won't fail if notification system is unavailable)
            try {
                if (file_exists(__DIR__ . '/notification_helper.php')) {
                    require_once __DIR__ . '/notification_helper.php';
                    $senderName = ($_SESSION['first_name'] ?? 'User') . ' ' . ($_SESSION['last_name'] ?? '');
                    $notifTitle = 'Product Transfer Received';
                    $notifBody = "{$senderName} has sent you {$quantity} unit(s) of a product. Please review and accept or reject the transfer.";
                    sendNotification($toUserId, 'system', $notifTitle, $notifBody);
                }
            } catch (Exception $notifError) {
                // Notification failed but transfer succeeded - log and continue
                error_log("Notification failed: " . $notifError->getMessage());
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Product sent successfully to ' . $recipient['first_name'] . ' ' . $recipient['last_name'] . '.',
                'transfer_id' => $transferId
            ]);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Product transfer failed: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // ── Accept transfer ──
    if ($action === 'accept_transfer') {
        // Only supervisor and sales_person can accept transfers
        if (!in_array($userRole, ['supervisor', 'sales_person'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $transferId = (int)($body['transfer_id'] ?? 0);
        
        if (!$transferId) {
            echo json_encode(['success' => false, 'message' => 'Transfer ID required.']);
            exit;
        }
        
        // Get transfer details
        $stmt = $pdo->prepare("
            SELECT pt.*, sp.qty AS product_qty, sp.current_holder_id
            FROM product_transfers pt
            JOIN supplier_products sp ON sp.id = pt.product_id
            WHERE pt.id = ? AND pt.to_user_id = ? AND pt.status = 'pending'
        ");
        $stmt->execute([$transferId, $userId]);
        $transfer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transfer) {
            echo json_encode(['success' => false, 'message' => 'Transfer not found or already processed.']);
            exit;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // Update transfer status
            $stmt = $pdo->prepare("
                UPDATE product_transfers 
                SET status = 'accepted', responded_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$transferId]);
            
            // Update product holder
            $holderType = $userRole === 'supervisor' ? 'supervisor' : 'sales_person';
            $stmt = $pdo->prepare("
                UPDATE supplier_products
                SET current_holder_id = ?, holder_type = ?
                WHERE id = ?
            ");
            $stmt->execute([$userId, $holderType, $transfer['product_id']]);
            
            // Add to transfer history
            $stmt = $pdo->prepare("
                INSERT INTO product_transfer_history
                (product_id, from_user_id, to_user_id, action, quantity, notes)
                VALUES (?, ?, ?, 'accepted', ?, 'Transfer accepted')
            ");
            $stmt->execute([$transfer['product_id'], $transfer['from_user_id'], $userId, $transfer['quantity']]);
            
            // Notify sender
            require_once __DIR__ . '/notification_helper.php';
            $recipientName = ($_SESSION['first_name'] ?? 'User') . ' ' . ($_SESSION['last_name'] ?? '');
            $notifTitle = 'Transfer Accepted';
            $notifBody = "{$recipientName} has accepted the product transfer.";
            sendNotification($transfer['from_user_id'], 'system', $notifTitle, $notifBody);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Transfer accepted successfully.'
            ]);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Accept failed: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // ── Reject transfer ──
    if ($action === 'reject_transfer') {
        // Only supervisor and sales_person can reject transfers
        if (!in_array($userRole, ['supervisor', 'sales_person'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        
        $transferId = (int)($body['transfer_id'] ?? 0);
        $reason = trim($body['reason'] ?? '');
        
        if (!$transferId) {
            echo json_encode(['success' => false, 'message' => 'Transfer ID required.']);
            exit;
        }
        
        // Get transfer details
        $stmt = $pdo->prepare("
            SELECT pt.*
            FROM product_transfers pt
            WHERE pt.id = ? AND pt.to_user_id = ? AND pt.status = 'pending'
        ");
        $stmt->execute([$transferId, $userId]);
        $transfer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transfer) {
            echo json_encode(['success' => false, 'message' => 'Transfer not found or already processed.']);
            exit;
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // Update transfer status
            $stmt = $pdo->prepare("
                UPDATE product_transfers 
                SET status = 'rejected', responded_at = NOW(), notes = CONCAT(COALESCE(notes, ''), '\nRejection reason: ', ?)
                WHERE id = ?
            ");
            $stmt->execute([$reason ?: 'No reason provided', $transferId]);
            
            // Add to transfer history
            $stmt = $pdo->prepare("
                INSERT INTO product_transfer_history
                (product_id, from_user_id, to_user_id, action, quantity, notes)
                VALUES (?, ?, ?, 'rejected', ?, ?)
            ");
            $stmt->execute([$transfer['product_id'], $transfer['from_user_id'], $userId, $transfer['quantity'], $reason ?: 'No reason provided']);
            
            // Notify sender
            require_once __DIR__ . '/notification_helper.php';
            $recipientName = ($_SESSION['first_name'] ?? 'User') . ' ' . ($_SESSION['last_name'] ?? '');
            $notifTitle = 'Transfer Rejected';
            $notifBody = "{$recipientName} has rejected the product transfer." . ($reason ? " Reason: {$reason}" : '');
            sendNotification($transfer['from_user_id'], 'system', $notifTitle, $notifBody);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Transfer rejected successfully.'
            ]);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Reject failed: ' . $e->getMessage()]);
            exit;
        }
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
